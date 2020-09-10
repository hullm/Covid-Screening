<?php

// Get the values from the URL
$userName = "";
if (isset($_GET['username'])) {
    $userName = $_GET["username"];
} 

if ($userName != "") {

    // Get the contact info fand student info from the database
    $contactInfo = getContactInfo($userName);
    $studentInfo = getStudentInfo($userName);
    $studentInfo = $studentInfo->fetch_assoc();
    $studentID = $studentInfo['StudentID'];

    // Get the student image if available
    if (isset($config['studentPhotos'])) {
        if (checkRemoteFile($config['studentPhotos']. "/". $studentID. ".". $config['photoFormat'])) {
            $studentImage = "<img src=\"". $config['studentPhotos']. "/". $studentID. ".". $config['photoFormat']. "\" title=\"". $studentID. "\" />";
        }
        else {
            $studentImage =  "<img src=\"images/student.png\" title=\"". $studentID. "\" />";
        }
    }
    else {
        $studentImage =  "<img src=\"images/student.png\" title=\"". $studentID. "\" />";
    }

    // Build the return link
    if (isset($_SERVER['HTTP_REFERER'])) {
        $returnLink = "<a href=\"". $_SERVER['HTTP_REFERER']. "\">
            <img src=\"images/back.png\" title=\"Return to List\" width=\"20\" height=\"20\" /></a>";
    }
    else {
        $returnLink = "";
   }
}
?>

<div class="container" id="reportsForm">
    <div class="form-row justify-content-around">
        <div class="col-md-3 mb-5">
            <?php echo $studentImage. "<br />". $returnLink; ?>

        </div>
        <div class="col-md-7 mb-5">
            <h3><?php echo $studentInfo['FirstName']. " ". $studentInfo['LastName']; ?></h1>
            <p>
                <i><?php echo $studentInfo['Grade'];?></i><br />
                <b>Building</b>: <?php echo $studentInfo['Building'];?><br />
                <b>Username</b>: <?php echo $studentInfo['UserName'];?><br />
                <b>Password</b>: <?php echo $studentInfo['PWord'];?><br />
            </p>
            <?php while ($row=$contactInfo->fetch_assoc()){ ?>
                    <b><?php echo $row['Relationship']. "</b>: ". $row['FirstName']. " ". $row['LastName'] ;?>
                    <li>Home Phone: <?php echo formatPhoneNumber($row['HomePhone']);?></li>
                    <li>Cell Phone: <?php echo formatPhoneNumber($row['CellPhone']);?></li>
                    <li>Email Address: <?php echo $row['Email'];?></li>
                    <br />
            <?php }?>
        </div>
    </div>
</div>
