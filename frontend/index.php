<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Meta tags -->
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Page Title -->
    <title>Weather Station Project</title>

    <!-- Dependencies -->
    <link rel="stylesheet" href="css/styles.css">
    <script src="scripts/nav.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
</head>
<body>
    <!-- Page navigation -->
    <div id="mySidenav" class="sidenav">
        <a href="javascript:void(0)" id="closebtn" onclick="closeNav()">
            &times;
        </a>
        <a href="#">Dashboard</a>
        <a href="#">Placeholder</a>
        <a href="#">Placeholder</a>
        <a href="#">Settings</a>
    </div>

    <!-- Include main PHP script -->
    <?php require_once 'includes/app.php'?>

    <!-- Main body element -->
    <main id="main">
        <header>
            <span id="openbtn" onclick="openNav()"> &#9776; </span>
            <h1>Weather Pi</h1>
        </header>
        <div class="container">
            <!-- All the charts -->
            <div class="charts">
                <div class="chart">
                    <canvas id="temperatureChart"></canvas>
                </div>
                <div class="chart">
                    <canvas id="humidityChart"></canvas>
                </div>
                <div class="chart">
                    <canvas id="pressureChart"></canvas>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>Created by <a href="https://github.com/wzerp" target="_blank">William Zernikow</a> | <a href="https://github.com/wzerp/weather-pi-project" target="_blank">GitHub Repository</a></footer>

    <!-- Render the charts with charts.js script -->
    <script src="scripts/charts.js"></script>
</body>
</html>