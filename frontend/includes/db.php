<?php
// Single place for the database connection settings.
// These must match the account created by install.sh and used by backend/api/import.php.
const DB_HOST = 'localhost';
const DB_USER = 'weatherPi';
const DB_PASS = '4Iz0p3hu9nSJujKz3kPM';
const DB_NAME = 'weatherPiProject';

// Returns a connected mysqli object, or null if the database can't be reached.
// Errors are reported through return values (not exceptions) so every page can
// show a friendly message instead of a PHP stack trace.
function db_connect(): ?mysqli {
    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_errno) {
        return null;
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
