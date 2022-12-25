<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
include_once "DbInfo.php";

//session_destroy();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>ShoppingCart</title>
    <link rel="stylesheet" href="CSS/reset.css" />
    <link rel="stylesheet" href="CSS/index.css" />
    <link rel="stylesheet" href="CSS/LoginPage.css" />
</head>

<body>
    <div class="Wrapper">
        <div class="NavigatieBalk rounded">
            <a class="AccountSelector" href="LoginPage.html">
                <?php
                $account = "";
                if (isset($_SESSION["username"])) {
                    $name = $_SESSION["username"];          //  vang naam user
                    if ($_SESSION["rechten"] == "admin") {  //  user = admin?
                        $account = "adminLoggedIn";
                    } else {                                //  user = !admin
                        $account = "userLoggedIn";
                    }
                }

                if ($account == "userLoggedIn") {                                                           //user login image
                ?>
                    <img class="icon floatL" src="Images/loggedIn.png" alt="AccountImage">
                <?php
                } elseif ($account == "adminLoggedIn") {                                                    //admin login image
                ?>
                    <img class="icon floatL" src="Images/admin.png" alt="AccountImage">
                <?php
                } else {                                                                                    //logged out image                                                                                      
                ?>
                    <img class="icon floatL" src="Images/loggedOut.png" alt="AccountImage">
                <?php
                }

                if (isset($_SESSION["username"])) {                                                         //display user name
                    echo "<p class='logout'>" . htmlspecialchars($_SESSION["username"]) . "</p>";
                }
                ?>

            </a>
            <a class="ShoppingcartSelector" href="ShoppingCart.php">
                <img class="icon" src="Images/ShoppingCart.png" alt="ShoppingCartImage">
            </a>
            <?php
            if (isset($_SESSION["username"])) {                                                             //log out button show only when logged in
            ?>
                <a class="logout" href="HomePage.php?action=logout">Logout</a>
            <?php
            } else {
            ?>
                <p class="logout">Logged out</p>
            <?php
            }
            ?>

            <a class="logout" href="HomePage.php">HomePage</a>

        </div>

        <?php
        $link = mysqli_connect($host, $user, $password) or die("Error: no connection can be made to the host");

        //open database
        mysqli_select_db($link, $database) or die("Error: the database could not be opened");

        //voor debuggen
        if (isset($_SESSION["username"])) {
            //bestaat er een cart voor een bepaald user nummer
            if (isset($_SESSION["cart"][$_POST["userNumber"]])) {
                //als er geen ding in de cart zit me het juist product ID en voeg die toe
                if (isset($_SESSION["cart"][$_POST["userNumber"]][$_POST["ProductID"]]) != true) {
                    $_SESSION["cart"][$_POST["userNumber"]][$_POST["ProductID"]][0] = $_POST["productBeschrijving"];
                }
                $_SESSION["cart"][$_POST["userNumber"]][$_POST["ProductID"]][1] += $_POST["Amount"];
                unset($_POST);
            } else {
                if (isset($_POST) && empty($_POST) != true) {
                    $_SESSION["cart"][$_POST["userNumber"]][$_POST["ProductID"]] = array($_POST["productBeschrijving"], $_POST["Amount"]);
                }
                unset($_POST);
            }
            //$_SESSION["cart"][$_POST["userNumber"]][$_POST["ProductID"]] = array($_POST["productBeschrijving"],$_POST["Amount"]);
            if (!empty($_SESSION["cart"][$_SESSION["userNumber"]])) {
                //show shoppingcart
                if (isset($_SESSION['cart'])) {
                    echo "<div class='Product rounded'>";
                    echo "<table>";
                    echo "<tr>";
                    echo "<td>Product</td>";
                    echo "<td>Amount</td>";
                    echo "<td>Prijs</td>";
                    echo "<td></td>";
                    echo "</tr>";
                    $totaalPrijs = 0;
                    foreach ($_SESSION['cart'][$_SESSION["userNumber"]] as $index) {
                        echo "<tr>";
                        echo "<td>" . $index[0] . "</td>";
                        echo "<td>" . $index[1] . "</td>";

                        //Product ID vangen 
                        $mysqli = new mysqli($host, $user, $password, $database);
                        $query = "SELECT `ProductID`, `prijs` FROM `products` WHERE `Beschrijving` = ?";
                        $stmt = $mysqli->prepare($query);

                        $beschrijving = htmlspecialchars($index[0]);

                        $stmt->bind_param("s", $beschrijving);

                        $stmt->execute();
                        $stmt->bind_result($ProductID, $prijs);
                        $stmt->store_result();
                        $stmt->fetch();
                        $mysqli->close();

                        $totaalPrijs += ($prijs * $index[1]);

                        echo "<td>" . $prijs . "€ per item</td>";
                        echo "<td><a href=\"ShoppingCart.php?index=" . $ProductID . "&action=removeFromCart\"> Delete Item </a></td>";
                        echo "</tr>";
                    }

                    //show totaalprijs
                    echo "<tr>";
                    echo "<td></td>";
                    echo "<td></td>";
                    echo "<td>" . $totaalPrijs . "</td>";
                    echo "<td></td>";
                    echo "</tr>";

                    echo "</table>";
                    echo "</div>";
                    echo "<td><a href=\"ShoppingCart.php?userNumber=" . $_SESSION["username"] . "&action=AddOrderToDb\"> Add Order </a></td>";
                }
            }
        }


        ?>
    </div>
</body>

<?php
//log out button
if (isset($_GET['action']) && $_GET['action'] == "logout") {
    $account = "";
    unset($_SESSION['username']);
    header("Location: HomePage.php");
}

//remove from cart
if (isset($_GET['action']) && $_GET['action'] == "removeFromCart") {
    unset($_SESSION["cart"][$_SESSION["userNumber"]][$_GET["index"]]);
    unset($_GET['action']);
    unset($_GET['index']);

    header("Location: /Labos/Webshop/ShoppingCart.php");
}

//add order to db
if (isset($_GET['action']) && $_GET['action'] == "AddOrderToDb") {
    $mysqli = new mysqli($host, $user, $password, $database);

    //order aanmaken
    $query = "INSERT INTO `orders`(`UserNumber`, `Datum`, `betaald`, `totaalPrijs`) VALUES (?,?,?,?)";
    $stmt = $mysqli->prepare($query);

    $userNumber = htmlspecialchars($_SESSION["userNumber"]);
    $date = htmlspecialchars(date("Y-m-d"));
    $betaald = htmlspecialchars(1);
    $totaalPrijs = htmlspecialchars($totaalPrijs);

    $stmt->bind_param("isii", $userNumber, $date, $betaald, $totaalPrijs);
    $stmt->execute();

    //store invullen
    foreach ($_SESSION['cart'][$_SESSION["userNumber"]] as $index) {
        //ID vragen van order
        $query = "SELECT MAX(`OrderID`), `UserNumber`, `Datum`, `betaald` FROM `orders` WHERE `UserNumber` = ?";
        $stmt = $mysqli->prepare($query);

        $userNumber = htmlspecialchars($_SESSION["userNumber"]);

        $stmt->bind_param("i", $userNumber);

        $stmt->execute();
        $stmt->bind_result($orderIDResult, $userNumberResult, $dateResult, $betaaldResult);
        $stmt->store_result();
        $stmt->fetch();

        //Product ID vangen 
        $query = "SELECT `ProductID` FROM `products` WHERE `Beschrijving` = ?";
        $stmt = $mysqli->prepare($query);

        $beschrijving = htmlspecialchars($index[0]);

        $stmt->bind_param("s", $beschrijving);

        $stmt->execute();
        $stmt->bind_result($ProductID);
        $stmt->store_result();
        $stmt->fetch();

        //Store aanvullen
        $query = "INSERT INTO `store`(`OrderID`, `ProductID`, `amount`) VALUES (?,?,?)";
        $stmt = $mysqli->prepare($query);

        $orderIDResult = htmlspecialchars($orderIDResult);
        $ProductID = htmlspecialchars($ProductID);
        $amount = $index[1];

        $stmt->bind_param("iii", $orderIDResult, $ProductID, $amount);
        $stmt->execute();
    }

    //items uit shoppingcart verwijderen
    unset($_SESSION['cart']);

    //done!
    header("Location: /Labos/Webshop/HomePage.php");
}
?>

</html>