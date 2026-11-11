<?php
require_once __DIR__ . '/includes/helpers.php';

// If the user is not logged in redirect to the login page...
if (!is_logged_in()) {
    redirect('./login.php?return=settings.php');
}

$pageTitle  = 'Settings';
$withCharts = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php require __DIR__ . '/includes/head.php'; ?>
</head>
<body>
    <!-- Include site navigation -->
    <?php require_once __DIR__ . '/includes/nav.php'; ?>

    <!-- Main body element -->
    <main id="main">
        <header>
            <button type="button" id="openbtn" onclick="openNav()" aria-label="Open menu">&#9776;</button>
            <h1><a href="./">⛅ ESPi Weather</a></h1>
        </header>

        <div class="container" id="container">
            <?php require_once __DIR__ . '/includes/settings.php'; ?>
        </div>

        <!-- Include footer -->
        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </main>
</body>
</html>
