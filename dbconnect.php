<?php
    // Check if constants are already defined before defining them
    if (!defined('HOSTNAME')) {
        define('HOSTNAME', 'localhost');
    }

    if (!defined('USERNAME')) {
        define('USERNAME', 'root');
    }

    if (!defined('PASSWORD')) {
        define('PASSWORD', '');
    }

    if (!defined('DATABASE')) {
        define('DATABASE', 'crafthub_db');
    }

    $connection = mysqli_connect(HOSTNAME, USERNAME, PASSWORD, DATABASE);


    if (!$connection) {
        die("Connection Failed");
    }
?>