<div class="container">
    <div class="row justify-content-center" style="padding-top:4%;">
        <div class="col-16 text-center">
            <div class="setupMessage">

<?php
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
    Email VARCHAR(50),
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
    Email VARCHAR(50),
    PhoneNumber VARCHAR(30),
    LastCheckin DATE
    )";
if ($connection->query($sql) === TRUE) {
    echo "People table created...<br />";
} else {
    echo "People table already exists...<br />";
}

// Create the students table
$sql = "CREATE TABLE Students (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Active BOOLEAN,
    StudentID INT(10) UNSIGNED,
    FirstName VARCHAR(30) NOT NULL,
    LastName VARCHAR(30) NOT NULL,
    Building VARCHAR(30) NOT NULL,
    Grade VARCHAR(30) NOT NULL,
    UserName VARCHAR(30),
    PWord VARCHAR(30),
    Email VARCHAR(50),
    PhoneNumber VARCHAR(30),
    LastCheckin DATE
    )";
if ($connection->query($sql) === TRUE) {
    echo "Students table created...<br />";
} else {
    echo "Students table already exists...<br />";
}

// Create the parents table
$sql = "CREATE TABLE Parents (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Active BOOLEAN,
    ParentID INT(10) UNSIGNED,
    StudentID INT(10) UNSIGNED,
    FirstName VARCHAR(30) NOT NULL,
    LastName VARCHAR(30) NOT NULL,
    Relationship VARCHAR(30),
    Email VARCHAR(50),
    HomePhone VARCHAR(30),
    CellPhone VARCHAR(30)
    )";
if ($connection->query($sql) === TRUE) {
    echo "Parents table created...<br />";
} else {
    echo "Parents table already exists...<br />";
}

// Add the building column to the people table
$sql = "ALTER TABLE People ADD COLUMN Building VARCHAR(30);";
if ($connection->query($sql) === TRUE) {

    // Get their building information from the tracking table and add it to the people table
    $sql = "SELECT UserName FROM People";
    $results = $connection->query($sql);

    if ($results->num_rows > 0) {
        while ($row=$results->fetch_assoc()) {
            $sql = "SELECT Building FROM Tracking WHERE UserName='". $row['UserName']. "' ORDER BY id DESC;";
            $buildingResults = $connection->query($sql);

            if ($results->num_rows > 0) {
                $building=$buildingResults->fetch_assoc();
                $sql = "UPDATE People SET Building='". $building['Building']. "' WHERE UserName='". $row['UserName']. "'";
            }
            else {
                $sql = "UPDATE People SET Building='' WHERE UserName='". $row['UserName']. "'";
            }
            $connection->query($sql);
        }
    }

    echo "Added Building column to People table...<br />";
} else {
    echo "Building Column already exists in People table...<br />";
}

// Add the active column to the people table
$sql = "ALTER TABLE People ADD COLUMN Active BOOLEAN;";
if ($connection->query($sql) === TRUE) {
    
    // Set all the users as active
    $sql = "UPDATE People SET Active=True;";
    $connection->query($sql);
    echo "Added Active column to People table...<br />";
} else {
    echo "Active Column already exists in People table...<br />";
}

// Add the last time the students checked in to the student table
$sql = "UPDATE Students SET LastCheckin='1978-06-16'";
$connection->query($sql);
$sql = "SELECT Username,DateSubmitted FROM Tracking WHERE UserType='Student';";
$results = $connection->query($sql);
if ($results->num_rows > 0) {
    while ($row=$results->fetch_assoc()) {
        $sql = "UPDATE Students SET LastCheckin='". $row['DateSubmitted']. "' WHERE UserName='". $row['Username']. "';";
        $connection->query($sql);
    }
}
echo "Last checkin dates added to students table...<br />";

// Remove Students from the People table
$sql = "DELETE FROM People WHERE UserType='Student';";
$connection->query($sql);
echo "Students removed from the people table...<br />";

// Change all adults to employees in the database
$sql = "UPDATE Tracking SET UserType='Employee' WHERE UserType='Adult';";
$connection->query($sql);
$sql = "UPDATE People SET UserType='Employee' WHERE UserType='Adult';";
$connection->query($sql);
echo "Adults changed to Employees in the people table...<br />";

// Disable the ONLY_FULL_GROUP_BY option in SQL
$sql = "SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));";
$connection->query($sql);
echo "Fixed Missing People Query...<br />";

// Close the connection to the database
$connection->close();

echo "Database upgraded successfully <br />";

?>
<a href="index.php">View the site</a>
</div>
</div>
</div>
</div>