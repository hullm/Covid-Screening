<?php
include 'includes/config.php';
include 'includes/functions.php';

// Create connection to the database server, not the database
$conn = new mysqli($config['servername'],$config['username'],$config['password']);

// Verify the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create the covid database
$sql = "CREATE DATABASE ". $config['dbname']. ";";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully...<br />";
} else {
    echo "Database already exists...<br />";
}

// Close the connection to the database server
$conn->close();

// Connect to the database
$connection = db_connect();

// Create the tracking table
$sql = "CREATE TABLE Tracking (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(30) NOT NULL,
    LastName VARCHAR(30) NOT NULL,
    UserName VARCHAR(30),
    UserType VARCHAR(30),
    Email VARCHAR(30),
    PhoneNumber VARCHAR(30),
    Building VARCHAR(30),
    HasPassed BOOLEAN,
    DateSubmitted DATE,
    TimeSubmitted TIME
    )";
if ($connection->query($sql) === TRUE) {
    echo "Tracking table created...<br />";

} else {
    echo "Tracking table already exists...<br />";
}

// Create the people table
$sql = "CREATE TABLE People (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(30) NOT NULL,
    LastName VARCHAR(30) NOT NULL,
    UserName VARCHAR(30),
    UserType VARCHAR(30),
    Email VARCHAR(30),
    PhoneNumber VARCHAR(30),
    LastCheckin DATE
    )";
if ($connection->query($sql) === TRUE) {
    echo "People table created...<br />";

} else {
    echo "People table already exists...<br />";
}

// Change all adults to employees in the database
$sql = "UPDATE Tracking SET UserType='Employee' WHERE UserType='Adult';";
$connection->query($sql);
$sql = "UPDATE People SET UserType='Employee' WHERE UserType='Adult';";
$connection->query($sql);

// Close the connection to the database
$connection->close();

echo "Database upgraded successfully <br />";

?>
<a href="login.php">View the site</a>