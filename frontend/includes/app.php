<?php
// Data layer for the dashboard.
//
// Instead of sending every raw reading to the browser, the database averages
// readings into fixed-size time buckets, so a chart never has to draw more than
// a few hundred points no matter how long the period is or how often the
// sensors report. Tweak the 'bucket' values below to trade detail for speed.
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

const ESPI_METRICS = ['temperature', 'humidity', 'pressure', 'light'];

// label => window length and averaging bucket, both in seconds
//   24 hours / 5 min  = 288 points      7 days / 30 min = 336 points
//   48 hours / 10 min = 288 points     30 days / 2 hours = 360 points
const ESPI_PERIODS = [
    'Last 24 Hours' => ['seconds' => 86400,   'bucket' => 300],
    'Last 48 Hours' => ['seconds' => 172800,  'bucket' => 600],
    'Last 7 Days'   => ['seconds' => 604800,  'bucket' => 1800],
    'Last 30 Days'  => ['seconds' => 2592000, 'bucket' => 7200],
];

// SQL fragment that turns a text column into a number, or NULL if it isn't one.
// The sensor sketch can post "nan" when a sensor read fails; without this check
// MariaDB would count it as 0 and drag the averages down.
// $col is always one of ESPI_METRICS, never user input.
function sql_num(string $col): string {
    return 'IF(' . $col . ' REGEXP \'^-?[0-9]+([.][0-9]+)?$\', ' . $col . ' + 0, NULL)';
}

function num_or_null($value, int $decimals = 1): ?float {
    return is_numeric($value) ? round((float) $value, $decimals) : null;
}

// Run a prepared SELECT and return all rows, or null if anything went wrong.
function db_rows(mysqli $conn, string $sql, string $types, array $params): ?array {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return null;
    }
    if ($types !== '') {
        $stmt->bind_param($types, ...$params);
    }
    if (!$stmt->execute()) {
        $stmt->close();
        return null;
    }
    $result = $stmt->get_result();
    $rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : null;
    $stmt->close();
    return $rows;
}

// State shared with index.php / charts.php
$chartPeriods    = array_keys(ESPI_PERIODS);
$nodeIDArr       = [];
$activeNodeID    = '';
$selectedPeriod  = $chartPeriods[0];
$dashboardError  = null;   // message shown instead of the charts
$dashboard       = null;   // data handed to scripts/charts.js

$conn = db_connect();

if ($conn === null) {
    $dashboardError = 'Connection to the database failed';
} else {
    // Which sensor nodes have reported data?
    $nodeRows = db_rows($conn, 'SELECT DISTINCT node_id FROM sensor_data ORDER BY node_id', '', []);
    if ($nodeRows === null) {
        $dashboardError = 'Failed to read from the database';
    } elseif (count($nodeRows) === 0) {
        $dashboardError = 'No devices were found';
    } else {
        $nodeIDArr = array_column($nodeRows, 'node_id');

        // Work out what the user asked for, falling back to sensible defaults
        $requestedNode   = get_str('node');
        $requestedPeriod = get_str('chartPeriod');
        $activeNodeID    = in_array($requestedNode, $nodeIDArr, true) ? $requestedNode : $nodeIDArr[0];
        $selectedPeriod  = isset(ESPI_PERIODS[$requestedPeriod]) ? $requestedPeriod : $chartPeriods[0];

        $bucket = (int) ESPI_PERIODS[$selectedPeriod]['bucket'];
        $to     = time();
        $from   = $to - (int) ESPI_PERIODS[$selectedPeriod]['seconds'];

        // 1) Averaged series for the charts
        $avgCols = [];
        foreach (ESPI_METRICS as $m) {
            $avgCols[] = 'AVG(' . sql_num($m) . ') AS avg_' . $m;
        }
        $seriesSql = 'SELECT (UNIX_TIMESTAMP(`time`) DIV ' . $bucket . ') * ' . $bucket . ' AS bucket_start, '
                   . implode(', ', $avgCols)
                   . ' FROM sensor_data WHERE node_id = ? AND `time` >= FROM_UNIXTIME(?)'
                   . ' GROUP BY bucket_start ORDER BY bucket_start';
        $seriesRows = db_rows($conn, $seriesSql, 'si', [$activeNodeID, $from]);

        // 2) Raw reading count and true min/max over the period (averaging hides the extremes)
        $statCols = ['COUNT(*) AS n'];
        foreach (ESPI_METRICS as $m) {
            $statCols[] = 'MIN(' . sql_num($m) . ') AS min_' . $m;
            $statCols[] = 'MAX(' . sql_num($m) . ') AS max_' . $m;
        }
        $statSql  = 'SELECT ' . implode(', ', $statCols)
                  . ' FROM sensor_data WHERE node_id = ? AND `time` >= FROM_UNIXTIME(?)';
        $statRows = db_rows($conn, $statSql, 'si', [$activeNodeID, $from]);

        // 3) The most recent raw reading (even if it is older than the selected period)
        $latestSql  = 'SELECT UNIX_TIMESTAMP(`time`) AS ts, temperature, humidity, pressure, light'
                    . ' FROM sensor_data WHERE node_id = ? ORDER BY `time` DESC LIMIT 1';
        $latestRows = db_rows($conn, $latestSql, 's', [$activeNodeID]);

        if ($seriesRows === null || $statRows === null || $latestRows === null) {
            $dashboardError = 'Failed to read from the database';
        } elseif (count($seriesRows) === 0) {
            $dashboardError = 'No readings were found for this device in the selected period';
        } else {
            $dashboard = [
                'node'     => $activeNodeID,
                'period'   => $selectedPeriod,
                'from'     => $from,
                'to'       => $to,
                'bucket'   => $bucket,
                'rawCount' => (int) $statRows[0]['n'],
                'times'    => [],
                'range'    => [],
            ];
            foreach (ESPI_METRICS as $m) {
                $dashboard[$m] = [];
                $dashboard['range'][$m] = [
                    num_or_null($statRows[0]['min_' . $m]),
                    num_or_null($statRows[0]['max_' . $m]),
                ];
            }
            foreach ($seriesRows as $row) {
                // Plot each average in the middle of its bucket
                $dashboard['times'][] = (int) $row['bucket_start'] + intdiv($bucket, 2);
                foreach (ESPI_METRICS as $m) {
                    $dashboard[$m][] = num_or_null($row['avg_' . $m]);
                }
            }

            $dashboard['latest'] = null;
            if (count($latestRows) > 0) {
                $dashboard['latest'] = ['ts' => (int) $latestRows[0]['ts']];
                foreach (ESPI_METRICS as $m) {
                    $dashboard['latest'][$m] = num_or_null($latestRows[0][$m]);
                }
            }
        }
    }
    $conn->close();
}
