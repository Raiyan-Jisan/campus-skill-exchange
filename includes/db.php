<?php
/**
 * Database Connection using PDO
 * Course: CSE 472 Web and Internet Programming Lab
 * Reference: Lab Manual 07 (MySQL and PHP Database Connection)
 */

$host = 'localhost';
$dbname = 'cse472_skill_exchange';
$username = 'root';
$password = ''; // Default password in XAMPP is empty

try {
    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // Helpful error message for students running locally
    die("Database Connection Error: " . htmlspecialchars($e->getMessage()) . "<br><br>
        <strong>Setup Tip:</strong> Please ensure MySQL is running in your XAMPP Control Panel and that you have imported <code>database/setup.sql</code> in phpMyAdmin.");
}
?>
