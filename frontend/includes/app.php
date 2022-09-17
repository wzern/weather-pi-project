<?php
// Define variables
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "weather-pi-project";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Execute SQL command
// $sql = "SELECT * FROM sensor_data_alt";
$sql = "SELECT * FROM `sensor_data_alt` WHERE time >= NOW() - INTERVAL 1 DAY";
$result = $conn->query($sql);

// Close Connection
$conn->close();

// Extract data into arrays if data exists
if ($result->num_rows > 0) {
    $temperatureDataArr = array();
    $humidityDataArr = array();
    $pressureDataArr = array();
    $timestampArr = array();

    // output data of each row
    while($row = $result->fetch_assoc()) {
        $temperatureDataArr[] = $row['temperature'];
        $humidityDataArr[] = $row['humidity'];
        $pressureDataArr[] = $row['pressure'];
        $timestampArr[] = $row['time'];
    }
} else {
    echo "0 results";
}
?> 

<!-- Set javascript data arrays -->
<script>
    // Setup Block
    const temperatureDataArr = <?php echo json_encode($temperatureDataArr) ?>;
    const humidityDataArr = <?php echo json_encode($humidityDataArr) ?>;
    const pressureDataArr = <?php echo json_encode($pressureDataArr) ?>;
    const timestampArr = <?php echo json_encode($timestampArr) ?>;
</script>