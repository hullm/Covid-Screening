<?php

// Send error information to the browser
error_reporting(E_ALL);
ini_set('display_errors', 'on');
$configFile="../config.ini";

// Read in the config file
$config = parse_ini_file($configFile);

// Start a new session if it doesn't exist
if(!isset($_SESSION)) {
    session_start();
}

// If the user is already signed in on the login page send them to the index, 
// and if they aren't signed in send them to the login screen.
if(isset($_SESSION["loggedIn"]) && $_SESSION["loggedIn"] == TRUE) {
    if (isset($_GET['login'])) {
        header("location:index.php");
        die;
    }
} else {

    if (!isset($_GET['login'])) {
        header("location:index.php?login");
        die; 
    }
}

// Grab the current user from the session
if(isset($_SESSION["userName"])) {
    $currentUser = $_SESSION["userName"];
}
?>