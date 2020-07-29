<?php

// This file contains the functions needed for the site.

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '/var/www/PHPMailer/src/Exception.php';
require '/var/www/PHPMailer/src/PHPMailer.php';
require '/var/www/PHPMailer/src/SMTP.php';

function db_connect() {

    // Use this to connect to the database

    include 'includes/config.php';

    // Define connection as a static variable
    static $connection;

    // See if we're already connected to the database first
    if(!isset($connection)) {
        
        // Grab the settings from the config.ini file
        $config = parse_ini_file($configFile); 
        $connection = @mysqli_connect($config['servername'],$config['username'],$config['password'],$config['dbname']);
    }

    // Verify the connection was successful, if not send them to the setup page
    if($connection == FALSE) {
        echo "<script>";
        echo "window.location.href = \"setup.php\";";
        echo "</script>";
    }

    // Send back the connection
    return $connection;
}

function addEvent($userName,$firstName,$lastName,$email,$phoneNumber,$building,$userType,$hasPassed) {

    // This will add the results of the form to the database, and add employees to the people table.  If an employee is already in the
    // people table it will update the LastCheckin date, and their phone number.

    // Make sure they didn't already submit today.  This will prevent a page reload from posting a second time.
    if(!alreadySubmitted($userName)) {

        // Connect to the database
        $connection = db_connect();

        // Prepare the variables for adding to the database
        $fixedUserName = mysqli_real_escape_string($connection,$userName);
        $fixedFirstName = mysqli_real_escape_string($connection,$firstName);
        $fixedLastName = mysqli_real_escape_string($connection,$lastName);
        $fixedEmail = mysqli_real_escape_string($connection,$email);
        $fixedPhoneNumber = mysqli_real_escape_string($connection,$phoneNumber);
        $fixedBuilding = mysqli_real_escape_string($connection,$building);
        $fixedUserType = mysqli_real_escape_string($connection,$userType);

        // Write the data to the tracking table
        $sql = "INSERT INTO Tracking (UserName,FirstName,LastName,Email,PhoneNumber,Building,UserType,HasPassed,DateSubmitted,TimeSubmitted)
            VALUES ('". 
            $fixedUserName. "','". 
            $fixedFirstName. "','". 
            $fixedLastName. "','". 
            $fixedEmail. "','".
            $fixedPhoneNumber. "','". 
            $fixedBuilding. "','". 
            $fixedUserType. "',".
            $hasPassed. 
            ",CURDATE(),CURTIME());";
        
        // We test while adding data to the database, if it fails the SQL string will be displayed.
        if ($connection->query($sql) === FALSE) {
            echo $sql. "<br />";
            echo "Failed to add record to the database...";
        }

        // If the user is not a visitor then add them to the people table, or update their info if they exist
        if ($userType == "Employee" || $userType == "Admin") {

            // See if they're already in the table
            $sql = "SELECT id FROM People WHERE UserName='". $userName. "';";
            $results = $connection->query($sql);

            // Update them in the people table
            if ($results->num_rows > 0) {
                $row = $results->fetch_assoc();
                $sql = "UPDATE People SET 
                    PhoneNumber='". $fixedPhoneNumber. "',
                    LastCheckin=CURDATE() 
                    WHERE id=". $row['id']. ";";
            } 
            // Add them to the people table
            else {
                $sql = "INSERT INTO People (UserName,FirstName,LastName,Email,PhoneNumber,UserType,LastCheckin)
                    VALUES ('". 
                    $fixedUserName. "','". 
                    $fixedFirstName. "','". 
                    $fixedLastName. "','". 
                    $fixedEmail. "','".
                    $fixedPhoneNumber. "','". 
                    $fixedUserType. "',".
                    "CURDATE());";
                    }
            
            // Execute the SQL command and display the SQL string if it fails
            if ($connection->query($sql) === FALSE) {
                echo $sql. "<br />";
                echo "Failed to add record to the database...";
            }
        }
    }
}

function purgeOldData($days){

    // This will remove any data that's older then the number of days passed to it

    // Connect to the database
    $connection = db_connect();

    // Remove old entries from the Tracking table
    $sql = "DELETE FROM Tracking WHERE DateSubmitted<=DATE_ADD(CURDATE(), INTERVAL ". -$days. " DAY);";

    // Execute the sql command and output an error if present
    if ($connection->query($sql) === FALSE) {
        echo $sql. "<br />";
        echo "Failed to delete records from the database...";
    }

    // Remove old entries from the People table
    $sql = "DELETE FROM People WHERE LastCheckin<=DATE_ADD(CURDATE(), INTERVAL ". -$days. " DAY);";

    // Execute the sql command and output an error if present
    if ($connection->query($sql) === FALSE) {
        echo $sql. "<br />";
        echo "Failed to delete records from the database...";
    }

}

function alreadySubmitted($userName) {

    // Check and see if the user already submitted the form today.

    // Connect to the database
    $connection = db_connect();
    
    // See if there's a submission by the user for today
    $sql = "SELECT id FROM Tracking WHERE UserName='". $userName. "' AND DateSubmitted=CURDATE();";
    $results = $connection->query($sql);

    // Return the results of the query
    if ($results->num_rows > 0) {
        return TRUE;
    } 
    else {
        return FALSE;
    }
    
}

function  GetLatestEntry() {

    // Returns the ID of the most recent entry

    // Connect to the database
    $connection = db_connect();

    // Look up the latest entry
    $sql = "SELECT id FROM Tracking ORDER BY id DESC LIMIT 1;";
    $results = $connection->query($sql);

    // Return the latest entry
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();     
        return $row['id'];
    } 
    else {
        return "1";
    }
}

function getPhoneNumber($userName){

    // Get the employees phone number from the people table if it exists

    // Connect to the database
    $connection = db_connect();
    
    // Look up the user in the database
    $sql = "SELECT PhoneNumber FROM People WHERE UserName='". $userName. "';";
    $results = $connection->query($sql);

    // Return the phone number if found, otherwise return an empty string
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();     
        return $row['PhoneNumber'];
    } 
    else {
        return "";
    }
}

function getUserResults($userName){

    // If the user already took the test today this will return if they passed or not.

    // Connect to the database
    $connection = db_connect();
    
    // See what the users result was for today
    $sql = "SELECT HasPassed FROM Tracking WHERE UserName='". $userName. "' AND DateSubmitted=CURDATE();";
    $results = $connection->query($sql);

    // Let them know if they passed, if they haven't submitted today then return a NULL value
    if ($results->num_rows > 0) {
        $row = $results->fetch_assoc();
        if ($row['HasPassed'] == 1 ) {
            return TRUE;
        } 
        else {
            return FALSE;
        }
    } 
    else {
        return NULL; // This is returned if they didn't take the test.
    }
}

function getReportResults($startDate, $endDate, $userType, $building, $hasPassed){

    // When you use the reports page and perform a query this function is called to get the data.

    // Connect to the database
    $connection = db_connect();

    // Prepare the variables for the database query
    $fixedBuilding = mysqli_real_escape_string($connection,$building);
    
    // Set the SQL WHERE clause for the UserType
    switch ($userType) {
        case "Employee":
            $userTypeQuery = "(UserType='Employee' OR UserType='Admin')";
            break;
        case "Admin":
            $userTypeQuery = "(UserType='Employee' OR UserType='Admin')";
        case "Student":
            $userTypeQuery = "UserType='Student'";
            break;
        case "Visitor":
            $userTypeQuery = "UserType='Visitor'";
            break;
        default:
            $userTypeQuery = "UserType IS NOT NULL";
            break;
    }
    
    // Set the SQL WHERE clause for the building
    if ($building!="All") {
        $buildingQuery = "Building='". $fixedBuilding. "'";
    }
    else {
        $buildingQuery = "Building IS NOT NULL";
    }

    // Set the SQL WHERE clause for hasPassed
    if ($hasPassed!="All") {
        $hasPassQuery = "hasPassed=". $hasPassed;
    }
    else {
        $hasPassQuery = "hasPassed IS NOT NULL";
    }

    // Build the SQL string to get the results
    $sql = "SELECT UserName,FirstName,LastName,Email,PhoneNumber,Building,UserType,HasPassed,DateSubmitted,TimeSubmitted 
        FROM Tracking WHERE ".
        "DateSubmitted>='". $startDate. "' AND ".
        "DateSubmitted<='". $endDate. "' AND ".
        $hasPassQuery. " AND ".
        $buildingQuery. " AND ".
        $userTypeQuery. " ".
        "ORDER BY LastName, FirstName;";
    // Look up the data and output the SQL string if it fails
    if ($connection->query($sql) === FALSE) {
        echo $sql. "<br />";
        echo "Failed to query the database...";
    }
    $results = $connection->query($sql);

    // Return the results
    return $results;
}

function getRecentResults($numberOfDays) {
    
    // Connect to the database
    $connection = db_connect();
    
    // 
    $sql = "SELECT id,UserName,FirstName,LastName,Email,PhoneNumber,Building,UserType,HasPassed,DateSubmitted,TimeSubmitted  
        FROM Tracking ORDER BY id DESC LIMIT ". $numberOfDays. ";";

    // Look up the data and output the SQL string if it fails
    if ($connection->query($sql) === FALSE) {
        echo $sql. "<br />";
        echo "Failed to query the database...";
    }
    $results = $connection->query($sql);

    // Return the results
    return $results;
}

function getMissingResults($building ){

    // When you use the missing page and perform a query this function is called to get the data.

    // Connect to the database
    $connection = db_connect();

    // Prepare the variables for the database query
    $fixedBuilding = mysqli_real_escape_string($connection,$building);
    
    // Set the SQL WHERE clause for the UserType
    $userTypeQuery = "(People.UserType='Employee' OR People.UserType='Admin')";
    
    // Set the SQL WHERE clause for the building
    if ($building!="All") {
        $buildingQuery = "Tracking.Building='". $fixedBuilding. "'";
    }
    else {
        $buildingQuery = "Tracking.Building IS NOT NULL";
    }

    // Build the SQL string to get the results
    $sql = "SELECT People.UserName,People.FirstName,People.LastName,People.Email,People.PhoneNumber,People.UserType,Tracking.Building
        FROM People INNER JOIN Tracking ON People.UserName=Tracking.UserName WHERE ". 
        "People.LastCheckIn<CURDATE() AND ".
        $buildingQuery. " AND ".
        $userTypeQuery. " ".
        "GROUP BY People.UserName ORDER BY People.LastName, People.FirstName;";

    // Look up the data and output the SQL string if it fails
    if ($connection->query($sql) === FALSE) {
        echo $sql. "<br />";
        echo "Failed to query the database...";
    }
    $results = $connection->query($sql);

    // Return the results
    return $results;
}

function  getScreenedTodayLabels(){

    // This function will return labels needed for the Total Screened Today pie chart

    // Connect to the database
    $connection = db_connect();

    // Build the SQL string to get the number who have submitted today.
    $sql = "SELECT UserType, COUNT(ID) as SubmittedToday FROM Tracking WHERE DateSubmitted=CURDATE() GROUP BY UserType ORDER BY UserType;";
    
    // Get the data from the database
    $results = $connection->query($sql);

    // Build the chartData string
    $chartData = "";
    if ($results->num_rows > 0) {
        while ($row=$results->fetch_assoc()) {
            if ($row['UserType'] != "Admin") {
                $chartData .= "'". $row['UserType']. "',";
            }
        }
    } 
    else {
        $chartData = "Employee";
    }

    // Return the results
    return $chartData;

}

function  getScreenedTodayData(){
    
    // This function will return data needed for the Total Screened Today pie chart

    // Connect to the database
    $connection = db_connect();

    // Build the SQL string to get the number who have submitted today.
    $sql = "SELECT UserType, COUNT(ID) as SubmittedToday FROM Tracking WHERE DateSubmitted=CURDATE() GROUP BY UserType ORDER BY UserType;";
    
    // Get the data from the database
    $results = $connection->query($sql);

    // Build the chartData string while merging employees and admin into one count.
    $chartData = "";
    $adminCount = 0;
    $employeeCount = 0;
    if ($results->num_rows > 0) {
        while ($row=$results->fetch_assoc()) {
            switch ($row['UserType']) {
                case "Admin":
                    $adminCount = $row['SubmittedToday'];
                    break;
                case "Employee":
                    $employeeCount = $row['SubmittedToday'] + $adminCount;
                    $chartData = $employeeCount. ",";
                    break;
                case "Student":
                    $chartData .= $row['SubmittedToday']. ",";
                    break;
                case "Visitor":
                    $chartData .= $row['SubmittedToday'];
                    break;
            }
        }
    }

    // Old code that will list admins and employees separately.  If that's what you want uncomment this block. 
    // $chartData = "";
    // if ($results->num_rows > 0) {
    //     while ($row=$results->fetch_assoc()) {
    //         $chartData .= $row['SubmittedToday']. ",";
    //     }
    // } 
    // else {
    //     $chartData = "0";
    // }

    // Return the results
    return $chartData;

}

function  getEmployeesScreenedToday(){
    
    // This function will return data needed for the Employees Screened Today pie chart

    // Connect to the database
    $connection = db_connect();

    // Build the SQL string to get the number who have submitted today.
    $sql = "SELECT COUNT(ID) as SubmittedToday FROM People WHERE LastCheckin=CURDATE();";
    
    // Get the data from the database
    $results = $connection->query($sql);
    if ($results->num_rows > 0) {
        $result = $results->fetch_assoc();
        $chartData = $result['SubmittedToday']. ",";
    }
    else {
        $chartData = "0,";
    }

    // Build the SQL string to get the number who haven't submitted today.
    $sql = "SELECT COUNT(ID) as SubmittedToday FROM People WHERE LastCheckin<CURDATE();";
    
    // Get the data from the database
    $results = $connection->query($sql);
    if ($results->num_rows > 0) {
        $result = $results->fetch_assoc();
        $chartData .= $result['SubmittedToday'];
    }
    else {
        $chartData .= "0";
    }

    // Return the results
    return $chartData;

}

function  getScreenedResults(){
    
    // This function will return data needed for the Screening Results pie chart

    // Connect to the database
    $connection = db_connect();

    // Build the SQL string to get the number who have submitted today.
    $sql = "SELECT COUNT(ID) as PassedToday FROM Tracking WHERE HasPassed = True AND DateSubmitted=CURDATE();";
    
    // Get the data from the database
    $results = $connection->query($sql);
    if ($results->num_rows > 0) {
        $result = $results->fetch_assoc();
        $chartData = $result['PassedToday']. ",";
    }
    else {
        $chartData = "0,";
    }

    // Build the SQL string to get the number who haven't submitted today.
    $sql = "SELECT COUNT(ID) as FailedToday FROM Tracking WHERE HasPassed = False AND DateSubmitted=CURDATE();";
    
    // Get the data from the database
    $results = $connection->query($sql);
    if ($results->num_rows > 0) {
        $result = $results->fetch_assoc();
        $chartData .= $result['FailedToday'];
    }
    else {
        $chartData .= "1";
    }

    // Return the results
    return $chartData;

}

function isAuthenticated($userName, $password) {

    // Takes the provided username and password and see if it's correct

    // This file contains the path to the config file needed to connect to Active Directory
    include 'includes/config.php';

    // Fix the username if they gave an email address
    if(strpos($userName, "@") == true){
        $userName = strstr($userName, '@', true);
    } 

    // Connect to Active Directory using information from the config.ini file
    $config = parse_ini_file($configFile);
    $ldap = ldap_connect("ldap://". $config['DC']);
    $netbiosName =  $config['netbios']. "\\". $userName;
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    
    // Make sure they sent a password
    if (strlen($password) > 0){

        // Connect to Active Directory with the username and password provided by the user
        $ADConnection = @ldap_bind($ldap, $netbiosName, $password);

        // If it worked then they provided the right info, grab information about them from Active Directory
        if ($ADConnection) {
            
            // Look up the user in Active Directory
            $result = ldap_search($ldap,$config['rootDN'],"(sAMAccountName=$userName)");
            $userLookup = ldap_get_entries($ldap, $result);
            
            // If the user is found create a session
            if($userLookup['count'] = 1) {
                
                // Determine what type of user they are
                if (strpos($userLookup[0]["distinguishedname"][0],$config['studentOU']) !== false) {
                    $_SESSION["userType"]="Student";
                }
                else {
                    $_SESSION["userType"]="Employee";
                }

                // Find out if the user is an admin
                $admins = explode(',',$config['admins']);
                foreach($admins as $admin) {
                    if (strtolower($admin) == strtolower($userName)){
                        $_SESSION["userType"]="Admin";
                    } 
                }

                // Set the session variables
                $_SESSION["userName"] = $userLookup[0]["samaccountname"][0];
                $_SESSION["firstName"] = $userLookup[0]["givenname"][0];
                $_SESSION["lastName"] = $userLookup[0]["sn"][0];
                $_SESSION["email"] = $userLookup[0]["userprincipalname"][0];
                $_SESSION["loggedIn"] = TRUE;
            }

            // Close the connection to Active Directory
            @ldap_close($ldap);

            return TRUE; // The user is authenticated, and the session created
        } 
        else {
            return FALSE; // Unable to connect to Active Directory with the username and password provided
        }
    } 
    else {
        return FALSE; // The password was blank, if a blank password is passed it will succeed for stupid reasons
    }

}

function visitorSignIn($firstName, $lastName, $email){

    // Create the session for a visitor

    // Set session variables for visitor sign in.  No authentication required
    $_SESSION["loggedIn"] = TRUE;
    $_SESSION["userName"] = $firstName.$lastName."_visitor";
    $_SESSION["firstName"] = $firstName;
    $_SESSION["lastName"] = $lastName;
    $_SESSION["email"] = $email;
    $_SESSION["userType"] = "Visitor";
}

function logout() {

    // Log out of the system

    // Set the session variables to empty strings.
    $_SESSION["loggedIn"] = FALSE;
    $_SESSION["userName"] = "";
    $_SESSION["firstName"] = "";
    $_SESSION["lastName"] = "";
    $_SESSION["email"] = "";
    $_SESSION["userType"] = "";
    header("location:login.php");
    die; 
}

function getStates(){

    // This will connect to the health.ny.gov site and pull a list of states that are part of the travel ban

    // Connect to health.ny.gov to get the list of states
    $html = @file_get_contents('https://coronavirus.health.ny.gov/covid-19-travel-advisory');

    // Build a list of all possible states, and an empty array to hold the list of states
    $allStates = "'Alabama','Alaska','American Samoa','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Federated States of Micronesia','Florida','Georgia','Guam','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Marshall Islands','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Northern Mariana Islands','Ohio','Oklahoma','Oregon','Palau','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Island','Virginia','Washington','West Virginia','Wisconsin','Wyoming'";
    $stateArray = array();
    
    // Create a new DOCDocument 
    $doc = new DOMDocument();
    libxml_use_internal_errors(TRUE);
    
    // If the site returned a value then we'll act on it
    if(!empty($html)){

        // Take the data, turn it into an HTML file, clear any errors, create a DOM and pull out all list items of the listed class
        $doc->loadHTML($html);
        libxml_clear_errors(); 
        $xpath = new DOMXPath($doc);
        $rows = $xpath->query('//*[@class="wysiwyg--field-webny-wysiwyg-body"]//li');
    
        // If data is found then look through it for the states
        if($rows->length > 0){

            //Loop through each line
            foreach($rows as $row){

                // Remove &nbsp; from the name if it's there
                $state = htmlentities($row->nodeValue, null, 'utf-8');
                $state = str_replace("&nbsp;", "", $state);

                // If the value from the webpage is found in the list of states from above then add it to the array
                if (strpos($allStates, $state) == true) {
                    array_push($stateArray,$state);
                }
            }

            // Loop through the array to list each state in a string separated by a comma with the last one being separated with an or.
            $stateCount = count($stateArray);
            $loopCounter = 0;
            $stateList = "";
            foreach($stateArray as $value) {
                $loopCounter += 1;
                switch ($loopCounter) {
                    case $stateCount - 1:
                        $stateList .= $value. ", or ";
                        break;
                    case $stateCount:
                        $stateList .= $value;
                        break;
                    default:
                        $stateList .= $value. ", ";
                        break;
                }
            }
            return $stateList. "."; // Return the list
        }
        else {
            return "";  // The page didn't have the requested div
        } 
    }
    else {
        return ""; // The website didn't have any data
    }
}

function getSymptoms(){

    // This will connect to the CDC site and pull down the current list of Covid 19 symptoms

    // Connect to the CDC site to get the symptoms and create an empty array to store them
    $html = @file_get_contents('https://www.cdc.gov/coronavirus/2019-ncov/symptoms-testing/symptoms.html');
    $symptomsArray = array();
    
    // Create a new DOCDocument 
    $doc = new DOMDocument();
    libxml_use_internal_errors(TRUE);
    
    // If the site returned a value then we'll act on it
    if(!empty($html)){

        // Take the data, turn it into an HTML file, clear any errors, create a DOM and pull out all list items of the listed class
        $doc->loadHTML($html);
        libxml_clear_errors(); 
        $xpath = new DOMXPath($doc);
        $rows = $xpath->query('//*[@class="public-symptoms"]//li');
    
        // If data is found then look through it for the symptoms
        if($rows->length > 0){

            //Loop through each line
            foreach($rows as $row){

                // Remove &nbsp; from the name if it's there
                $symptom = htmlentities($row->nodeValue, null, 'utf-8');
                $symptom = str_replace("&nbsp;", "", $symptom);
                
                // Add the symptom to the array
                array_push($symptomsArray,$symptom);
            }

            // Loop through the array to list each symptom in a string separated by a comma with the last one being separated with an or.
            $symptomCount = count($symptomsArray);
            $loopCounter = 0;
            $symptomList = "";
            foreach($symptomsArray as $value) {
                $loopCounter += 1;
                switch ($loopCounter) {
                    case $symptomCount - 1:
                        $symptomList .= $value. ", or ";
                        break;
                    case $symptomCount:
                        $symptomList .= $value;
                        break;
                    default:
                        $symptomList .= $value. ", ";
                        break;
                }
            }
            return $symptomList. "."; // Return the list
        }
        else {
            return ""; // The page didn't have the requested div
        } 
    }
    else {
        return ""; // The website didn't have any data
    }
}

function entryDeniedEmail($firstName, $lastName, $building){

    // Get settings from config
    include 'includes/config.php';
    $config = parse_ini_file($configFile); 
    $host = $config['host'];
    $smtpAuth = $config['smtpAuth'];
    $port = $config['port'];
    $recipients = explode(',',$config['mailRecipients']);
    $fromAddress = $config['fromAddress'];
    $fromName = $config['fromName'];

    // Set variables for email
    $subject = "ALERT: Building Entry Denied";
    $message = $firstName." ".$lastName." has been denied access at the ".$building.".";
    
    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try {
        //Server settings
        // $mail->SMTPDebug = 2;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $host;  // Specify main and backup SMTP servers
        $mail->SMTPAuth = $smtpAuth;                               // Enable SMTP authentication
        $mail->Port = $port;                                    // TCP port to connect to


        $mail->SMTPOptions = array(
          'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
          )
        );

        //Recipients
        $mail->setFrom($fromAddress, $fromName);
        for ($i=0; $i<count($recipients); $i++){
            $mail->addAddress($recipients[$i]);
        }

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = "$subject";
        $mail->Body    = $message;
        @$mail->send();
        // echo 'Message has been sent';
    }
    catch (Exception $e) {
        //echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
    }
  }

?>