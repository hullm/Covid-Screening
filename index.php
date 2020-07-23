<?php
include 'includes/config.php';
include 'includes/functions.php';
include 'includes/submit.php';
include 'includes/header.php';

// If the user is visiting for the first time today we'll show 
// them the form, otherwise we'll show them their result.
if (alreadySubmitted($currentUser)) {
    if (getUserResults($currentUser)) {
        include 'includes/allow.php';
    } else {
        include 'includes/deny.php';
    }
} else {
    include 'includes/form.php';
}
include 'includes/footer.php';
?>