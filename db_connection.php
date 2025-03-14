


<?php
$host = 'localhost';
$dbname = 'book_store_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Database connection successful!<br>"; // Debugging
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>

