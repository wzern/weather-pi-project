<?php
session_start();

// Define database connection settings
$servername = "localhost";
$username = "username";
$password = "password";
$dbname = "weather-pi-project";

// Create a connection to the database server
$con = mysqli_connect($servername, $username, $password, $dbname);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
    header('Location: ./login.php?err=Failed to connect to MySQL');
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if the data from the login form was submitted
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
    header('Location: ./login.php?err=Please fill both the username and password fields!');
	exit('Please fill both the username and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // Account exists, now we verify the password.
        if (password_verify($_POST['password'], $password)) {
            // Verification success! User has logged-in!
            // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
            session_regenerate_id();
            $_SESSION['loggedin'] = TRUE;
            $_SESSION['name'] = $_POST['username'];
            $_SESSION['id'] = $id;
            header('Location: ./' . $_POST['return']);
        } else {
            // Incorrect password
            header('Location: ./login.php?err=Incorrect username and/or password!');
        }
    } else {
        // Incorrect username
        header('Location: ./login.php?err=Incorrect username and/or password!');
    }

	$stmt->close();
}
?>