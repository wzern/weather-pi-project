<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Login | ⛅ ESPi Weather Station</title>

    <!-- Dependencies -->
    <link rel="stylesheet" href="css/styles.css">

    <script src="scripts/nav.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>
<body>
    <!-- Include site navigation -->
    <?php require_once 'includes/nav.php'?>

    <!-- Main body element -->
    <main id="main">
        <header>
            <span id="openbtn" onclick="openNav()"> &#9776; </span>
            <h1><a href="./">⛅ ESPi Weather</a></h1>
        </header>
        
        <div class="container" id="container">
            <?php require_once 'includes/login.php'?>
        </div>

        <!-- Include footer -->
        <?php require_once 'includes/footer.php'; ?>
    </main>

    <!-- Render the charts with charts.js script -->
    <script src="scripts/charts.js"></script>
</body>
</html>