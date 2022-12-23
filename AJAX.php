<?php
session_start();
include_once "DbInfo.php";

if (isset($_GET['search1']) && isset($_GET['search2']) ) {
    if (!empty($_GET['search1']) && !empty($_GET['search2'])) {
        if($_GET['search2'] == "NoFilter"){
            //toon alles
            $mysqli = new mysqli($host, $user, $password, $database);
            $query = "SELECT * FROM `products` WHERE Beschrijving LIKE ?";
            $stmt = $mysqli->prepare($query);

            $searchB = "%" . htmlspecialchars($_GET['search1']) . "%";
            $stmt->bind_param("s", $searchB);
        }
        else{
            //toon en hou rekening met de groep
            $mysqli = new mysqli($host, $user, $password, $database);
            $query = "SELECT * FROM `products` WHERE Beschrijving LIKE ? && `itemGroup` LIKE ?";
            $stmt = $mysqli->prepare($query);

            $searchB = "%" . htmlspecialchars($_GET['search1']) . "%";
            $searchI = "%" . htmlspecialchars($_GET['search2']) . "%";
            $stmt->bind_param("ss", $searchB, $searchI);
        }

        $stmt->execute();

        $stmt->bind_result($ProductID, $Beschrijving, $aktief, $Image, $itemGroup, $prijs);
        $stmt->store_result();


        if ($stmt->num_rows() == 0) {
            echo "No results found.";
        } else {

            while ($stmt->fetch()) {
                echo "<div class='Product rounded'>";
                echo "<img src='" . htmlspecialchars($Image) . "' alt='tmp' class='floatL, ProductImage'>";
                if($aktief == 1){
                    $msg = "Item is beschikbaar";
                }
                else{
                    $msg = "Item is niet beschikbaar";
                }
                echo "<p class='ProductName, floatL'>" . htmlspecialchars($Beschrijving)." - ".htmlspecialchars($itemGroup)." - ". $msg ."</p>";

                if (isset($_SESSION["rechten"])) {
                    if ($_SESSION["rechten"] == "user" || $_SESSION["rechten"] == "admin") {
?>
                        <!--Form add to shopping cart-->
                        <div class="floatR">
                            <p>
                            <form name="AddToCart" method="POST" action="ShoppingCart.php">
                                Aantal: <input type="text" name="Amount">
                                <input type="hidden" name="userNumber" value="<?php echo htmlspecialchars($_SESSION["userNumber"]) ?>">
                                <input type="hidden" name="productBeschrijving" value="<?php echo htmlspecialchars($Beschrijving) ?>">
                                <input type="hidden" name="ProductID" value="<?php echo htmlspecialchars($ProductID) ?>">
                                <input type="submit" value="Add To Cart" name="addToCart">
                            </form>
                            <?php
                            if ($_SESSION["rechten"] == "admin") {
                                //echo "<a class='center' href=\"PHP\DeleteProduct.php?index=" . htmlspecialchars($ProductID) . "&action=delete\"> ! delete product ! </a>";
                                echo "<a class='center' href=\"ActivateProduct.php?index=" . htmlspecialchars($ProductID) . "&action=activateProduct\"> (de)activate product </a>";
                            }
                            ?>
                            </p>
                        </div>
<?php
                    }
                }
                echo "</div>";
            }
        }
    }
} else {
    header("Location:HomePage.php");
}
?>