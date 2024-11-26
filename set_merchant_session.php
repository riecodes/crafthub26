<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id'])) {
    $_SESSION['application_id'] = $_POST['application_id'];
    echo 'Session set';
} else {
    echo 'Error setting session';
}
?>
