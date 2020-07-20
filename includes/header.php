<?php 

// Purge old data
purgeOldData(120);

// Add javascript to the body tag if needed
if (basename($_SERVER['PHP_SELF']) == "login.php") {
    $showLogout="visibility:hidden;";
    $onLoad="onload=typeWriter()";
    $showTypewriter="<p id='subtitle' style='color:white; text-shadow:2px 2px 5px black;'></p>";
}
else{
    $showLogout="";
    $onLoad="";
    $showTypewriter="";
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
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
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
            <h1 style="color:white;"><?php echo str_replace("'","\'",$config['title']); ?></h1>
            <?php echo $showTypewriter; ?>
        </div>
        <div class="p-2" style="padding-right: 20px !important;">
            <a href="index.php?logout"><i class="fas fa-sign-out-alt" style="font-size:36px; color:white; <?php echo $showLogout; ?>"></i></a>
        </div>
    </div>
</div>
