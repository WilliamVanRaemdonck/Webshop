<?php

include_once "Errorhandler.php";
set_error_handler("handleErrors");

if (!isset($_POST['username']) || !isset($_POST['password'])) {
	header("Location: LoginPage.html");
} elseif (empty($_POST['username']) || empty($_POST['password'])) {
	header("Location: LoginPage.html");
} else {
	//convert field values to simple variables and use htmlspecialchars to clean user input	
	$user = htmlspecialchars($_POST['username']);
	$pass = htmlspecialchars($_POST['password']);

	$result = search_user($user, $pass);

	//$result = 2;

	try {
		if ($result == 1) {
			header("Location: HomePage.php");
		}
		elseif($result == 0) {	
			header("Location: LoginPage.html");
		}
		else{
			throw new Exception("user login failed, result value =  " . $result);
		}
	} catch (Exception $e) {
		$log = new ErrorLog($e->getCode(), $e->getMessage(), $e->getFile(), $e->getLine());
		$log->WriteError();
		exit("Error: check the logfile for more info.");
	}
}

function search_user($Inputuser, $Inputpass)
{
	include "DbInfo.php";
	$mysqli = new mysqli($host, $user, $password, $database);

	$query = "SELECT `UserNumber`, `voornaam`, `achternaam`, `adres`, `rechten`, `Password`, `Actief` FROM `users` WHERE `voornaam` = ?";

	$stmt = $mysqli->prepare($query);

	/* bind parameters for markers */
	$search = htmlspecialchars($Inputuser);
	//mysqli_stmt_bind_param($stmt,"s",$search);
	$stmt->bind_param("s", $search);

	/* execute query */
	$stmt->execute();

	/* bind result variables */
	$stmt->bind_result($userNumberResult, $voornaamResult, $achternaamResult, $adresResult, $rechtenResult, $passwordResult, $actiefResult);
	$stmt->store_result();

	$stmt->fetch();

	if (password_verify($Inputpass, $passwordResult)) {
		session_start();
		if ($rechtenResult == "admin") {
			$_SESSION['rechten'] = "admin";
			$_SESSION['username'] = $voornaamResult;
			$_SESSION['userNumber'] = $userNumberResult;
			$_SESSION['userActief'] = $actiefResult;
		} else {
			$_SESSION['rechten'] = "user";
			$_SESSION['username'] = $voornaamResult;
			$_SESSION['userNumber'] = $userNumberResult;
			$_SESSION['userActief'] = $actiefResult;
		}
		return 1;
	} else {
		return 0;
	}
}
