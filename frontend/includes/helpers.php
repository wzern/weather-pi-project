<?php
// Small shared helpers: sessions, CSRF protection, escaping and safe redirects.

// Start the session once, with sensible cookie settings.
function espi_session_start(): void {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $secure,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function is_logged_in(): bool {
    espi_session_start();
    return !empty($_SESSION['loggedin']);
}

// HTML-escape a value for safe output.
function e($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// URL for a static file with a cache-busting version, so browsers pick up
// new CSS/JS straight after an update instead of using a stale cached copy.
function asset(string $path): string {
    $file = dirname(__DIR__) . '/' . $path;
    $mtime = is_file($file) ? filemtime($file) : 0;
    return $path . '?v=' . $mtime;
}

// Read a query-string value as a plain string ('' if missing or not a string).
function get_str(string $key): string {
    return (isset($_GET[$key]) && is_string($_GET[$key])) ? $_GET[$key] : '';
}

// Read a POST value as a plain string ('' if missing or not a string).
function post_str(string $key): string {
    return (isset($_POST[$key]) && is_string($_POST[$key])) ? $_POST[$key] : '';
}

function redirect(string $path): void {
    header('Location: ' . $path);
    exit;
}

// Only allow redirects back to our own pages (prevents open redirects).
// Returns the page name without a leading slash, or '' for the dashboard.
function safe_return(string $page): string {
    $allowed = ['settings.php', 'login.php'];
    return in_array($page, $allowed, true) ? $page : '';
}

// CSRF token for state-changing forms.
function csrf_token(): string {
    espi_session_start();
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_valid(string $token): bool {
    espi_session_start();
    return $token !== '' && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
