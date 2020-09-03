<?php

include "config.php";
include "functions.php";

// See if they passed an argument, if so do what they asked for, if not let them know.
if (isset($argv[1])) {
    switch (strtolower($argv[1])) {
        case "missing":
            sendMissingMessages();
            break;

        case "summary":
            if (isset($argv[2])) {
                sendSummaryMessage($argv[2]);
            }
            else {
                echo "Missing email address...\n\r";
            }
            break;

        default:
            echo "Invalid Argument...\n\r";
    }
}
else {
    echo "Missing Argument...\n\r";
}

?>