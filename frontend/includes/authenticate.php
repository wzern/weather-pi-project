<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

espi_session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/login.php');
}

$return = safe_return(post_str('return'));

// Send the user back to the login form with an error code (and where they were headed)
function login_fail(string $code, string $return): void {
    redirect('/login.php?err=' . $code . ($return !== '' ? '&return=' . urlencode($return) : ''));
}

// Check if the data from the login form was submitted
$username = post_str('username');
$password = post_str('password');
if ($username === '' || $password === '') {
    login_fail('missing', $return);
}

$con = db_connect();
if ($con === null) {
    login_fail('db', $return);
}

// Prepared statements prevent SQL injection
$stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hash);
    $stmt->fetch();
    // Account exists, now we verify the password.
    if (password_verify($password, $hash)) {
        // Verification success! Remember on the server that this user is logged in.
        session_regenerate_id(true);
        $_SESSION['loggedin'] = true;
        $_SESSION['name']     = $username;
        $_SESSION['id']       = $id;
        redirect('/' . $return);
    }
}

// Unknown user or wrong password - same message for both
login_fail('bad', $return);
