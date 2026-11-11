<?php
// Load the data first - this has to happen before any HTML is sent
require_once __DIR__ . '/includes/app.php';
espi_session_start(); // before any output, so the nav can tell if you are logged in

$pageTitle  = '';
$withCharts = true;
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
            <?php if (count($nodeIDArr) > 0): ?>
            <form method="get" id="optForm">
                <select name="node" id="nodeSelect" onchange="this.form.submit()" aria-label="Sensor node">
                    <?php foreach ($nodeIDArr as $nodeID): ?>
                        <option value="<?= e($nodeID) ?>"<?= $nodeID === $activeNodeID ? ' selected' : '' ?>><?= e($nodeID) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="chartPeriod" id="chartPeriodSelect" onchange="this.form.submit()" aria-label="Time period">
                    <?php foreach ($chartPeriods as $chartPeriod): ?>
                        <option value="<?= e($chartPeriod) ?>"<?= $chartPeriod === $selectedPeriod ? ' selected' : '' ?>><?= e($chartPeriod) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
            <?php endif; ?>
        </header>

        <div class="container" id="container">
            <?php require_once __DIR__ . '/includes/charts.php'; ?>
        </div>

        <!-- Include footer -->
        <?php require_once __DIR__ . '/includes/footer.php'; ?>
    </main>

    <?php if ($dashboard !== null): ?>
    <!-- Render the summary cards and charts -->
    <script src="<?= e(asset('scripts/charts.js')) ?>"></script>
    <?php endif; ?>
</body>
</html>
