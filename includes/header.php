<?php 

// Show the typewriter and reCAPTCHA on the login page, and remove the logout.
if (basename($_SERVER['PHP_SELF']) == "login.php") {
    //$showLogout="visibility:hidden;";
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

// Kick them out if they aren't an admin and they try to get to reports or setup.
if ((basename($_SERVER['PHP_SELF']) == "reports.php") && $_SESSION['userType'] != "Admin") {
    header("Location: index.php?logout");
}
if ((basename($_SERVER['PHP_SELF']) == "setup.php") && $_SESSION['userType'] != "Admin") {
    header("Location: index.php?logout");
}

// Only show the reports button if they are an admin
if (isset($_SESSION['userType']) && $_SESSION['userType'] == "Admin"){
    // $showReports="";
    // $showMissing="";
    $isAdmin="True";
}
else{
    // $showReports="visibility:hidden;";
    // $showMissing="visibility:hidden;";
    $isAdmin="False";
}

// Set the active page in the nav bar
$navHomeActive = "";
$navHomeSpan = "";
$navReportsActive = "";
$navReportsSpan = "";
$navMissingActive = "";
$navMissingSpan = "";
switch (basename($_SERVER['PHP_SELF'])) {
    case "index.php":
        $navHomeActive = "active";
        $navHomeSpan = "<span class=\"sr-only\">(current)</span>";
        break;
    case "reports.php":
        $navReportsActive = "active";
        $navReportsSpan = "<span class=\"sr-only\">(current)</span>";
        break;
    case "missing.php":
        $navMissingActive = "active";
        $navMissingSpan = "<span class=\"sr-only\">(current)</span>";
        break;
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
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.bootstrap4.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.colVis.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <?php echo $reCAPTCHA; ?>
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="apple-mobile-web-app-capable" content="yes" />
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
                    <a class="nav-link" href="reports.php">Reports <?php echo $navReportsSpan; ?></a>
                </li>
                <li class="nav-item <?php echo $navMissingActive;?>">
                    <a class="nav-link" href="missing.php">Missing <?php echo $navMissingSpan; ?></a>
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