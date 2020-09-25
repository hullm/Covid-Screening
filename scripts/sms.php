<?php

include "config.php";
include "functions.php";

// See if they passed an argument, if so do what they asked for, if not let them know.
if (isset($argv[1])) {
    switch (strtolower($argv[1])) {
        case "missing":
            sendMissingSMSs();
            break;
        
        default:
            echo "Invalid Argument...\n\r";
    }
}
else {
    echo "Missing Argument...\n\r";
}

?>