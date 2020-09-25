<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;
require '/var/www/PHPMailer/src/Exception.php';
require '/var/www/PHPMailer/src/PHPMailer.php';
require '/var/www/PHPMailer/src/SMTP.php';
require '/var/www/html/vendor/autoload.php';

function db_connect() {

    // Use this to connect to the database
    include 'config.php';

    // Define connection as a static variable
    static $connection;

    // See if we're already connected to the database first
    if(!isset($connection)) {
        
        // Grab the settings from the config.ini file
        $config = parse_ini_file($configFile); 
        $connection = @mysqli_connect($config['servername'],$config['username'],$config['password'],$config['dbname']);
    }

    // Send back the connection
    return $connection;
}

function importEmployees(){

    // Import the employee data into the database

    // Connect to the database
    $connection = db_connect();

    // Disable all student accounts, later we will only turn on the ones that are still here
    $sql = "UPDATE People SET Active=FALSE";
    $connection->query($sql);

    // Define constants needed to pull data from the CSV
    define("FIRSTNAME", 3);
    define("LASTNAME", 4);
    define("USERNAME", 5);
    define("EMAIL", 6);
    define("BUILDING", 12);

    // Open the CSV file and add or update the employees
    if(file_exists("csv/employees.csv")){
        $file = fopen("csv/employees.csv","r");

        // Loop through the import file
        fgetcsv($file);
        while(! feof($file)) {
            $data = fgetcsv($file);

            // Fix the data before we attempt to add it to the database
            $firstName = mysqli_real_escape_string($connection,$data[FIRSTNAME]);
            $lastName = mysqli_real_escape_string($connection,$data[LASTNAME]);
            $username = mysqli_real_escape_string($connection,$data[USERNAME]);
            $email = mysqli_real_escape_string($connection,$data[EMAIL]);
            $building = mysqli_real_escape_string($connection,$data[BUILDING]);

            //Check and see if they exist in the database, if so update them, if not add them.
            $sql = "SELECT id FROM People WHERE Username='". $data[USERNAME]. "';";
            $results = $connection->query($sql);

            if ($results->num_rows > 0) {
                $sql = "UPDATE People SET 
                    Active=TRUE,
                    FirstName='". $firstName. "',
                    LastName='". $lastName. "',
                    Email='". $email. "',
                    Building='". $building. "' 
                    WHERE Username='". $data[USERNAME]. "';";
            } 
            else {
                $sql = "INSERT INTO People (Active,FirstName,LastName,UserName,Email,Building) 
                    VALUES (TRUE,'".
                        $firstName. "','".
                        $lastName. "','".
                        $username. "','".
                        $email. "','".
                        $building. "');";
            }
            $connection->query($sql);
        }
        fclose($file);
    }
    else {
        echo "Import file not found \"csv/employees.csv\"\n\r";
    }
}

function importStudents(){

    // Import the students data into the database

    // Connect to the database
    $connection = db_connect();

    // Disable all student accounts, later we will only turn on the ones that are still here
    $sql = "UPDATE Students SET Active=FALSE";
    $connection->query($sql);

    // Define constants needed to pull data from the CSV
    define("STUDENTID", 1);
    define("FIRSTNAME", 3);
    define("LASTNAME", 4);
    define("BUILDING", 12);
    define("GRADE", 17);
    define("USERNAME", 5);
    define("PASSWORD", 8);
    define("EMAIL", 6);

    // Open the CSV file and add or update the students
    if(file_exists("csv/students.csv")){
        $file = fopen("csv/students.csv","r");
        
        // Loop through the import file
        fgetcsv($file);
        while(! feof($file)) {
            $data = fgetcsv($file);

            // Fix the data before we attempt to add it to the database
            $firstName = mysqli_real_escape_string($connection,$data[FIRSTNAME]);
            $lastName = mysqli_real_escape_string($connection,$data[LASTNAME]);
            $building = mysqli_real_escape_string($connection,$data[BUILDING]);
            $grade = mysqli_real_escape_string($connection,$data[GRADE]);
            $username = mysqli_real_escape_string($connection,$data[USERNAME]);
            $password = mysqli_real_escape_string($connection,$data[PASSWORD]);
            $email = mysqli_real_escape_string($connection,$data[EMAIL]);

            //Check and see if they exist in the database, if so update them, if not add them.
            $sql = "SELECT id FROM Students WHERE StudentID=". $data[STUDENTID]. ";";
            $results = $connection->query($sql);

            if ($results->num_rows > 0) {
                $sql = "UPDATE Students SET 
                    Active=TRUE,
                    FirstName='". $firstName. "',
                    LastName='". $lastName. "',
                    Building='". $building. "',
                    Grade='". $grade. "',
                    UserName='". $username. "',
                    PWord='". $password. "',
                    Email='". $email. "'  
                    WHERE StudentID=". $data[STUDENTID]. ";";
            } 
            else {
                $sql = "INSERT INTO Students (Active,StudentID,FirstName,LastName,Building,Grade,UserName,PWord,Email) 
                    VALUES (TRUE,".
                        $data[STUDENTID]. ",'".
                        $firstName. "','".
                        $lastName. "','".
                        $building. "','".
                        $grade. "','".
                        $username. "','".
                        $password. "','".
                        $email. "');";
            }
            $connection->query($sql);
        }
        fclose($file);
    }
    else {
        echo "Import file not found \"csv/students.csv\"\n\r";
    }
}

function importParents(){

    // Import the parents data into the database

    // Connect to the database
    $connection = db_connect();

    // Disable all parent data, later will turn on what we need
    $sql = "UPDATE Parents SET Active=FALSE";
    $connection->query($sql);

    // Define constants needed to pull data from the CSV
    define("PARENTID", 4);
    define("STUDENTID", 3);
    define("FIRSTNAME", 5);
    define("LASTNAME", 6);
    define("RELATIONSHIP", 7);
    define("EMAIL", 8);
    define("HOMEPHONE", 9);
    define("CELLPHONE", 10);

    // Open the CSV file and add or update the students
    if(file_exists("csv/parents.csv")){
        $file = fopen("csv/parents.csv","r");

        // Loop through the import file
        fgetcsv($file);
        while(! feof($file)) {
            $data = fgetcsv($file);

            // Fix the data before we attempt to add it to the database
            $firstName = mysqli_real_escape_string($connection,$data[FIRSTNAME]);
            $lastName = mysqli_real_escape_string($connection,$data[LASTNAME]);
            $relationShip = mysqli_real_escape_string($connection,$data[RELATIONSHIP]);
            $email = mysqli_real_escape_string($connection,$data[EMAIL]);
            $homePhone = mysqli_real_escape_string($connection,$data[HOMEPHONE]);
            $cellPhone = mysqli_real_escape_string($connection,$data[CELLPHONE]);

            // Check and see if they exist in the database, if so update them, if not add them.
            $sql = "SELECT id FROM Parents WHERE ParentID=". $data[PARENTID]. " AND StudentID=". $data[STUDENTID]. ";";
            $results = $connection->query($sql);

            if ($results->num_rows > 0) {
                $sql = "UPDATE Parents SET 
                    Active=TRUE,
                    FirstName='". $firstName. "',
                    LastName='". $lastName. "',
                    Relationship='". $relationShip. "',
                    Email='". $email. "',
                    HomePhone='". $homePhone. "',
                    CellPhone='". $cellPhone. "' 
                    WHERE ParentID=". $data[PARENTID]. " AND StudentID=". $data[STUDENTID]. ";";
            } 
            else {
                $sql = "INSERT INTO Parents (Active,ParentID,StudentID,FirstName,LastName,Relationship,Email,HomePhone,CellPhone) 
                    VALUES (TRUE,".
                        $data[PARENTID]. ",".
                        $data[STUDENTID]. ",'".
                        $firstName. "','".
                        $lastName. "','".
                        $relationShip. "','".
                        $email. "','".
                        $homePhone. "','".
                        $cellPhone. "');";
            }

            $connection->query($sql);
        }
        fclose($file);
    }
    else {
        echo "Import file not found \"csv/parents.csv\"\n\r";
    }
}

function getMissingStudents($days) {

    // Get the list of students who haven't submitted today

    // Connect to the database
    $connection = db_connect();

    // Build the SQL string to get the results
    $sql = "SELECT Students.FirstName As StudentFirstName,
            Students.LastName As StudentLastName,
            Username,PWord,Parents.FirstName,Parents.LastName,Relationship,Parents.Email
        FROM Students 
        INNER JOIN Parents On Students.StudentID = Parents.StudentID
        WHERE DATEDIFF(NOW(),LastCheckin) > ". $days. " AND Parents.Email <> '' AND Students.Active=True 
        ORDER BY Students.id ASC";

    // Look up the data in the database
    $results = $connection->query($sql);

    // Return the results
    return $results;
}

function sendParentReminders($days) {

    // This will loop through all the parents and send them an email

    // Get the list of users from the database
    $results = getMissingStudents($days);

    while ($row=$results->fetch_assoc()) {
        if ($row['Email'] != '') {
            sendParentReminder($row,$days);
        }
    }
}

function sendParentReminder($data,$days) {

    // This will actually send the email to the parent

    // Get settings from config file to send email
    include 'config.php';
    $config = parse_ini_file($configFile); 
    $host = $config['host'];
    $smtpAuth = $config['smtpAuth'];
    $port = $config['port'];
    $recipients = explode(',',$config['mailRecipients']);
    $fromAddress = $config['fromAddress'];
    $fromName = $config['fromName'];

    // Set variables for email reading in the message body from a text file
    $subject = "COVID Screening For ". $data['StudentFirstName']. " ". $data['StudentLastName'];
    $message = file_get_contents("parents.txt");
    $message = str_replace("%FIRSTNAME%",$data['FirstName'],$message);
    $message = str_replace("%LASTNAME%",$data['LastName'],$message);
    $message = str_replace("%STUDENTFIRSTNAME%",$data['StudentFirstName'],$message);
    $message = str_replace("%STUDENTLASTNAME%",$data['StudentLastName'],$message);
    $message = str_replace("%USERNAME%",$data['Username'],$message);
    $message = str_replace("%PASSWORD%",$data['PWord'],$message);
    $message = str_replace("%DAYS%",$days,$message);
    
    // Create the mail object and set the values and send the message
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = $smtpAuth;
    $mail->Port = $port;
    $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        )
    );
    $mail->setFrom($fromAddress, $fromName);
    $mail->addAddress($data['Email']);  
    $mail->isHTML(true);
    $mail->Subject = "$subject";
    $mail->Body    = $message;
    $mail->send();

}

function getMissingResults(){

    // Get the list of people who haven't submitted today

    // Connect to the database
    $connection = db_connect();

    // Build the SQL string to get the results
    $sql = "SELECT UserName,FirstName,LastName,Email,PhoneNumber,UserType,Building
        FROM People 
        WHERE (LastCheckIn<CURDATE() OR LastCheckin IS NULL) AND Active=TRUE AND Building IS NOT NULL
        ORDER BY LastName,FirstName;";

    // Look up the data in the database
    $results = $connection->query($sql);

    // Return the results
    return $results;
}

function sendSummaryMessage($email) {

    // Get the list of users from the database
    $results = getMissingResults();

    // Loop through the users and add them to a string
    $people = "";
    while ($row=$results->fetch_assoc()) {
        $people .= $row['FirstName']. " ". $row['LastName']. "<br />";
    }

    // Get settings from config file to send email
    include 'config.php';
    $config = parse_ini_file($configFile); 
    $host = $config['host'];
    $smtpAuth = $config['smtpAuth'];
    $port = $config['port'];
    $recipients = explode(',',$config['mailRecipients']);
    $fromAddress = $config['fromAddress'];
    $fromName = $config['fromName'];

    // Set variables for email reading in the message body from a text file
    $subject = "COVID Screening Summary";
    $message = file_get_contents("summary.txt");
    $message = str_replace("%LIST%",$people,$message);
    
    // Create the mail object and set the values and send the message
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = $smtpAuth;
    $mail->Port = $port;
    $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        )
    );
    $mail->setFrom($fromAddress, $fromName);
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "$subject";
    $mail->Body    = $message;
    $mail->send();
}

function sendMissingMessages() {

    // Get the list of users from the database
    $results = getMissingResults();

    // Loop through the users and send each of them a message
    while ($row=$results->fetch_assoc()) {
        sendMissingMessage($row['FirstName'],$row['Email']);
    }
}

function sendMissingMessage($firstName, $email){

    // Get settings from config file to send email
    include 'config.php';
    $config = parse_ini_file($configFile); 
    $host = $config['host'];
    $smtpAuth = $config['smtpAuth'];
    $port = $config['port'];
    $recipients = explode(',',$config['mailRecipients']);
    $fromAddress = $config['fromAddress'];
    $fromName = $config['fromName'];

    // Set variables for email reading in the message body from a text file
    $subject = "COVID Screening";
    $message = file_get_contents("missing.txt");
    $message = str_replace("%FIRSTNAME%",$firstName,$message);
    
    // Create the mail object and set the values and send the message
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = $smtpAuth;
    $mail->Port = $port;
    $mail->SMTPOptions = array(
        'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
        )
    );
    $mail->setFrom($fromAddress, $fromName);
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = "$subject";
    $mail->Body    = $message;
    $mail->send();
  }

function sendMissingSMSs() {

        // Get the list of users from the database
        $results = getMissingResults();

        // Loop through the users and send each of them a message
        while ($row=$results->fetch_assoc()) {
            sendMissingSMS($row['FirstName'],$row['PhoneNumber']);
        }
}

function sendMissingSMS($firstName, $cellPhone) {

    // Get settings from config file to send the SMS message
    include 'config.php';
    $config = parse_ini_file($configFile); 

    // Set the settings needed to send the SMS messages
    $accountSID = $config['accountSID'];;
    $authToken = $config['authToken'];;
    $smsNumber = $config['smsNumber'];;

    // Get the message from the file
    $message = file_get_contents("employees-sms.txt");
    $message = str_replace("%FIRSTNAME%",$firstName,$message);

    $client = new Client($accountSID, $authToken);
    $client->messages->create(
        "+1". $cellPhone,
        array(
            'from' => $smsNumber,
            'body' => $message
        )
    );
}

?>