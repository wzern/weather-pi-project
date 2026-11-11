<?php // Dashboard body: messages, summary cards and the four charts. Data comes from includes/app.php ?>

<?php if ($dashboardError !== null): ?>
  <p class="notice"><?= e($dashboardError) ?></p>
<?php else: ?>
  <!-- Latest reading + range cards, filled in by scripts/charts.js -->
  <section class="summary" id="summary" aria-label="Current readings"></section>
  <p class="resolution" id="resolution"></p>

  <!-- Create a container for each chart -->
  <div class="charts">
    <div class="chart"><canvas id="temperatureChart"></canvas></div>
    <div class="chart"><canvas id="humidityChart"></canvas></div>
    <div class="chart"><canvas id="pressureChart"></canvas></div>
    <div class="chart"><canvas id="luxChart"></canvas></div>
  </div>

  <!-- Chart data (already averaged by the database) as JSON, read by scripts/charts.js -->
  <script type="application/json" id="espi-data"><?=
    json_encode($dashboard, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES)
  ?></script>
<?php endif; ?>
