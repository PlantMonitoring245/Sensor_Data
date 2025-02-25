<?php
// Database Connection
$servername = "localhost";
$username = "root";  // Change if needed
$password = "";      // Change if needed
$database = "plant_monitor_db";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed!"]));
}

// Fetch latest sensor data
$sql = "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$data = $result->fetch_assoc() ?: ["moisture" => "N/A", "temperature" => "N/A", "humidity" => "N/A", "motion" => "N/A"];
$conn->close();

// Handle AJAX Request
if (isset($_GET['ajax'])) {
    echo json_encode($data);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Monitoring Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #eef7f9; }
        .navbar { background: linear-gradient(to right, #2E7D32, #66BB6A); padding: 15px; }
        .navbar-brand { color: white; font-size: 28px; font-weight: bold; }
        .container { margin-top: 40px; }
        .card {
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
            text-align: center;
            background: white;
            transition: all 0.3s ease-in-out;
        }
        .card:hover { transform: translateY(-10px); }
        h2 { font-size: 24px; font-weight: bold; }
        p { font-size: 26px; font-weight: bold; }
        .motion-active { color: green; font-weight: bold; animation: blink 1s infinite alternate; }
        .motion-inactive { color: red; font-weight: bold; }
        @keyframes blink {
            0% { opacity: 1; }
            100% { opacity: 0.5; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <i class="fas fa-seedling"></i> Plant Monitoring Dashboard
        </a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center g-4">
        <div class="col-md-3">
            <div class="card">
                <h2>Soil Moisture</h2>
                <canvas id="moistureGauge"></canvas>
                <p id="moistureText"><?php echo $data["moisture"]; ?>%</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h2>Temperature</h2>
                <p id="temperature" style="color: red;"><?php echo $data["temperature"]; ?>°C</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h2>Humidity</h2>
                <p id="humidity" style="color: blue;"><?php echo $data["humidity"]; ?>%</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <h2>Motion Sensor</h2>
                <p id="motion">
                    <i class="fas fa-running motion-active"></i> <?php echo ($data["motion"] == 1) ? "Motion Detected" : "No Motion"; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    let moistureChart;
    function fetchData() {
        fetch("index.php?ajax=1")
            .then(response => response.json())
            .then(data => {
                document.getElementById("moistureText").innerText = data.moisture + "%";
                document.getElementById("temperature").innerText = data.temperature + "°C";
                document.getElementById("humidity").innerText = data.humidity + "%";
                document.getElementById("motion").innerHTML =
                    data.motion == 1 
                    ? '<i class="fas fa-running motion-active"></i> Motion Detected' 
                    : '<i class="fas fa-user-slash motion-inactive"></i> No Motion';
                drawMoistureGauge(data.moisture);
            })
            .catch(error => console.error("Error fetching data:", error));
    }

    function drawMoistureGauge(moisture) {
        let ctx = document.getElementById("moistureGauge").getContext("2d");
        moisture = parseInt(moisture) || 0;
        if (moistureChart) moistureChart.destroy();
        moistureChart = new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: ["Moisture", "Empty"],
                datasets: [{
                    data: [moisture, 100 - moisture],
                    backgroundColor: ["#4CAF50", "#ddd"],
                }]
            },
            options: { cutout: "70%" }
        });
    }
    setInterval(fetchData, 5000);
    drawMoistureGauge(<?php echo $data["moisture"]; ?>);
</script>

</body>
</html>
