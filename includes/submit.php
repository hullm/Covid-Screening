<?php

// Check and see if hit the logout button, if so log them out.
if(isset($_GET['logout'])){
    logout();
}

// Check and see if the user submitted the form on the login page
if (basename($_SERVER['PHP_SELF']) == "login.php") {
    if (isset($_POST["employee_submit"])){
        if (isAuthenticated($_POST["username"], $_POST["password"])) {
            header("location:index.php");
            die;
        }
        else{
            echo "LIAR";
        }
    }

    // Check for visitor sign in
    if (isset($_POST["visitor_submit"])){
        visitorSignIn($_POST["firstname"], $_POST["lastname"], $_POST["email"]);
        header("location:index.php");
        die;
    }
    else{
        echo "";
    }
}

//Check and see if the user submitted the form on the index page
if (basename($_SERVER['PHP_SELF']) == "index.php") {
    if (isset($_POST["submit"])) {

        // Get the values from the form
        $phoneNumber = $_POST["phone_number"];
        $building = $_POST["building"];
        $temperature = $_POST["temperature_radios"];
        $symptoms = $_POST["symptoms_radios"];
        $tested = $_POST["tested_radios"];
        $contact = $_POST["contact_radios"];
        $travel = $_POST["travel_radios"];

        // Determine if the users passed the checks
        if ($temperature <> "no" || $symptoms <> "no" || $tested <> "no" || $contact <> "no" || $travel <> "no"){
            $hasPassed = "FALSE";
        } 
        else {
            $hasPassed = "TRUE";
        }

        // Add the entry to the database
        addEvent($_SESSION["userName"],
            $_SESSION["firstName"],
            $_SESSION["lastName"],
            $_SESSION["email"],
            $phoneNumber,
            $building,
            $_SESSION["userType"], 
            $hasPassed);
    }
}

?>