<?php
require_once __DIR__ . '/includes/helpers.php';

// Destroy the user session and its cookie
espi_session_start();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'] ?: 'Lax',
    ]);
}
session_destroy();

// Redirect to the requested page (only our own pages are allowed)
redirect('/' . safe_return(get_str('return')));
