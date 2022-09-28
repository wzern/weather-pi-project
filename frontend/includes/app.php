<?php
// Define database connection settings
$servername = "localhost";
$username = "weatherPi";
$password = "4Iz0p3hu9nSJujKz3kPM";
$dbname = "weatherPiProject";

// Create a connection to the database server
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the server connection
if ($conn->connect_error) {
    echo "<center style='position: absolute; top: 10vh; left: 50%; transform: translateX(-50%)'>Connection to the database failed</center>";
    die();
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

    // Define a default option incase the user has not specified a node
    $defaultNodeID = (isset($nodeIDArr[0])) ? $nodeIDArr[0] : "Default";
    $activeNodeID = (!empty($_GET['node'])) ? $_GET['node'] : $defaultNodeID;

    // Define the differet time ranges
    $chartPeriods = ['Last 24 Hours', 'Last 48 Hours', 'Last 7 Days'];
    $selectedPeriod = $_GET['chartPeriod'];

    // Convert the selected time range to minutes by converting to hours, adding one hour, then subtracting 1 minute. 
    // This is so that the chart shows the last <x> amount of hours but also the current time.
    // E.g: Selected 24 hours, add one hour (25), convet to minutes (1500), subtract one minute -> 1499 minutes.
    // 1499 minutes on the chart might look something like 3pm yesterday to 3pm today rather than 4pm yesterday to 3pm today.
    if (!empty($selectedPeriod)) {
        switch ($selectedPeriod) {
            case 'Last 24 Hours':
                $periodStr = 'time >= NOW() - INTERVAL 1499 MINUTE AND';
                $timeFormatStr = 'timestampRawArr.map((i) => formatAMPM(roundHour(new Date(i))));';
                break;
            case 'Last 48 Hours':
                $periodStr = 'time >= NOW() - INTERVAL 2939 MINUTE AND';
                $timeFormatStr = 'timestampRawArr.map((i) => formatDayAMPM(roundHour(new Date(i))));';
                break;
            case 'Last 7 Days':
                $periodStr = 'time >= NOW() - INTERVAL 10079 MINUTE AND';
                $timeFormatStr = 'timestampRawArr.map((i) => formatDayAMPM(roundHour(new Date(i))));';
                break;
            default:
                $periodStr = 'time >= NOW() - INTERVAL 1499 MINUTE AND';
                $timeFormatStr = 'timestampRawArr.map((i) => formatAMPM(roundHour(new Date(i))));';
        } 
    } 

    // Default to 24 hours
    else {
        $periodStr = 'time >= NOW() - INTERVAL 1499 MINUTE AND';
        $timeFormatStr = 'timestampRawArr.map((i) => formatAMPM(roundHour(new Date(i))));';
    }



    // Execute SQL command
    $stmt = $conn->prepare('SELECT * FROM `sensor_data_alt` WHERE ' . $periodStr . ' node_id = ?');
    $stmt->bind_param('s', $activeNodeID); // 's' specifies the variable type => 'string'
    $stmt->execute();

    $result = $stmt->get_result();

    // Extract data into arrays if data exists
    if ($result->num_rows > 0) {
        $temperatureDataArr = array();
        $humidityDataArr = array();
        $pressureDataArr = array();
        $pressureSLDataArr = array();
        $luxDataArr = array();
        $timestampArr = array();

        // output data of each row
        while($row = $result->fetch_assoc()) {
            $temperatureDataArr[] = $row['temperature'];
            $humidityDataArr[] = $row['humidity'];
            $pressureDataArr[] = $row['pressure'];
            $pressureSLDataArr[] = 1013.2;
            $luxDataArr[] = $row['light'];
            $luxDataMinAvgArr[] = 32000;
            $luxDataMaxAvgArr[] = 100000;
            $timestampArr[] = $row['time'];
        }
    } else {
        echo "<center style='position: absolute; top: 10vh; left: 50%; transform: translateX(-50%)'>No data was found for this device</center>";
    }
} else {
    echo "<center style='position: absolute; top: 10vh; left: 50%; transform: translateX(-50%)'>No devices were found</center>";
}

// Close Connection
$conn->close();

?> 

<script>
    // Setup Block used to parse the PHP arrays into javascipt arrays
    const temperatureDataArr = <?php echo json_encode($temperatureDataArr) ?>;
    const humidityDataArr = <?php echo json_encode($humidityDataArr) ?>;
    const pressureDataArr = <?php echo json_encode($pressureDataArr) ?>;
    const pressureSLDataArr = <?php echo json_encode($pressureSLDataArr) ?>;
    const luxDataArr = <?php echo json_encode($luxDataArr) ?>;
    const luxDataMinAvgArr = <?php echo json_encode($luxDataMinAvgArr) ?>;
    const luxDataMaxAvgArr = <?php echo json_encode($luxDataMaxAvgArr) ?>;
    const timestampRawArr = <?php echo json_encode($timestampArr) ?>;
    timestampArr = <?=$timeFormatStr?>;
</script>
