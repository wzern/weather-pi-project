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

$apiTokenSQL = "SELECT * FROM `api_keys` LIMIT 1";
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

// Close Connection
$conn->close();
?>

<div class="settings">
    <div class="manageToken">
        <h1 id="settingsH1">Manage API Token</h1>
        <h2 id="settingsH2">Current token: <input type="text" id="apiToken" value="<?=$apiToken?>" readonly></h2>
        <form action="./includes/regen-token.php" method="post">
            <input type="text" name="token" value="<?=$apiToken?>" hidden />
            <input type="submit" value="Regenerate" />
        </form>

    </div>


    <form id="passwordUpdateForm" action="./includes/password-updater.php" method="post">
        <h1 id="settingsH1">Change password</h1>
        <h2 id="settingsErrorH2"><?=$_GET['err']?></h2>
        <input
            type="password"
            name="passwordCurrent"
            placeholder="Current Password"
            id="passwordCurrent"
            required
        />
        <input
            type="password"
            name="password"
            placeholder="Password"
            id="password"
            required
        />
        <input
            type="password"
            name="passwordConfirm"
            placeholder="Confirm Password"
            id="passwordConfirm"
            required
        />
        <input type="submit" value="Update" />
    </form>
</div>