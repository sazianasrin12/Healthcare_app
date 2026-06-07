<?php
$host = 'localhost';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("<h3 style='color:red'> Connection Failed</h3><p>Ensure XAMPP MySQL is running.</p>");
}

$sql = "CREATE DATABASE IF NOT EXISTS digital_healthcare";
$conn->query($sql);
$conn->select_db('digital_healthcare');

$sqlFile = __DIR__ . '/sql/install.sql';
if (file_exists($sqlFile)) {
    $sqlContent = file_get_contents($sqlFile);
    if ($conn->multi_query($sqlContent)) {
        do {
            if ($result = $conn->store_result()) { $result->free(); }
        } while ($conn->more_results() && $conn->next_result());
        
        echo "<h2 style='color:#10b981; font-family:sans-serif;'>✅ Installation Successful!</h2>";
        echo "<p style='font-family:sans-serif;'>Your database is ready. For security, please delete <code>setup.php</code>.</p>";
        echo "<a href='modules/public/login.php' style='display:inline-block; padding:10px 20px; background:#0ea5e9; color:white; text-decoration:none; border-radius:6px; font-family:sans-serif;'>Go to Login Page</a>";
    } else {
        echo "<h3 style='color:red'>Error: " . $conn->error . "</h3>";
    }
} else {
    echo "<h3 style='color:red'>SQL file not found.</h3>";
}
$conn->close();
?>
