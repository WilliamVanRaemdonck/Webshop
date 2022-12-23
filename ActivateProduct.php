<?php
include_once "DbInfo.php";
if (isset($_GET['action']) && $_GET['action'] == "activateProduct") {
    $ProductID = htmlspecialchars($_GET['index']);

    // get current state 
    $mysqli = new mysqli($host, $user, $password, $database);
    $query = "SELECT `Aktief` FROM `products` WHERE `ProductID` = ?";

    $stmt = $mysqli->prepare($query);

    $userNumber = (int)htmlspecialchars($ProductID);

    $stmt->bind_param("i", $userNumber);

    $stmt->execute();
    $stmt->bind_result($AktiefResult);
    $stmt->store_result();
    $stmt->fetch();

    if($AktiefResult == 1){
        $AktiefResult = 0;
    }
    else{
        $AktiefResult = 1;
    }

    // update state
    $mysqli = new mysqli($host, $user, $password, $database);
    $query = "UPDATE `products` SET `Aktief`= ? WHERE `ProductID` = ?;";

    $stmt = $mysqli->prepare($query);
    $ProductID = htmlspecialchars($_GET['index']);
    
    $stmt->bind_param("ii", $AktiefResult, $ProductID);

    $stmt->execute();
    $mysqli->close();

    header("Location: /Labos/Webshop/HomePage.php");
}
