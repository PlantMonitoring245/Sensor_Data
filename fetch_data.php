<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "plant_monitor_db"; // ✅ Updated database name

$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Fetch Data
$sql = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 25";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sensor Data Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {background-color: #f2f2f2;}
    </style>
</head>
<body>

<h2>Sensor Data Table</h2>

<table>
    <tr>
        <th>ID</th>
        <th>Light Intensity (lux)</th>
        <th>Motion</th>
        <th>Soil Moisture (%)</th>
        <th>Humidity (%)</th>
        <th>Temperature (°C)</th>
        <th>Timestamp</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['lux']}</td>
                    <td>" . ($row['motion'] ? "Detected" : "No Motion") . "</td>
                    <td>{$row['moisture']}%</td>
                    <td>{$row['humidity']}%</td>
                    <td>{$row['temperature']}°C</td>
                    <td>{$row['timestamp']}</td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No Data Available</td></tr>";
    }
    $conn->close();
    ?>
</table>

</body>
</html>
