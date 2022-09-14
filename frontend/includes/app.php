<?php
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

$sql = "SELECT * FROM sensor_data_alt";
$result = $conn->query($sql);

$conn->close();

if ($result->num_rows > 0) {
    $temperature = array();
    $humidity = array();
    $pressure = array();
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $temperature[] = $row['temperature'];
        $humidity[] = $row['humidity'];
        $pressure[] = $row['pressure'];
    }
} else {
    echo "0 results";
}
?> 

<script>
    // Setup Block
    const temperature = <?php echo json_encode($temperature) ?>;
    const humidity = <?php echo json_encode($humidity) ?>;
    const pressure = <?php echo json_encode($pressure) ?>;
</script>