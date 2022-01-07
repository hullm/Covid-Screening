<?php 

// 
if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] > 4*60*60)) { // Four hours 
    header("Location: index.php?logout");
}
$_SESSION['lastActivity'] = time();

// Show the typewriter and reCAPTCHA on the login page, and remove the logout.
if (isset($_GET['login'])) {
    $showLogout="False";
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
    $showLogout="True";
    $onLoad="";
    $showTypewriter="";
    $reCAPTCHA="";
}

// Kick them out if they aren't an admin and they try to get to an admin page.
if ((isset($_GET['charts']) ||
    isset($_GET['reports']) ||
    isset($_GET['missing']) ||
    isset($_GET['setup'])) &&
    $_SESSION['userType'] != "Admin") {
        header("Location: index.php?logout");
}

// Only show the reports button if they are an admin
if (isset($_SESSION['userType']) && $_SESSION['userType'] == "Admin"){
    $isAdmin="True";
}
else{
    $isAdmin="False";
}

// Set the active page in the nav bar
$navHomeActive = "";
$navHomeSpan = "";
$navReportsActive = "";
$navReportsSpan = "";
$navMissingActive = "";
$navMissingSpan = "";
if (isset($_GET['reports'])) {
    $navReportsActive = "active";
    $navReportsSpan = "<span class=\"sr-only\">(current)</span>";
}
elseif (isset($_GET['missing'])) {
    $navMissingActive = "active";
    $navMissingSpan = "<span class=\"sr-only\">(current)</span>";
}
elseif (isset($_GET['missingstudents'])) {
    $navMissingActive = "active";
    $navMissingSpan = "<span class=\"sr-only\">(current)</span>";
}
else {
    $navHomeActive = "active";
    $navHomeSpan = "<span class=\"sr-only\">(current)</span>";
}
?>

<!doctype html>
<html lang="en">

<head>
    <title><?php echo str_replace("'","\'",$config['title']); ?></title>
    <meta charset="utf-8">
    <link rel="apple-touch-icon" href="images/apple-touch-icon.png">
    <link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
    <link rel="stylesheet" href="assets/bootstrap.min.css">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/fontawesome.css">
    <script src="assets/jquery-3.5.1.slim.min.js"></script>
    <script src="assets/jquery-3.5.1.js"></script>
    <script src="assets/popper.min.js"></script>
    <script src="assets/bootstrap.min.js"></script>
    <script src="assets/jquery.dataTables.min.js"></script>
    <script src="assets/datatables.min.js"></script>
    <link rel="stylesheet" href="assets/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="assets/buttons.bootstrap4.css">
    <script type="text/javascript" src="assets/buttons.bootstrap4.js"></script>
    <script type="text/javascript" src="assets/buttons.html5.js"></script>
    <script type="text/javascript" src="assets/buttons.colVis.min.js"></script>
    <link rel="stylesheet" href="assets/Chart.min.css">
    <script src="assets/Chart.min.js"></script>
    <script src="assets/chartjs-plugin-labels.js"></script>
    <?php echo $reCAPTCHA; ?>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<head>

<body id="main" <?php echo $onLoad; ?>>

<!-- Logo -->
<nav class="navbar navbar-expand-sm navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><strong><?php echo str_replace("'","\'",$config['title']); ?></strong></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item <?php echo $navHomeActive;?>">
                    <a class="nav-link" href="index.php">Home <?php echo $navHomeSpan; ?></a>
                </li>
        <?php   if($isAdmin == "True") {?>
                <li class="nav-item <?php echo $navReportsActive;?>">
                    <a class="nav-link" href="index.php?reports">Reports <?php echo $navReportsSpan; ?></a>
                </li>
                <li class="dropdown nav-item <?php echo $navMissingActive;?>"><a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#">Missing<span class="caret"></span> <?php echo $navMissingSpan; ?></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="index.php?missing">Employees</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="index.php?missingstudents">Students</a>
                        </li>
                    </ul>
                </li>
        <?php   } 
                if ($showLogout == "True") { ?>
                <li class="nav-item">
                    <a class="nav-link" href="index.php?logout">Logout</a>
                </li>
        <?php   } ?>
            </ul>
        <?php if ($showLogout == "True") { ?>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <div class="nav-link"><i class="fas fa-user"></i> &nbsp;<?php echo $_SESSION["firstName"]. " ". $_SESSION["lastName"]; ?></div>
                </li>
            </ul>
        <?php   } ?>
        </div>
        
    </div>
</nav>

<div class ="container-fluid">
    <div class="d-flex justify-content-center">
        <div class="p-2">
            <?php echo $showTypewriter; ?>
        </div>
    </div>
</div>