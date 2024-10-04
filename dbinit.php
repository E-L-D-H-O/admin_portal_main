<?php
$servername = "localhost";
$username = "root"; 
$password = "";      
$dbname = "ecommerce_db";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS ecommerce_db";
if ($conn->query($sql) === TRUE) {
    echo "<script>console.log('Database created successfully');</script>";
} else {
    echo "<script>console.log('Error creating database: " . addslashes($conn->error) . "');</script>";
}

// Select the newly created database
$conn->select_db($dbname);

// SQL to create the table 'smartphones' if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS smartphones (
    SmartphoneID INT(11) AUTO_INCREMENT PRIMARY KEY,
    SmartphoneName VARCHAR(100) NOT NULL,
    Description TEXT NOT NULL,
    QuantityAvailable INT(11) NOT NULL,
    Price DECIMAL(10,2) NOT NULL,
    ProductAddedBy VARCHAR(100) NOT NULL DEFAULT 'Eldho Shaju',
    LaunchDate DATE
)";

if ($conn->query($sql) === TRUE) {
    echo "<script>console.log('Table smartphones created successfully');</script>";
} else {
    echo "<script>console.log('Error creating Table: " . addslashes($conn->error) . "');</script>";
}
?>