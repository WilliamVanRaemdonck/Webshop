<?php
include_once "Errorhandler.php";
include_once "DbInfo.php";
set_error_handler("handleErrors");

$link = mysqli_connect($host, $user, $password) or die("Error: no connection can be made to the host");

//open database
mysqli_select_db($link, $database) or die("Error: the database could not be opened");

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    header("Location: LoginPage.html");
} elseif (empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: LoginPage.html");
} else {
    //convert field values to simple variables and use htmlspecialchars to clean user input	
    $user = htmlspecialchars($_POST['username']);
    $achternaam = htmlspecialchars($_POST['achternaam']);
    $pass = password_hash(htmlspecialchars($_POST['password']), PASSWORD_DEFAULT);
    $adres = htmlspecialchars($_POST['adres']);
    $rechten = htmlspecialchars($_POST['rechten']);
    $actief = 1;

    $result = search_user($user, $link);

    if ($result == 1) {
        session_start();
        //zie of admin ingelogd is anders naar homepage redirecten
        if ($_SESSION["rechten"] == "admin") {
            header("Location: HomePage.php");
        } else {
            header("Location: LoginPage.html");
        }
    } else {
        //make account
        //default rechten geven als er geen zijn meegegeven
        if ($rechten == "") {
            $rechten = "user";
        }
        $query =  "INSERT INTO `users`(`voornaam`, `achternaam`, `adres`, `rechten`, `Password`, `Actief`) VALUES (?,?,?,?,?,?)";
        $stmt = mysqli_prepare($link, $query);

        mysqli_stmt_bind_param($stmt, "sssssi", $user, $achternaam, $adres, $rechten, $pass, $actief);

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);
        mysqli_close($link);

        session_start();
        //zie of admin ingelogd is anders naar homepage redirecten
        if ($_SESSION["rechten"] == "admin") {
            header("Location: HomePage.php");
        } else {
            header("Location: LoginPage.html");
        }
    }
}

function search_user($Inputuser, $link)
{
    //nog is navrage ma moe ni prepared zijn denkk
    $query = "SELECT voornaam FROM `users` WHERE `voornaam` = '" . $Inputuser . "'";

    $result = mysqli_query($link, $query) or die("Error: an error has occurred while executing the query.");
    $row = mysqli_fetch_array($result);

    if (isset($row["voornaam"])) {
        return 1;
    } else {
        return 0;
    }
}
