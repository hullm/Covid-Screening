<?php
include 'includes/config.php';
include 'includes/functions.php';
include 'includes/submit.php';
include 'includes/header.php';
include 'includes/footer.php';

// Show the login page
if(isset($_GET['login'])) {
    include 'includes/login.php';
}

// Show the charts page if they're an admin and the page is requested
elseif(isset($_GET['charts']) && $_SESSION['userType'] == "Admin"){
    include 'includes/charts.php';
}

// Show the reports page if they're an admin and the page is requested
elseif(isset($_GET['reports']) && $_SESSION['userType'] == "Admin"){
    include 'includes/reports.php';
}

// Show the missing page if they're an admin and the page is requested
elseif(isset($_GET['missing']) && $_SESSION['userType'] == "Admin"){
    include 'includes/missing.php';
}
elseif(isset($_GET['missingstudents']) && $_SESSION['userType'] == "Admin"){
    include 'includes/missingstudents.php';
}

// Show the setup page if they're an admin and the page is requested
elseif((isset($_GET['setup']) || isset($_GET['upgrade'])) && $_SESSION['userType'] == "Admin"){
    include 'includes/setup.php';
}

// Show the contact page if they're an admin and the page is requested
elseif(isset($_GET['contact']) && $_SESSION['userType'] == "Admin"){
    include 'includes/contact.php';
}

// If the user is visiting for the first time today we'll show 
// them the form, otherwise we'll show them their result.
else {
    if (alreadySubmitted($currentUser)) {
        if (isset($_SESSION['userType']) && $_SESSION['userType'] == "Admin") {
            include 'includes/charts.php';
        }
        else {
            if (getUserResults($currentUser)) {
                include 'includes/allow.php';
            } else {
                include 'includes/deny.php';
            }
        }
    } else {
        include 'includes/form.php';
    }
}
?>