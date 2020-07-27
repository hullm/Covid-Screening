<?php

// Check and see if hit the logout button, if so log them out.
if(isset($_GET['logout'])){
    logout();
}

// Check and see if the user submitted the form on the login page
if (basename($_SERVER['PHP_SELF']) == "login.php") {
    
    // If they provided the right username and password then send them to the form, if not show access denied message
    if (isset($_POST["employee_submit"])){
        if (isAuthenticated($_POST["username"], $_POST["password"])) {
            header("location:index.php");
            die;
        }
        else{
            ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;" id="failedLogin">
                <strong>Incorrect Username/Password!</strong> Please try again.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <script>
                function failedLogin(){
                    document.getElementById('failedLogin').style.display='block';
                }
                failedLogin();
            </script>
            <?php
        }
    }

    // Check for visitor sign in
    if (isset($_POST["visitor_submit"])){

        // Check for a reCAPTCH posting
        if (isset($_POST['recaptcha_response'])) {

            // Build POST request for the reCAPTCHA
            $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
            $recaptcha_secret = $config['secretkey'];
            $recaptcha_response = $_POST['recaptcha_response'];

            // Make and decode POST request
            $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
            $recaptcha = json_decode($recaptcha);

            // Take action based on the score returned if it was ok sign them in as a guest and send them to the form
            if (@$recaptcha->score >= $config['score']) {
                visitorSignIn($_POST["firstname"], $_POST["lastname"], $_POST["email"]);
                header("location:index.php");
                die;
            } else {
                ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;" id="failedRecaptcha">
                    <strong>Failed ReCAPTCHA!</strong> Ewww, bots have cooties.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <script>
                    function failedLogin(){
                        document.getElementById('failedRecaptcha').style.display='block';
                    }
                    failedLogin();
                </script>
                <?php
            }
        } 
        // reCAPTCHA isn't enabled so just sign them in and send them to the form
        else {
            visitorSignIn($_POST["firstname"], $_POST["lastname"], $_POST["email"]);
            header("location:index.php");
            die;
        }
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
            
            // Send email if the user fails the checks.
            if($config['host']!=""){
                entryDeniedEmail($_SESSION['firstName'], $_SESSION['lastName'],$building);
            }
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

// Check if the user submitted the form on the reports page 
if (basename($_SERVER['PHP_SELF']) == "reports.php"){
    if (isset($_POST["submit"])){

        // Get the values from the form
        $fromDate = $_POST["from_date"];
        $toDate = $_POST["to_date"];
        $userType = $_POST["user_type"];
        $building = $_POST["building"];
        $passed = $_POST["passed"];

        // Query the database
        $results = getReportResults($fromDate, $toDate, $userType, $building, $passed);

        // Set the default values for the form elements
        $fromDateValue = $fromDate;
        $toDateValue = $toDate;
        $userTypeAllSelected = "";
        $employeeSelected = "";
        $studentSelected = "";
        $visitorSelected = "";
        $buildingValue = "";
        $buildingAllSelected = "";
        $resultsAllSelected = "";
        $resultsPassedSelected = "";
        $resultsFailedSelected = "";
        
        // Choose which option is enabled in the UserType dropdown
        switch ($userType) {
            case "Employee":
                $employeeSelected = "selected";
                break;
            case "Student":
                $studentSelected = "selected";
                break;
            case "Visitor":
                $visitorSelected = "selected";
                break;
            default:
                $userTypeAllSelected = "selected";
                break;
        }

        // Set the defaults for building
        $buildingValue = $building;
        if ($buildingValue == "All") {
            $buildingAllSelected = "selected";
        }
        else {
            $buildingAllSelected = "";
        }

        // Choose which option is enabled in the Passed dropdown
        $resultsAllSelected = "selected";
        $resultsPassedSelected = "";
        $resultsFailedSelected = "";
        switch ($passed) {
            case "True":
                $resultsPassedSelected = "selected";
                break;
            case "False":
                $resultsFailedSelected = "selected";
                break;
            default:
                $resultsAllSelected = "selected";
                break;
        }
    }
    else {

        // Set the default values for the form elements
        $fromDateValue = date("Y-m-d");
        $toDateValue = date("Y-m-d");
        $userTypeAllSelected = "selected";
        $employeeSelected = "";
        $studentSelected = "";
        $visitorSelected = "";
        $buildingValue = "";
        $buildingAllSelected = "selected";
        $resultsAllSelected = "selected";
        $resultsPassedSelected = "";
        $resultsFailedSelected = "";
    }
}

// Check if the user submitted the form on the missing page 
if (basename($_SERVER['PHP_SELF']) == "missing.php"){
    if (isset($_POST["submit"])){

        // Get the values from the form
        $building = $_POST["building"];

        // Query the database
        $results = getMissingResults($building);

        // Set the default values for the form elements
        $buildingValue = "";
        $buildingAllSelected = "";

        // Set the defaults for building
        $buildingValue = $building;
        if ($buildingValue == "All") {
            $buildingAllSelected = "selected";
        }
        else {
            $buildingAllSelected = "";
        }
    }
    else {

        // Set the default values for the form elements
        $buildingAllSelected = "selected";
    }
}
?>