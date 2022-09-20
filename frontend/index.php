<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>⛅ ESPi Weather Station</title>

    <!-- Dependencies -->
    <link rel="stylesheet" href="css/styles.css">

    <script src="scripts/nav.js"></script>
    <script src="scripts/functions.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>
<body>
    <!-- Include site navigation -->
    <?php require_once 'includes/nav.php'?>

    <!-- Include main PHP script -->
    <?php require_once 'includes/app.php'?>

    <!-- Main body element -->
    <main id="main">
        <header>
            <span id="openbtn" onclick="openNav()"> &#9776; </span>
            <h1>⛅ ESPi Weather</h1>
            <form method="get" id="optForm">
                <select name='node' id="nodeSelect" onchange='if(this.value != 0) { this.form.submit(); }'>
                    <?php foreach ($nodeIDArr as $nodeID) {
                        $selected = ($_GET['node'] === $nodeID) ? "selected" : "";
                        echo <<<EOD
                            <option value="$nodeID" $selected>$nodeID</option>
                        EOD;
                    } ?>
                </select>
                <select name='chartPeriod' id="chartPeriodSelect" onchange='if(this.value != 0) { this.form.submit(); }'>
                    <?php foreach ($chartPeriods as $chartPeriod) {
                        $selected = ($_GET['chartPeriod'] === $chartPeriod) ? "selected" : "";
                        echo <<<EOD
                            <option value="$chartPeriod" $selected>$chartPeriod</option>
                        EOD;
                    } ?>
                </select>
            </form>
        </header>

        <div class="container" id="container">
            <?php require_once 'includes/charts.php';?>
        </div>

        <?php require_once 'includes/footer.php'; ?>
    </main>

    <!-- Render the charts with charts.js script -->
    <script src="scripts/charts.js"></script>
</body>
</html>