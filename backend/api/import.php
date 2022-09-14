<?php
// Authorisation and identity
$API_KEY = $_GET['api_key'];
$NODE_ID = $_GET['node_id'];

// Metrics
$TEMPERATURE = $_GET['temperature'];
$HUMIDITY = $_GET['humidity'];
$PRESSURE = $_GET['pressure'];
$LIGHT = $_GET['light'];

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

$sql = "SELECT * FROM api_keys WHERE api_key='$API_KEY'";
$result = $conn->query($sql);

if ($result->num_rows > 0 && !empty($NODE_ID)) {
    $sql = "INSERT INTO sensor_data_alt (node_id, temperature, humidity, pressure, light)
    VALUES ('$NODE_ID', '$TEMPERATURE', '$HUMIDITY', '$PRESSURE', '$LIGHT')";

    if ($conn->query($sql) === TRUE) {
        echo "Success";
    } else {
        echo "Fault: " . $sql . "<br>" . $conn->error;
    }

} else {
  echo "Unauthorized";
}
$conn->close();
?> 