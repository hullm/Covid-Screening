<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '/var/www/PHPMailer/src/Exception.php';
require '/var/www/PHPMailer/src/PHPMailer.php';
require '/var/www/PHPMailer/src/SMTP.php';

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

function getMissingResults(){

    // Get the list of people who haven't submitted today

    // Connect to the database
    $connection = db_connect();

    // Build the SQL string to get the results
    $sql = "SELECT People.UserName,People.FirstName,People.LastName,People.Email,People.PhoneNumber,People.UserType,Tracking.Building
        FROM People INNER JOIN Tracking ON People.UserName=Tracking.UserName
        WHERE People.LastCheckIn<CURDATE() AND Tracking.Building IS NOT NULL AND (People.UserType='Employee' OR People.UserType='Admin')
        GROUP BY People.UserName ORDER BY People.LastName, People.FirstName;";

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

?>