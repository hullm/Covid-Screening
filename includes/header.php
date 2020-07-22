<?php 

// Show the typewriter and reCAPTCHA on the login page, and remove the logout.
if (basename($_SERVER['PHP_SELF']) == "login.php") {
    $showLogout="visibility:hidden;";
    $onLoad="onload=typeWriter()";
    $showTypewriter="<p id='subtitle' style='color:white; text-shadow:2px 2px 5px black;'></p>";
    if ($config['sitekey']!='') {
        $reCAPTCHA="<script src=\"https://www.google.com/recaptcha/api.js?render=". $config['sitekey']. "\"></script>";
    } 
    else {
        $reCAPTCHA="";
    }
}
else{
    $showLogout="";
    $onLoad="";
    $showTypewriter="";
    $reCAPTCHA="";
}

// Kick them out if they aren't an admin and they try to get to reports or setup.
if ((basename($_SERVER['PHP_SELF']) == "reports.php") && $_SESSION['userType'] != "Admin") {
    header("Location: index.php?logout");
}
if ((basename($_SERVER['PHP_SELF']) == "setup.php") && $_SESSION['userType'] != "Admin") {
    header("Location: index.php?logout");
}

// Only show the reports button if they are an admin
if (isset($_SESSION['userType']) && $_SESSION['userType'] == "Admin"){
    $showReports="";
    $showMissing="";
}
else{
    $showReports="visibility:hidden;";
    $showMissing="visibility:hidden;";
}
?>

<!doctype html>
<html lang="en">

<head>
    <title><?php echo str_replace("'","\'",$config['title']); ?></title>
    <meta charset="utf-8">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/v/bs4/dt-1.10.21/b-1.6.2/b-colvis-1.6.2/b-html5-1.6.2/datatables.min.js"></script>
    <link rel="stylesheet" href="assets/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.bootstrap4.css">
    <ling rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.dataTables.css">
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.bootstrap4.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.colVis.min.js"></script>
    <?php echo $reCAPTCHA; ?>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
<head>

<body id="main" <?php echo $onLoad; ?>>

<!-- Logo -->
<div class ="container-fluid">
    <div class="d-flex justify-content-between">
        <div class="p-2">
            <a href="index.php"><img src="/images/logo.png"></a>
        </div>
        
        <div class="p-2">
            <h1 style="color:white; padding-left:20px;"><?php echo str_replace("'","\'",$config['title']); ?></h1>
            <?php echo $showTypewriter; ?>
        </div>
        <div class="p-2" style="padding-right: 20px !important;">
            <a href="missing.php" data-toggle="tooltip" title="View Missing Check Ins"><i class="fas fa-ghost" style="font-size:36px; color:white; padding-right:20px; <?php echo $showMissing; ?>"></i></a>
            <a href="reports.php" data-toggle="tooltip" title="View Reports"><i class="fas fa-list-alt" style="font-size:36px; color:white; padding-right:20px; <?php echo $showReports; ?>"></i></a>
            <a href="index.php?logout" data-toggle="tooltip" title="Log Out"><i class="fas fa-sign-out-alt" style="font-size:36px; color:white; <?php echo $showLogout; ?>"></i></a>
        </div>
    </div>
</div>