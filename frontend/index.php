<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart.js</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php require_once 'includes/app.php'?>

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

    <script src="scripts/charts.js"></script>
</body>
</html>