<?php
include_once "DbInfo.php";
if (isset($_GET['action']) && $_GET['action'] == "activateProduct") {
    $ProductID = htmlspecialchars($_GET['index']);

    // get current state 
    $mysqli = new mysqli($host, $user, $password, $database);
    $query = "SELECT `Actief` FROM `products` WHERE `ProductID` = ?";

    $stmt = $mysqli->prepare($query);

    $userNumber = (int)htmlspecialchars($ProductID);

    $stmt->bind_param("i", $userNumber);

    $stmt->execute();
    $stmt->bind_result($ActiefResult);
    $stmt->store_result();
    $stmt->fetch();

    if($ActiefResult == 1){
        $ActiefResult = 0;
    }
    else{
        $ActiefResult = 1;
    }

    // update state
    $mysqli = new mysqli($host, $user, $password, $database);
    $query = "UPDATE `products` SET `Actief`= ? WHERE `ProductID` = ?;";

    $stmt = $mysqli->prepare($query);
    $ProductID = htmlspecialchars($_GET['index']);
    
    $stmt->bind_param("ii", $ActiefResult, $ProductID);

    $stmt->execute();
    $mysqli->close();

    header("Location: /Labos/Webshop/HomePage.php");
}
