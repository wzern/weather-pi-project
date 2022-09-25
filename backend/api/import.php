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


$con = mysqli_connect($servername, $username, $password, $dbname);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT * FROM api_keys WHERE api_key = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $API_KEY);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();


  if ($stmt->num_rows > 0) {
    if ($stmt->num_rows > 0 && !empty($NODE_ID)) {
      $sql = "INSERT INTO sensor_data_alt (node_id, temperature, humidity, pressure, light)
      VALUES ('$NODE_ID', '$TEMPERATURE', '$HUMIDITY', '$PRESSURE', '$LIGHT')";
  
      if ($con->query($sql) === TRUE) {
          echo "Success";
      } else {
          echo "Fault: " . $sql . "<br>" . $con->error;
      }
  
      $stmt->close();
    }
  } else {
      // Incorrect username
      echo 'Incorrect API key!';
  }


	$stmt->close();
}
?> 