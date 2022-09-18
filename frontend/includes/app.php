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
$sqlNode = "SELECT DISTINCT node_id FROM `sensor_data_alt`";
$resultNode = $conn->query($sqlNode);

// Extract data into arrays if data exists
if ($resultNode->num_rows > 0) {
    $nodeIDArr = array();

    // output data of each row
    while($row = $resultNode->fetch_assoc()) {
        $nodeIDArr[] = $row['node_id'];
    }
} else {
    echo "No Nodes";
}

$defaultNodeID = (isset($nodeIDArr[0])) ? $nodeIDArr[0] : "Default";
$activeNodeID = (!empty($_GET['node'])) ? $_GET['node'] : $defaultNodeID;


// Execute SQL command
$sql = "SELECT * FROM `sensor_data_alt` WHERE time >= NOW() - INTERVAL 1 DAY AND node_id = '$activeNodeID'";
$result = $conn->query($sql);

// Extract data into arrays if data exists
if ($result->num_rows > 0) {
    $temperatureDataArr = array();
    $humidityDataArr = array();
    $pressureDataArr = array();
    $pressureSLDataArr = array();
    $timestampArr = array();

    // output data of each row
    while($row = $result->fetch_assoc()) {
        $temperatureDataArr[] = $row['temperature'];
        $humidityDataArr[] = $row['humidity'];
        $pressureDataArr[] = $row['pressure'];
        $pressureSLDataArr[] = 1013.2;
        $timestampArr[] = $row['time'];
    }
} else {
    echo "No Data";
}

// Close Connection
$conn->close();

?> 

<!-- Set javascript data arrays -->
<script>
    // Setup Block
    const temperatureDataArr = <?php echo json_encode($temperatureDataArr) ?>;
    const humidityDataArr = <?php echo json_encode($humidityDataArr) ?>;
    const pressureDataArr = <?php echo json_encode($pressureDataArr) ?>;
    const pressureSLDataArr = <?php echo json_encode($pressureSLDataArr) ?>;
    const timestampArr = <?php echo json_encode($timestampArr) ?>;
</script>