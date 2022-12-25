<?php
session_start();

include_once "Errorhandler.php";
include_once "DbInfo.php";
set_error_handler("handleErrors");

/*
$input = 5;
if($input <= 10)
{
    trigger_error(" random test error message", E_USER_WARNING);
}*/

$link = mysqli_connect($host, $user, $password) or die("Error: no connection can be made to the host");
//open database
mysqli_select_db($link, $database) or die("Error: the database could not be opened");

if (isset($_GET['action']) && $_GET['action'] == "promote") {
    $mysqli = new mysqli($host, $user, $password, $database);
    $query = "SELECT `UserNumber`, `rechten` FROM `users` WHERE  `userNumber` = ?";

    $stmt = $mysqli->prepare($query);

    $userNumber = (int)htmlspecialchars($_GET['index']);

    $stmt->bind_param("i", $userNumber);

    $stmt->execute();
    $stmt->bind_result($UserNumberResult, $rechtenResult);
    $stmt->store_result();
    $stmt->fetch();

    if ($rechtenResult == "user") {
        //promote
        $query = "UPDATE `users` SET `rechten`= ? WHERE `userNumber` = ?";
        $stmt = $mysqli->prepare($query);

        $rechten = htmlspecialchars("admin");
        $userNumber = htmlspecialchars($UserNumberResult);
        $stmt->bind_param("si", $rechten, $userNumber);

        $stmt->execute();
        $mysqli->close();
    } else {
        //demote
        $query = "UPDATE `users` SET `rechten`= ? WHERE `userNumber` = ?";
        $stmt = $mysqli->prepare($query);

        $rechten = htmlspecialchars("user");
        $userNumber = htmlspecialchars($UserNumberResult);
        $stmt->bind_param("si", $rechten, $userNumber);

        $stmt->execute();
        $mysqli->close();
    }
}

if (isset($_GET['action']) && $_GET['action'] == "activate") {
    $mysqli = new mysqli($host, $user, $password, $database);
    //haal usernumber van een andere persoon
    $query = "SELECT `UserNumber`, `Actief` FROM `users` WHERE  `userNumber` = ?";

    $stmt = $mysqli->prepare($query);

    $userNumber = (int)htmlspecialchars($_GET['index']);

    $stmt->bind_param("i", $userNumber);

    $stmt->execute();
    $stmt->bind_result($UserNumberResult, $ActiefResult);
    $stmt->store_result();
    $stmt->fetch();

    if ($ActiefResult == 1) {
        //set nonactief
        $query = "UPDATE `users` SET `Actief`= ? WHERE `userNumber` = ?";
        $stmt = $mysqli->prepare($query);

        $actief = htmlspecialchars(0);
        $userNumber = htmlspecialchars($UserNumberResult);
        $stmt->bind_param("ii", $actief, $userNumber);

        $stmt->execute();
        $mysqli->close();
    } else {
        //set actief
        $query = "UPDATE `users` SET `Actief`= ? WHERE `userNumber` = ?";
        $stmt = $mysqli->prepare($query);

        $actief = htmlspecialchars(1);
        $userNumber = htmlspecialchars($UserNumberResult);
        $stmt->bind_param("ii", $actief, $userNumber);

        $stmt->execute();
        $mysqli->close();
    }
}

if (isset($_GET['action']) && $_GET['action'] == "logout") {
    $account = "";
    unset($_SESSION['username']);
    unset($_SESSION['rechten']);
    unset($_SESSION['userNumber']);
    unset($_SESSION['userActief']);
    //session_destroy();
    header("Location: HomePage.php");
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <title>Homepage</title>
    <link rel="stylesheet" href="CSS/reset.css" />
    <link rel="stylesheet" href="CSS/index.css" />
    <link rel="stylesheet" href="CSS/LoginPage.css" />

    <!-- TO DO
        - nakijken product actief of ni ook voo user            done
        - JS bug                                                done
        - prepares querries PHP folder                          done
        - AJAX verfijnen op groepen dropdown ding               done
        - betaald in db steken                                  done
        - admin rechten aanspassen                              done
        - delete ni doen alleen non actief                      done
        - errorhandling                                         done
        - images beter doen                                     done
        - sql close nazien                                      pakt oké
        - orders zien                                           done
        - HTML verified                                         done
        - fix modify order                                      done
    -->

</head>

<body onload="startOL()">
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
                    echo "<img class='icon floatL' src='Images/loggedIn.png' alt='AccountImage'>";
                } elseif ($account == "adminLoggedIn") {                                                    //admin login image
                    echo "<img class='icon floatL' src='Images/admin.png' alt='AccountImage'>";
                } else {                                                                                    //logged out image                                                                                      
                    echo "<img class='icon floatL' src='Images/loggedOut.png' alt='AccountImage'>";
                }

                if (isset($_SESSION["username"])) {                                                         //display user name
                    echo "<p class='logout'>" . htmlspecialchars($_SESSION["username"]) . "</p>";
                }
                ?>
            </a>

            <?php
            if (isset($_SESSION["username"])) {
                echo "<a class='ShoppingcartSelector' href='ShoppingCart.php'>";
                echo "<img class='icon' src='Images/ShoppingCart.png' alt='ShoppingCartImage'>";
                echo "</a>";
            }
            if (isset($_SESSION["username"])) {                                                             //log out button show only when logged in
                echo "<a class='logout' href='HomePage.php?action=logout'>Logout</a>";
            } else {
                echo "<p class='logout'>Logged out</p>";
            }
            ?>

        </div>

        <div class="OntvangstBalk rounded center">
            <h1>Snoepwinkel</h1>
        </div>

        <div class="SearchBalk rounded">
            <input class="floatR AJAXSearchBalk" type="text" id="AjaxInputBeschrijving" onkeyup="start();" value="Snoep">

            <select class="floatR AJAXSearchBalk" id="AjaxInputGroup" onchange="start();">
                <option value="NoFilter">No Filter</option>
                <option value="Uniek">Uniek</option>
                <option value="Mix">Mix</option>
            </select>
        </div>

        <?php
        if (isset($_SESSION["userActief"])) {
            if ($_SESSION["userActief"] == 0) {
                $mysqli = new mysqli($host, $user, $password, $database);
                $query = "UPDATE `users` SET `Actief`= ? WHERE `UserNumber` = ?";

                $stmt = $mysqli->prepare($query);

                $userNumber = htmlspecialchars($_SESSION["userNumber"]);
                $Actief = htmlspecialchars(1);
                $stmt->bind_param("ii", $Actief, $userNumber);

                $stmt->execute();
                $mysqli->close();

                echo "<div class='SearchBalk rounded'>";
                echo "<p class='floatL AJAXSearchBalk'>Your account has been reactivated</p>";
                echo "</div>";
                $_SESSION["userActief"] = 1;
            }
        }

        //toon producten in webshop (AJAX)
        ?>
        <div id="output"></div>
        <?php

        //print users als als admin ingelogd
        if ($account == "adminLoggedIn") {
            //create and execute
            $query = "SELECT * FROM users";
            $result = mysqli_query($link, $query) or die("Error: an error has occurred while executing the query.");

            //write result
            $numberRecords = mysqli_num_rows($result);

            echo "<hr>";

            echo "<div class='Product rounded'>";
            echo "<h4>Users</h4>";
            echo "<table>";
            echo "<tr>";
            echo "<td>UserID</td>";
            echo "<td>Voornaam</td>";
            echo "<td>Achternaam</td>";
            echo "<td>Adres</td>";
            echo "<td>Rechten</td>";
            echo "<td>Actief</td>";
            echo "</tr>";

            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['UserNumber'] . "</td>";
                echo "<td>" . $row['voornaam'] . "</td>";
                echo "<td>" . $row['achternaam'] . "</td>";
                echo "<td>" . $row['adres'] . "</td>";
                echo "<td>" . $row['rechten'] . " : " . "<a href=\"HomePage.php?index=" . $row['UserNumber'] . "&action=promote\"> click </a></td>";
                echo "<td>" . $row['Actief'] . " : " . "<a href=\"HomePage.php?index=" . $row['UserNumber'] . "&action=activate\"> click </a></td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";

            echo "<hr>";

            //print current orders
            $query = "SELECT * FROM orders";
            $result = mysqli_query($link, $query) or die("Error: an error has occurred while executing the query.");
            $numberRecords = mysqli_num_rows($result);

            echo "<div class='Product rounded'>";
            echo "<h4>Orders</h4>";
            echo "<table>";
            echo "<tr>";
            echo "<td>OrderID</td>";
            echo "<td>UserNumber</td>";
            echo "<td>Datum</td>";
            echo "<td>Betaald</td>";
            echo "<td>totaalprijs</td>";
            echo "</tr>";

            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<td>" . $row['OrderID'] . "</td>";
                echo "<td>" . $row['userNumber'] . "</td>";
                echo "<td>" . $row['Datum'] . "</td>";
                if ($row['Betaald'] == 1) {
                    $row['Betaald'] = "Ja";
                } else {
                    $row['Betaald'] = "Nee";
                }
                echo "<td>" . $row['Betaald'] . "</td>";
                echo "<td>" . $row['totaalPrijs'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
            echo "<hr>";

            //print current products
            $query = "SELECT * FROM products";
            $result = mysqli_query($link, $query) or die("Error: an error has occurred while executing the query.");
            $numberRecords = mysqli_num_rows($result);

            echo "<div class='Product rounded'>";
            echo "<h4>Products</h4>";
            echo "<table>";
            echo "<tr>";
            echo "<td class='smallTD' >ProductID</td>";
            echo "<td class='smallTD' id='prodModBe'>Beschrijving</td>";
            echo "<td class='smallTD' id='prodModAc'>Actief</td>";
            echo "<td class='smallTD' id='prodModIm'>Image</td>";
            echo "<td class='smallTD' id='prodModIt'>ItemGroup</td>";
            echo "<td class='smallTD' id='prodModPr'>Prijs</td>";
            echo "<td></td>";
            echo "</tr>";
            
            while ($row = mysqli_fetch_array($result)) {
                echo "<tr>";
                echo "<form name='MakeAccount' method='POST' onsubmit='return checkIfEmptyModify()' action='ModifyProduct.php'>";
                echo "<td class='smallTD'>" . $row['ProductID']  . "</td>";
                echo "<input type='hidden' id='ProductIDMod' name='ProductIDMod' value='". $row['ProductID'] ."'>";
                echo "<td class='smallTD'><input class='smallTD' type='text' id='BeschrijvingMod' name='BeschrijvingMod' value='". $row['Beschrijving'] ."'></td>";
                echo "<td class='smallTD'><input class='smallTD' type='text' id='ActiefMod' name='ActiefMod' value='". $row['Actief'] ."'></td>";
                echo "<td class='smallTD'><input class='smallTD' type='text' id='imageMod' name='imageMod' value='". $row['Image'] ."'></td>";
                echo "<td class='smallTD'><input class='smallTD' type='text' id='itemGroupMod' name='itemGroupMod' value='". $row['itemGroup'] ."'></td>";
                echo "<td class='smallTD'><input class='smallTD' type='text' id='prijsMod' name='prijsMod' value='". $row['prijs'] ."'></td>";
                echo "<td class='smallTD'><input class='smallTD' type='submit' value='Edit' name='Edit'></td>";
                echo "</form>";
                echo "</tr>";
            }
            echo "</table>";
            echo "</div>";
            echo "<hr>";
        ?>

            <div class="after">
                <!--User aanmaken-->
                <div class="floatR adminAddToDB marginBottom">
                    <form name="MakeAccount" method="POST" onsubmit="return checkIfEmpty()" action="MakeAccount.php">
                        <h3>User Aanmaken</h3>

                        <p id="usernameTitle">Username:</p>
                        <input type="text" id="username" name="username">

                        <p id="achternaamTitle">achternaam:</p>
                        <input type="text" id="achternaam" name="achternaam">

                        <p id="rechtenTitle">rechten (user/admin):</p>
                        <input type="text" id="rechten" name="rechten">

                        <p id="passwordTitle">Password:</p>
                        <input type="password" id="password" name="password">

                        <p id="adresTitle">adres:</p>
                        <input type="text" id="adres" name="adres">

                        <input type="submit" value="Registreren" name="login">
                    </form>
                </div>

                <!--Product aanmaken-->
                <div class="floatL adminAddToDB marginBottom">
                    <form name="MakeProduct" method="POST" onsubmit="return checkIfEmptyProduct()" action="MakeProduct.php">
                        <!--onsubmit="return checkIfEmpty()"-->
                        <h3>Product Aanmaken</h3>

                        <p id="productNametitle">Naam:</p>
                        <input type="text" id="productName" name="productName">

                        <p id="productActiefTitle">Actief (1/0):</p>
                        <input type="text" id="productActief" name="productActief" value="0/1">

                        <p id="ImageTitle">Image (/images/...):</p>
                        <input type="text" id="Image" name="Image" value="Images/Ballen/x.png">
                        <!--<input type="image" id="Image" name="Image">-->

                        <p id="groupTitle">Groep (Uniek/Mix):</p>
                        <input type="text" id="group" name="group" value="Uniek/Mix">

                        <p id="prijsTitle">Prijs:</p>
                        <input type="text" id="prijs" name="prijs" value="">

                        <input type="submit" value="Aanmaken" name="ProductAanmaken">
                    </form>
                </div>
            </div>

        <?php
        }
        //close link
        mysqli_close($link);
        ?>
    </div>
</body>

<script>
    //AJAX
    function start() {
        xhr = new XMLHttpRequest();
        if (xhr != null) {
            var searchString = document.getElementById("AjaxInputBeschrijving").value;
            var searchString2 = document.getElementById("AjaxInputGroup").value;

            var url = "AJAX.php?search1=" + searchString + "&search2=" + searchString2;

            xhr.onreadystatechange = showResult;

            xhr.open("GET", url, true);
            xhr.send(null);
        }
    }

    function startOL() {
        xhr = new XMLHttpRequest();
        if (xhr != null) {
            var searchString = document.getElementById("AjaxInputBeschrijving").value;
            var searchString2 = document.getElementById("AjaxInputGroup").value;

            var url = "AJAX.php?search1=" + searchString + "&search2=" + searchString2;

            xhr.onreadystatechange = showResult;

            xhr.open("GET", url, true);
            xhr.send(null);
        }
    }

    function showResult() {
        var output = document.getElementById("output");
        if (xhr.readyState == 4 && xhr.status == 200) {
            if (xhr.responseText) {
                output.innerHTML = xhr.responseText;
            } else {
                output.innerHTML = " Search Field Empty :'-( ";
            }
        } else {
            output.innerHTML = " Searching . . . ";
        }
    }

    function checkIfEmptyModify() {
        var BeschrijvingMod = document.getElementById("BeschrijvingMod");
        var ActiefMod = document.getElementById("ActiefMod");
        var imageMod = document.getElementById("imageMod");
        var itemGroupMod = document.getElementById("itemGroupMod");
        var prijsMod = document.getElementById("prijsMod");

        if (BeschrijvingMod.value == "") {
            document.getElementById("prodModBe").style.color = "red";
            return false;
        } else {
            document.getElementById("prodModBe").style.color = "black";
        }

        if (ActiefMod.value == "") {
            document.getElementById("prodModAc").style.color = "red";
            return false;
        } else {
            document.getElementById("prodModAc").style.color = "black";
        }

        if (imageMod.value == "") {
            document.getElementById("prodModIm").style.color = "red";
            return false;
        } else {
            document.getElementById("prodModIm").style.color = "black";
        }

        if (itemGroupMod.value == "") {
            document.getElementById("prodModIt").style.color = "red";
            return false;
        } else {
            document.getElementById("prodModIt").style.color = "black";
        }

        if (prijsMod.value == "") {
            document.getElementById("prodModPr").style.color = "red";
            return false;
        } else {
            document.getElementById("prodModPr").style.color = "black";
        }
    }

    //JS
    function checkIfEmpty() {
        var inputUsername = document.getElementById("username");
        var inputAchternaam = document.getElementById("achternaam");
        var inputRechten = document.getElementById("rechten");
        var inputPassword = document.getElementById("password");
        var inputAdres = document.getElementById("adres");


        if (inputUsername.value == "") {
            document.getElementById("usernameTitle").style.color = "red";
            return false;
        } else {
            document.getElementById("usernameTitle").style.color = "black";
        }

        if (inputAchternaam.value == "") {
            document.getElementById("achternaamTitle").style.color = "red";
            return false;
        } else {
            document.getElementById("achternaamTitle").style.color = "black";
        }

        if (inputRechten.value == "") {
            document.getElementById("rechtenTitle").style.color = "red";
            return false;
        } else if (inputRechten.value != "user" && inputRechten.value != "admin") {
            document.getElementById("rechtenTitle").style.color = "purple";
            return false;
        } else {
            document.getElementById("rechtenTitle").style.color = "black";
        }

        if (inputPassword.value == "") {
            document.getElementById("passwordTitle").style.color = "red";
            return false;
        } else {
            document.getElementById("passwordTitle").style.color = "black";
        }

        if (inputAdres.value == "") {
            document.getElementById("adresTitle").style.color = "red";
            return false;
        } else {
            document.getElementById("adresTitle").style.color = "black";
        }
    }

    function checkIfEmptyProduct() {
        var productName = document.getElementById("productName");
        var productActief = document.getElementById("productActief");
        var Image = document.getElementById("Image");
        var group = document.getElementById("group");
        var prijs = document.getElementById("prijs");


        if (prijs.value == "") {
            document.getElementById("prijsTitle").style.color = "red";
            return false;
        } else {
            document.getElementById("prijsTitle").style.color = "black";
        }

        if (productName.value == "") {
            document.getElementById("productNametitle").style.color = "red";
            return false;
        } else {
            document.getElementById("productNametitle").style.color = "black";
        }

        if (productActief.value == "") {
            document.getElementById("productActiefTitle").style.color = "red";
            return false;
        } else if (productActief.value != "1" && productActief.value != "0") {
            document.getElementById("productActiefTitle").style.color = "purple";
            return false;
        } else {
            document.getElementById("productActiefTitle").style.color = "black";
        }

        if (Image.value == "") {
            document.getElementById("ImageTitle").style.color = "red";
            return false;
        } else {
            document.getElementById("ImageTitle").style.color = "black";
        }

        if (group.value == "") {
            document.getElementById("groupTitle").style.color = "red";
            return false;
        } else if (group.value != "Uniek" && group.value != "Mix") {
            document.getElementById("groupTitle").style.color = "yellow";
            return false;
        } else {
            document.getElementById("groupTitle").style.color = "black";
        }
    }
</script>

</html>