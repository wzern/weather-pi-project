<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chart.js</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once 'includes/app.php'?>

    <div class="chart">
        <canvas id="temperatureChart"></canvas>
    </div>

    <div class="chart">
        <canvas id="humidityChart"></canvas>
    </div>

    <div class="chart">
        <canvas id="pressureChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Setup Block
    const temperature = <?php echo json_encode($temperature) ?>;
    const humidity = <?php echo json_encode($humidity) ?>;
    const pressure = <?php echo json_encode($pressure) ?>;

    const temperatureData = {
    labels: temperature,
    datasets: [{
        label: 'Temperature',
        data: temperature,
        fill: false,
        borderColor: 'rgb(255, 99, 132)',
        tension: 0.1
    }]};

    const humidityData = {
    labels: humidity,
    datasets: [{
        label: 'Humidity',
        data: humidity,
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
    }]};

    const pressureData = {
    labels: pressure,
    datasets: [{
        label: 'Pressure',
        data: pressure,
        fill: false,
        borderColor: 'rgb(153, 102, 255)',
        tension: 0.1
    }]};

    // Config Block
    const temperatureConfig = {
        type: 'line',
        data: temperatureData,
        options: {
            scales: {
            y: {
                beginAtZero: true
            }
            }
        }
    };

    const humidityConfig = {
        type: 'line',
        data: humidityData,
        options: {
            scales: {
            y: {
                beginAtZero: true
            }
            }
        }
    };

    const pressureConfig = {
        type: 'line',
        data: pressureData,
        options: {
            scales: {
            y: {
                beginAtZero: true
            }
            }
        }
    };

    // Render Block
    const temperatureChart = new Chart(
        document.getElementById('temperatureChart'),
        temperatureConfig
    );

    const humidityChart = new Chart(
        document.getElementById('humidityChart'),
        humidityConfig
    );

    const pressureChart = new Chart(
        document.getElementById('pressureChart'),
        pressureConfig
    );
    </script>

</body>
</html>