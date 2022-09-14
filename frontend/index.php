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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Include main PHP script -->
    <?php require_once 'includes/app.php'?>

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

    <!-- Render the charts with charts.js script -->
    <script src="scripts/charts.js"></script>
</body>
</html>