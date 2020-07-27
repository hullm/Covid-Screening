<?php
include 'includes/functions.php';

if(isset($_GET['GetLatestEntry'])){
    echo GetLatestEntry();
}
?>