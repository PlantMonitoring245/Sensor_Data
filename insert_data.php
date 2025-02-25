<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/x-www-form-urlencoded");

// Database Connection
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";      // Change if needed
$database = "plant_monitor_db"; // ✅ Updated database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get data from ESP32
$lux = $_POST['lux'];
$motion = $_POST['motion'];
$moisture = $_POST['moisture'];
$humidity = $_POST['humidity'];
$temperature = $_POST['temperature'];

// Insert Data
$sql = "INSERT INTO sensor_data (lux, motion, moisture, humidity, temperature) 
        VALUES ('$lux', '$motion', '$moisture', '$humidity', '$temperature')";

if ($conn->query($sql) === TRUE) {
    echo "Data inserted successfully";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
