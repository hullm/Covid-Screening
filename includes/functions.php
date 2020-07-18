<?php

// This file contains the functiones needed for the site.

function db_connect() {

    include 'includes/config.php';

    // Define connection as a static variable
    static $connection;

    // See if we're already connected to the database first
    if(!isset($connection)) {
        
        // Grab the settings from the config.ini file
        $config = parse_ini_file($configFile); 
        $connection = @mysqli_connect($config['servername'],$config['username'],$config['password'],$config['dbname']);
    }

    // Verify the connection was sucessful, if not send them to the setup page
    if($connection == FALSE) {
        header("location:setup.php");
        die; 
    }
    return $connection;
}

function addEvent($userName,$firstName,$lastName,$email,$phoneNumber,$building,$userType,$hasPassed) {

    // Make sure they didn't already submit today.  This will prevent a reload posting a second time.
    if(!alreadySubmitted($userName)) {

        // Connect to the database
        $connection = db_connect();

        // Prepair the variables for adding to the database
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
        
        if ($connection->query($sql) === FALSE) {
            echo $sql. "<br />";
            echo "Failed to add record to the database...";
        }

        // If the user is not a visitor then add them to the people table, or update their info if they exist
        if ($userType != "Visitor") {

            // See if they're already in the table
            $sql = "SELECT id FROM People WHERE UserName='". $userName. "';";
            $results = $connection->query($sql);

            // 
            if ($results->num_rows > 0) {
                $row = $results->fetch_assoc();
                $sql = "UPDATE People SET 
                    PhoneNumber='". $fixedPhoneNumber. "',
                    LastCheckin=CURDATE() 
                    WHERE id=". $row['id']. ";";
            } 
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
            
            // Update or add them to the people table
            if ($connection->query($sql) === FALSE) {
                echo $sql. "<br />";
                echo "Failed to add record to the database...";
            }
        }
    }
}

function alreadySubmitted($userName) {

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

function getPhoneNumber($userName){

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
        return NULL;
    }
}

function getAdminResults($startDate, $endDate, $userType, $building){

    // Connect to the database
    $connection = db_connect();

    // Prepair the variables for the database query
    $fixedBuilding = mysqli_real_escape_string($connection,$building);
    
    // Get the correct value for the userType
    switch ($userType) {
        case "Adult":
            $userTypeQuery = "UserType='Adult'";
            break;
        case "Admin":
            $userTypeQuery = "UserType='Adult'";
        case "Student":
            $userTypeQuery = "UserType='Student'";
            break;
        case "Visitor":
            $userTypeQuery = "UserType='Visitor'";
            break;
        default:
            $userTypeQuery = "";
            break;
    } 

    // See what the users result was for today
    $sql = "SELECT UserName,FirstName,LastName,Email,PhoneNumber,Building,UserType,HasPassed,DateSubmitted,TimeSubmitted 
        FROM Tracking WHERE ".
        "DateSubmitted>=". $startDate. " AND ".
        "DateSubmitted<=". $endDate. " AND ".
        "Building='". $fixedBuilding. "' AND ".
        $userTypeQuery. ";";

    // Look up the data
    if ($connection->query($sql) === FALSE) {
        echo $sql. "<br />";
        echo "Failed to add record to the database...";
    }
    $results = $connection->query($sql);

    return $results;

}

function isAuthenticated($userName, $password) {

    include 'includes/config.php';

    // Connect to Active Directory using incofmation from the config.ini file
    $config = parse_ini_file($configFile);
    $ldap = ldap_connect("ldap://". $config['DC']);
    $netbiosName =  $config['netbios']. "\\". $userName;
    ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap, LDAP_OPT_REFERRALS, 0);
    
    if (strlen($password) > 0){

        $ADConnection = @ldap_bind($ldap, $netbiosName, $password);

        if ($ADConnection) {
            $result = ldap_search($ldap,$config['rootDN'],"(sAMAccountName=$userName)");

            $userLookup = ldap_get_entries($ldap, $result);
            
            // If the user is found create a session
            if($userLookup['count'] = 1) {
                
                // Determine what type of user they are
                if (strpos($userLookup[0]["distinguishedname"][0],$config['studentOU']) !== false) {
                    $_SESSION["userType"]="Student";
                }
                else {
                    $_SESSION["userType"]="Adult";
                }

                // Set the session variables
                $_SESSION["userName"] = $userLookup[0]["samaccountname"][0];
                $_SESSION["firstName"] = $userLookup[0]["givenname"][0];
                $_SESSION["lastName"] = $userLookup[0]["sn"][0];
                $_SESSION["email"] = $userLookup[0]["userprincipalname"][0];
                $_SESSION["loggedIn"] = TRUE;
            }

            @ldap_close($ldap);

            return TRUE;

        } 
        else {
            return FALSE;
        }
    } 
    else {
        return FALSE;
    }

}

function visitorSignIn($firstName, $lastName, $email){
    // Set session variables for visitor sign in.  No authentication required
    $_SESSION["loggedIn"] = TRUE;
    $_SESSION["userName"] = $firstName.$lastName;
    $_SESSION["firstName"] = $firstName;
    $_SESSION["lastName"] = $lastName;
    $_SESSION["email"] = $email;
    $_SESSION["userType"] = "Adult";
}

function logout() {
    $_SESSION["loggedIn"] = FALSE;
    $_SESSION["userName"] = "";
    $_SESSION["firstName"] = "";
    $_SESSION["lastName"] = "";
    $_SESSION["email"] = "";
    header("location:login.php");
    die; 
}

function getStates(){

    // Connect to health.ny.gov to get the list of states 
    $html = file_get_contents('https://coronavirus.health.ny.gov/covid-19-travel-advisory');
    $allStates = "'Alabama','Alaska','American Samoa','Arizona','Arkansas','California','Colorado','Connecticut','Delaware','District of Columbia','Federated States of Micronesia','Florida','Georgia','Guam','Hawaii','Idaho','Illinois','Indiana','Iowa','Kansas','Kentucky','Louisiana','Maine','Marshall Islands','Maryland','Massachusetts','Michigan','Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire','New Jersey','New Mexico','New York','North Carolina','North Dakota','Northern Mariana Islands','Ohio','Oklahoma','Oregon','Palau','Pennsylvania','Puerto Rico','Rhode Island','South Carolina','South Dakota','Tennessee','Texas','Utah','Vermont','Virgin Island','Virginia','Washington','West Virginia','Wisconsin','Wyoming'";
    $stateArray = array();
    $doc = new DOMDocument();
    libxml_use_internal_errors(TRUE);
    if(!empty($html)){
        $doc->loadHTML($html);
        libxml_clear_errors(); 
        $xpath = new DOMXPath($doc);
        $rows = $xpath->query('//*[@class="wysiwyg--field-webny-wysiwyg-body"]//li');
    
        if($rows->length > 0){

            foreach($rows as $row){

                // Remove &nbsp; from the name if it's there
                $state = htmlentities($row->nodeValue, null, 'utf-8');
                $state = str_replace("&nbsp;", "", $state);

                if (strpos($allStates, $state) == true) {
                    array_push($stateArray,$state);
                }
            }
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
            return $stateList;
        }
        else {
            return "";
        } 
    }
    else {
        return "";
    }
}

function getSymptoms(){

    // Connect to the CDC site to get the symptoms
    $html = file_get_contents('https://www.cdc.gov/coronavirus/2019-ncov/symptoms-testing/symptoms.html');
    $symptomsArray = array();
    $doc = new DOMDocument();
    libxml_use_internal_errors(TRUE);
    if(!empty($html)){
        $doc->loadHTML($html);
        libxml_clear_errors(); 
        $xpath = new DOMXPath($doc);
        $rows = $xpath->query('//*[@class="public-symptoms"]//li');
    
        if($rows->length > 0){

            foreach($rows as $row){

                // Remove &nbsp; from the name if it's there
                $symptom = htmlentities($row->nodeValue, null, 'utf-8');
                $symptom = str_replace("&nbsp;", "", $symptom);
                array_push($symptomsArray,$symptom);
            }
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
            return $symptomList;
        }
        else {
            return "";
        } 
    }
    else {
        return "";
    }
}

?>