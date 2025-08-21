<?php
/*config.php — ضعها في جذر المشروع
session_start();

$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'shopping_db';
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) die("DB Conn Error: " . $conn->connect_error);
$conn->set_charset('utf8mb4');

// helper: safe redirect
function redirect($url) {
    header("Location: $url");
    exit;
}
?>
*/


$conn = new mysqli("localhost", "root", "", 'shopping_db');

if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}
