<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'sql105.infinityfree.com';
$user = 'if0_41435947';      // change if needed
$pass = 'Ammanaina2004';          // change if needed
$db   = 'if0_41435947_agronexus';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');

require_once __DIR__ . '/functions.php';
?>