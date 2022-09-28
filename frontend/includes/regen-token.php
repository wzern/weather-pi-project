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

if (!isset($_POST['token'])) {
    header('Location: /settings.php');
	exit('Token not provided');
}

function genToken($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
 
    for ($i = 0; $i < $length; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $randomString .= $characters[$index];
    }
 
    return $randomString;
}

$curToken = $_POST['token'];
$newToken = genToken(32);

$apiTokenSQL = "UPDATE `api_keys` SET api_key = '$newToken' WHERE api_key = '$curToken'";
$resultToken = $conn->query($apiTokenSQL);

// Extract data into arrays if data exists
if ($resultToken->num_rows > 0) {
    // output data of each row
    while($row = $resultToken->fetch_assoc()) {
        $apiToken = $row['api_key'];
    }

} else {
    $apiToken = "Non existent. Please rebuild the database!";
}

header('Location: /settings.php');
// Close Connection
$conn->close();
?>