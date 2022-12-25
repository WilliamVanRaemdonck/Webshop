<?php
include_once "Errorhandler.php";
include_once "DbInfo.php";
set_error_handler("handleErrors");
$link = mysqli_connect($host, $user, $password) or die("Error: no connection can be made to the host");

//open database
mysqli_select_db($link, $database) or die("Error: the database could not be opened");



if (!isset($_POST['productName']) || !isset($_POST['productActief']) || !isset($_POST['Image']) || !isset($_POST['group'])) {
    header("Location: HomePage.php");
} elseif (empty($_POST['productName']) || empty($_POST['productActief']) || empty($_POST['Image']) || empty($_POST['group'])) {
    header("Location: HomePage.php");
} else {

    //convert POST values to simple variables and use htmlspecialchars to clean user input	
    $productNaam = htmlspecialchars($_POST['productName']);
    $productActief = htmlspecialchars($_POST['productActief']);
    $productImage = htmlspecialchars($_POST['Image']);
    $productGroup = htmlspecialchars($_POST['group']);
    $productPrijs = htmlspecialchars($_POST['prijs']);
    
    $result = search_product($productNaam, $link);

    
    if ($result == 1) {
        header("Location: HomePage.php");
    } else{
        //make Product
        $query = "INSERT INTO `products`(`Beschrijving`, `Actief`, `Image`, `itemGroup`, `prijs`) VALUES (?,?,?,?,?)";

        $stmt = mysqli_prepare($link, $query);

        mysqli_stmt_bind_param($stmt, "sissi", $productNaam, $productActief, $productImage, $productGroup, $productPrijs);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        mysqli_close($link);

        header("Location: HomePage.php");
    }
}

function search_product($InputProduct, $link)
{
    $query = "SELECT Beschrijving FROM `products` WHERE `Beschrijving` = '" . $InputProduct . "'";

    $result = mysqli_query($link, $query) or die("Error: an error has occurred while executing the query.");

    $row = mysqli_fetch_array($result);

    if (isset($row["Beschrijving"])) {
        return 1;
    } else {
        return 0;
    }
}
