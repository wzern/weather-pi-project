// Draws the summary cards and the four charts on the dashboard.
//
// The server (includes/app.php) has already averaged the readings into a few
// hundred time buckets, so everything here stays fast even for 30 days of data.
(function () {
  const dataElement = document.getElementById("espi-data");
  if (!dataElement || typeof Chart === "undefined") return;

  const data = JSON.parse(dataElement.textContent);
  const times = data.times; // unix seconds, one per averaged point

  // ----- Chart.js defaults -----
  Chart.defaults.color = "#9aa4b2";
  Chart.defaults.borderColor = "rgba(255, 255, 255, 0.08)";
  Chart.defaults.font.family = getComputedStyle(document.body).fontFamily;

  // ----- What we show -----
  const METRICS = [
    {
      key: "temperature",
      canvas: "temperatureChart",
      label: "Temperature",
      unit: "°C",
      decimals: 1,
      color: "rgb(255, 99, 132)",
      y: { grace: "10%" },
    },
    {
      key: "humidity",
      canvas: "humidityChart",
      label: "Humidity",
      unit: "%",
      decimals: 0,
      color: "rgb(128, 255, 102)",
      y: { min: 0, max: 100 },
    },
    {
      key: "pressure",
      canvas: "pressureChart",
      label: "Atmospheric Pressure",
      unit: " hPa",
      decimals: 1,
      color: "rgb(75, 192, 192)",
      y: { suggestedMin: 980, suggestedMax: 1045 },
      references: [{ label: "Avg. AP at Sea level", value: 1013.2, color: "rgb(54, 162, 235)" }],
    },
    {
      key: "light",
      canvas: "luxChart",
      label: "Light Intensity",
      unit: " lux",
      decimals: 0,
      color: "rgb(255, 250, 102)",
      y: { beginAtZero: true },
      // Typical daylight range, shaded between the two lines. Click a legend entry to hide it.
      references: [
        {
          label: "Min Avg Daylight",
          value: 32000,
          color: "rgba(255, 199, 102, 0.2)",
          fill: { target: 2, above: "rgba(255, 199, 102, 0.02)", below: "rgba(255, 199, 102, 0.02)" },
        },
        { label: "Max Avg Daylight", value: 100000, color: "rgba(255, 199, 102, 0.2)" },
      ],
    },
  ];

  // ----- Work out where the line should break (sensor offline) -----
  // A gap is a jump of more than 3x the normal spacing between points. The normal
  // spacing is the larger of the bucket size and the typical (median) spacing, so
  // sensors that report rarely (e.g. hourly) still get a continuous line.
  const deltas = [];
  for (let i = 1; i < times.length; i++) deltas.push(times[i] - times[i - 1]);
  deltas.sort((a, b) => a - b);
  const median = deltas.length ? deltas[Math.floor(deltas.length / 2)] : data.bucket;
  const maxGap = Math.max(data.bucket, median) * 3;

  // One row per point, plus a marker row (i = -1) after any gap so Chart.js breaks the line there
  const rows = [];
  times.forEach((t, i) => {
    if (i > 0 && t - times[i - 1] > maxGap) {
      rows.push({ x: (times[i - 1] + data.bucket) * 1000, i: -1 });
    }
    rows.push({ x: t * 1000, i: i });
  });

  const points = (values) => rows.map((r) => ({ x: r.x, y: r.i < 0 ? null : values[r.i] }));
  const flatLine = (value) => rows.map((r) => ({ x: r.x, y: value }));

  // With only a few points, draw them as dots so they are visible
  const sparse = times.length <= 100;

  // ----- Time axis formatting depends on how long the period is -----
  const spanHours = (data.to - data.from) / 3600;
  const timeFormats = {
    hour: spanHours <= 24 ? "h a" : "EEE h a",
    day: spanHours <= 168 ? "EEE d" : "d MMM",
    week: "d MMM",
    month: "MMM yyyy",
  };

  // ----- Build a chart for each metric -----
  METRICS.forEach((m) => {
    const datasets = [
      {
        label: m.label,
        data: points(data[m.key]),
        borderColor: m.color,
        backgroundColor: m.color,
        borderWidth: 2,
        tension: 0.25,
        fill: false,
        spanGaps: false,
        pointRadius: sparse ? 2 : 0,
        pointHoverRadius: 4,
      },
    ];

    (m.references || []).forEach((ref) => {
      datasets.push({
        label: ref.label,
        data: flatLine(ref.value),
        borderColor: ref.color,
        backgroundColor: ref.color,
        borderWidth: 1,
        borderDash: [6, 4],
        fill: ref.fill || false,
        pointRadius: 0,
        pointHoverRadius: 0,
        isReference: true, // hidden from tooltips
      });
    });

    new Chart(document.getElementById(m.canvas), {
      type: "line",
      data: { datasets: datasets },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        animation: false, // animating hundreds of points on a Pi-served page just adds jank
        parsing: false, // we already supply {x, y} points
        normalized: true, // ...sorted by x with no duplicates
        interaction: { mode: "index", axis: "x", intersect: false },
        plugins: {
          legend: { labels: { usePointStyle: true, boxWidth: 8 } },
          tooltip: {
            filter: (item) => !item.dataset.isReference,
            callbacks: {
              label: (ctx) =>
                ctx.parsed.y == null
                  ? ""
                  : ctx.dataset.label + ": " + formatNumber(ctx.parsed.y, m.decimals) + m.unit,
            },
          },
        },
        scales: {
          x: {
            type: "time",
            min: data.from * 1000,
            max: data.to * 1000,
            time: { tooltipFormat: "EEE d MMM, h:mm a", displayFormats: timeFormats },
            ticks: { maxRotation: 0, autoSkipPadding: 16 },
          },
          y: Object.assign(
            {
              ticks: { callback: (value) => formatNumber(value) + m.unit },
            },
            m.y
          ),
        },
      },
    });
  });

  // ----- Summary cards -----
  const summary = document.getElementById("summary");

  function addText(parent, tag, className, text) {
    const el = document.createElement(tag);
    el.className = className;
    el.textContent = text;
    parent.appendChild(el);
    return el;
  }

  if (summary) {
    METRICS.forEach((m) => {
      const card = document.createElement("div");
      card.className = "card";
      card.style.setProperty("--accent", m.color);

      addText(card, "span", "card-label", m.label);

      const latest = data.latest ? data.latest[m.key] : null;
      const value = addText(card, "span", "card-value", latest == null ? "–" : formatNumber(latest, m.decimals));
      if (latest != null) addText(value, "small", "", m.unit.trim());

      const [low, high] = data.range[m.key];
      const range = addText(
        card,
        "span",
        "card-range",
        low == null || high == null
          ? ""
          : "Low " + formatNumber(low, m.decimals) + " · High " + formatNumber(high, m.decimals)
      );
      range.title = "Lowest and highest reading in the selected period";

      summary.appendChild(card);
    });
  }

  // ----- Status line under the cards -----
  const status = document.getElementById("resolution");

  function renderStatus() {
    if (!status) return;
    const raw = formatNumber(data.rawCount, 0);
    const detail =
      data.rawCount <= times.length
        ? raw + " readings"
        : raw + " readings averaged into " + times.length + " points (" + formatBucket(data.bucket) + " averages)";
    status.textContent = (data.latest ? "Last reading " + timeAgo(data.latest.ts) + " · " : "") + detail;
  }

  renderStatus();
  setInterval(renderStatus, 30000);
})();
