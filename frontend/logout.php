<?php
// Destory the user session
session_start();
session_destroy();

// Redirect to the login page:
header('Location: /'.$_GET['return']);
?>