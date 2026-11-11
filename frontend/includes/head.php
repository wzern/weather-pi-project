<?php
// Shared <head> contents. Expects $pageTitle (string) and $withCharts (bool) to be set by the page.
require_once __DIR__ . '/helpers.php';
$fullTitle = (($pageTitle ?? '') !== '' ? $pageTitle . ' | ' : '') . 'ESPi Weather Station';
?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="dark">
    <title><?= e($fullTitle) ?></title>
    <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E⛅%3C/text%3E%3C/svg%3E">
    <link rel="stylesheet" href="<?= e(asset('css/styles.css')) ?>">
    <script src="<?= e(asset('scripts/nav.js')) ?>"></script>
<?php if (!empty($withCharts)): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>
    <script src="<?= e(asset('scripts/functions.js')) ?>"></script>
<?php endif; ?>
