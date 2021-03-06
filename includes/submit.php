<?php

// Check and see if hit the logout button, if so log them out.
if(isset($_GET['logout'])){
    logout();
}

// Check and see if the user submitted the form on the login page
if (isset($_GET['login'])) {
    
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

//Check and see if the user submitted the screening form
if (isset($_GET['screeningform'])) {
    if (isset($_POST["submit"])) {

        // Get or set data depending on vaccination status
        $vaccinated = $_POST["vaccinated_radios"];
        if ($vaccinated=="yes") {
            $temperature = "no";
            $symptoms = "no";
            $tested = "no";
            $contact = "no";
            $isVaccinated = "TRUE";
        }
        else {
            $temperature = $_POST["temperature_radios"];
            $symptoms = $_POST["symptoms_radios"];
            $tested = $_POST["tested_radios"];
            $contact = $_POST["contact_radios"];
            $isVaccinated = "FALSE";
        }

        // Get the values from the form
        $phoneNumber = $_POST["phone_number"];
        $building = $_POST["building"];
        
        // $travel = $_POST["travel_radios"];
        // Override for a questions that's been removed
        $travel = "no";

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
            $hasPassed,
            $isVaccinated);
    }
}

// Check if the user submitted the form on the reports page 
if (isset($_GET['reports'])){
    if (isset($_POST["submit"]) || isset($_GET['LoadReport'])){

        // Initialize the variables
        $fromDate = "";
        $toDate = "";
        $userType = "";
        $building = "";
        $passed = "";
        $vaccinated = "";
        
        // Get the values from the form
        if (isset($_POST["submit"])) {
            $fromDate = $_POST["from_date"];
            $toDate = $_POST["to_date"];
            $userType = $_POST["user_type"];
            $building = $_POST["building"];
            $passed = $_POST["passed"];
            $vaccinated = $_POST["vaccinated"];

            // Redirect the user to a page with a sharable URL
            header("location:index.php?reports&LoadReport&fromDate=". $fromDate.
                "&toDate=". $toDate.
                "&userType=". $userType. 
                "&building=". $building.
                "&passed=". $passed.
                "&vaccinated=". $vaccinated);
            die; 
        }

        // Get the values from the URL
        if (isset($_GET['LoadReport'])) {
            if ($fromDate == "" && isset($_GET['fromDate'])) {
                $fromDate = $_GET["fromDate"];
            }
            if ($toDate == "" && isset($_GET['toDate'])) {
                $toDate = $_GET["toDate"];
            }
            if ($userType == "" && isset($_GET['userType'])) {
                $userType = $_GET["userType"];
            }
            else {
                $userType = "All";
            }
            if ($building == "" && isset($_GET['building'])) {
                $building = $_GET["building"];
            }
            else {
                $building = "All";
            }
            if ($passed == "" && isset($_GET['passed'])) {
                $passed = $_GET["passed"];
            }
            else {
                $passed = "All";
            }
            if ($vaccinated == "" && isset($_GET['vaccinated'])) {
                $vaccinated = $_GET["vaccinated"];
            }
            else {
                $vaccinated = "All";
            }
        }

        // Query the database
        $results = getReportResults($fromDate, $toDate, $userType, $building, $passed, $vaccinated);

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
        $vaccinatedAllSelected = "";
        $vaccinatedYesSelected = "";
        $vaccinatedNoSelected = "";
        
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

        // Choose which option is selected in the Vaccinated dropdown
        switch ($vaccinated) {
            case "Yes":
                $vaccinatedYesSelected = "Selected";
                break;
            case "No":
                $vaccinatedNoSelected = "Selected";
                break;
            default:
                $vaccinatedAllSelected = "Selected";
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
        $vaccinatedAllSelected = "selected";
        $vaccinatedYesSelected = "";
        $vaccinatedNoSelected = "";
    }
}

// Check if the user clicked the delete link on the reports page 
if (isset($_GET['reports'])){
    if (isset($_GET["delete"]) || isset($_GET['id'])){
        
        // Delete the entry
        deleteScreeningEntry($_GET['id']);

        // Build the return URL
        $url = "index.php?reports&LoadReport&fromDate=". $_GET["fromDate"]. 
            "&toDate=". $_GET["toDate"]. 
            "&userType=". $_GET["userType"].
            "&building=". $_GET["building"]. 
            "&passed=". $_GET["passed"];

        // Redirect the user to a page with a sharable URL
        header("location:". $url);
        die;
    }
}

// Check if the user submitted the form on the missing page 
if (isset($_GET['missing'])){
    if (isset($_POST["submit"]) || isset($_GET['LoadMissing'])){

        // Initialize the variable
        $building = "";

        // Get the values from the form
        if (isset($_POST["submit"])) {
            $building = $_POST["building"];

            // Redirect the user to a page with a sharable URL
            header("location:index.php?missing&LoadMissing&building=". $building);
            die; 
        }

        // Get the values from the URL
        if (isset($_GET['LoadMissing'])) {
            if ($building == "" && isset($_GET['building'])) {
                $building = $_GET["building"];
            }
        }

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

// Check if the user submitted the form on the missingstudents page 
if (isset($_GET['missingstudents'])){
    if (isset($_POST["submit"]) || isset($_GET['LoadMissing'])){

        // Initialize the variable
        $building = "";
        $fromDate = "";
        
        // Get the values from the form
        if (isset($_POST["submit"])) {
            $building = $_POST["building"];
            $fromDate = $_POST["from_date"];

            // Redirect the user to a page with a sharable URL
            header("location:index.php?missingstudents&LoadMissing".
                "&building=". $building.
                "&fromDate=". $fromDate);
            die; 
        }

        // Get the values from the URL
        if (isset($_GET['LoadMissing'])) {
            if ($building == "" && isset($_GET['building'])) {
                $building = $_GET["building"];
            }
            if ($fromDate == "" && isset($_GET['fromDate'])) {
                $fromDate = $_GET["fromDate"];
            }
        }

        // Query the database
        $results = getMissingStudentResults($building,$fromDate);

        // Set the default values for the form elements
        $buildingValue = "";
        $buildingAllSelected = "";
        $fromDateValue = $fromDate;

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
        $fromDateValue = date("Y-m-d");
    }
}

// Check if the user clicked an action link on the Missing Student Report page
if (isset($_GET['missingstudents'])){
    if (isset($_GET['userName'])){
        
        // Get the students information from the database
        $results = getStudentInfo($_GET['userName']);
        $studentData=$results->fetch_assoc();

        // Set the hasPassed variable based on what icon this clicked
        if (isset($_GET['allow'])) {
            $hasPassed = "TRUE";
        }
        if (isset($_GET['deny'])) {
            $hasPassed = "FALSE";

            // Send email if they failed the screener
            entryDeniedEmail($studentData['FirstName'], $studentData['LastName'], $studentData['Building']);
        }

        // Add the entry to the database
        if (isset($hasPassed)) {
            addEvent($studentData['UserName'],
                $studentData['FirstName'],
                $studentData['LastName'],
                $studentData['Email'],
                $studentData['PhoneNumber'],
                $studentData['Building'],
                "Student", 
                $hasPassed,FALSE);
        }

        // Build the return URL
        $url = "index.php?missingstudents&LoadMissing&building=". $_GET["building"]. 
            "&fromDate=". $_GET["fromDate"];    

        // Redirect the user to a page with a sharable URL
        header("location:". $url);
        die;
    }
}

?>