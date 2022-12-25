<?php
include_once "Errorhandler.php";
include_once "DbInfo.php";
set_error_handler("handleErrors");

$mysqli = new mysqli($host, $user, $password, $database);

$query = "UPDATE `products` SET `Beschrijving`= ?,`Actief`= ?,`Image`= ?,`itemGroup`= ?,`prijs`= ? WHERE `ProductID` = ?";
$stmt = $mysqli->prepare($query);

$ProductID = htmlspecialchars($_POST['ProductIDMod']);
$Beschrijving = htmlspecialchars($_POST['BeschrijvingMod']);
$Actief = htmlspecialchars($_POST['ActiefMod']);
$Image = htmlspecialchars($_POST['imageMod']);
$itemGroup = htmlspecialchars($_POST['itemGroupMod']);
$prijs = htmlspecialchars($_POST['prijsMod']);

$stmt->bind_param("sissii", $Beschrijving, $Actief, $Image, $itemGroup, $prijs, $ProductID);
$stmt->execute();

unset($_POST);

header("Location: HomePage.php");

?>