<?php
$host = 'localhost';
$user = 'root';
$password = ''; // XAMPP default empty password
$database = 'digital_healthcare';

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Database connection failed. Please ensure XAMPP MySQL is running and you have visited <code>setup.php</code> first.");
}
$conn->set_charset("utf8mb4");
?>
