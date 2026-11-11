<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Only a logged-in user may change their password, and only via a valid form post
if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/settings.php');
}
if (!csrf_valid(post_str('csrf'))) {
    redirect('/settings.php?err=csrf');
}

$current = post_str('passwordCurrent');
$new     = post_str('password');
$confirm = post_str('passwordConfirm');

if ($current === '' || $new === '' || $confirm === '') {
    redirect('/settings.php?err=fields');
}
if ($new !== $confirm) {
    redirect('/settings.php?err=mismatch');
}
if (strlen($new) < 8) {
    redirect('/settings.php?err=short');
}

$con = db_connect();
if ($con === null) {
    redirect('/settings.php?err=db');
}

// Prepared statements prevent SQL injection
$stmt = $con->prepare('SELECT password FROM accounts WHERE id = ?');
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    // This should never happen as the user wouldn't have been able to log in
    redirect('/settings.php?err=account');
}

$stmt->bind_result($hash);
$stmt->fetch();
$stmt->close();

if (!password_verify($current, $hash)) {
    redirect('/settings.php?err=current');
}

// Verification success - store the new password
$newHash = password_hash($new, PASSWORD_DEFAULT);
$update = $con->prepare('UPDATE accounts SET password = ? WHERE id = ?');
$update->bind_param('si', $newHash, $_SESSION['id']);
$update->execute();
$update->close();
$con->close();

// Log out so the user signs in again with the new password
redirect('/logout.php?return=login.php');
