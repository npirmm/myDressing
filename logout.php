<?php
session_start();
require_once 'includes/logs.php';

if (isset($_SESSION['user_id'])) {
    $logs = new Logs();
    $logs->logAction($_SESSION['user_id'], 'logout', 'User logged out.');
}

session_destroy();
header('Location: index.php');
exit();
?>
