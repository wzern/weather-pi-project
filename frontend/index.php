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
        <canvas id="myChart"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Setup Block
    const temperature = <?php echo json_encode($temperature) ?>;
    const humidity = <?php echo json_encode($humidity) ?>;
    const pressure = <?php echo json_encode($pressure) ?>;

    const data = {
    labels: temperature,
    datasets: [{
        label: 'Temperature',
        data: temperature,
        fill: false,
        borderColor: 'rgb(75, 192, 192)',
        tension: 0.1
    },
    {
        label: 'Humidity',
        data: humidity,
        fill: false,
        borderColor: 'rgb(255, 99, 132)',
        tension: 0.1
    },
    {
        label: 'Pressure',
        data: pressure,
        fill: false,
        borderColor: 'rgb(153, 102, 255)',
        tension: 0.1
    }]
    };

    // Config Block
    const config = {
        type: 'line',
        data
    };

    // Render Block
    const myChart = new Chart(
        document.getElementById('myChart'),
        config

    );
    </script>

</body>
</html>