<?php
session_start();

// Define database connection settings
$servername = "localhost";
$username = "weatherPi";
$password = "4Iz0p3hu9nSJujKz3kPM";
$dbname = "weatherPiProject";

// Create a connection to the database server
$con = mysqli_connect($servername, $username, $password, $dbname);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
    header('Location: /settings.php?err=Failed to connect to MySQL');
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Check if the data from the login form was submitted
if ( !isset($_POST['passwordCurrent'], $_POST['password'], $_POST['passwordConfirm']) ) {
	// Could not get the data that should have been sent.
    header('Location: /settings.php?err=Please fill in all the required fields');
	exit('Please fill in all the required fields');
}

if ( $_POST['password'] !== $_POST['passwordConfirm'] ) {
    // Could not get the data that should have been sent.
    header('Location: /settings.php?err=New passwords do not match');
	exit('New passwords do not match');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	$stmt->bind_param('s', $_SESSION['name']);
	$stmt->execute();
	$stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password);
        $stmt->fetch();
        // Account exists, now we verify the password.
        if (password_verify($_POST['passwordCurrent'], $password)) {
            // Verification success!
            $newHashedPassword = password_hash($_POST['password'] , PASSWORD_DEFAULT);
            if ($stmt = $con->prepare('UPDATE accounts SET password = ? WHERE username = ?')) {
                $stmt->bind_param('ss', $newHashedPassword, $_SESSION['name']);
                $stmt->execute();
            }
            header('Location: /logout.php?return=login.php');
        } else {
            // Incorrect password
            header('Location: /settings.php?err=Current password is incorrect!');
        }
    } else {
        // Incorrect username - this should never happen as the user woudln't have been able to log in
        header('Location: /settings.php?err=Your user account does not exist');
    }

	$stmt->close();
}


?>