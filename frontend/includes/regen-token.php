<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

// Only a logged-in user may regenerate the token, and only via a valid form post
if (!is_logged_in() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/settings.php');
}
if (!csrf_valid(post_str('csrf'))) {
    redirect('/settings.php?err=csrf');
}

$curToken = post_str('token');
if ($curToken === '') {
    redirect('/settings.php');
}

// Random alphanumeric token from a cryptographically secure source
function genToken(int $length): string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $max = strlen($characters) - 1;
    $token = '';
    for ($i = 0; $i < $length; $i++) {
        $token .= $characters[random_int(0, $max)];
    }
    return $token;
}

$conn = db_connect();
if ($conn === null) {
    redirect('/settings.php?err=db');
}

$newToken = genToken(32);

// Prepared statements prevent SQL injection
$stmt = $conn->prepare('UPDATE `api_keys` SET api_key = ? WHERE api_key = ?');
$stmt->bind_param('ss', $newToken, $curToken);
$stmt->execute();
$changed = $stmt->affected_rows > 0;
$stmt->close();
$conn->close();

redirect($changed ? '/settings.php?ok=token' : '/settings.php?err=token');
