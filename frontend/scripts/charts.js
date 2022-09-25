// Define the variables used in each chart
const temperatureData = {
  labels: timestampArr,
  datasets: [
    {
      label: "Temperature",
      data: temperatureDataArr,
      fill: false,
      borderColor: "rgb(255, 99, 132)",
      tension: 0.1,
    },
  ],
};

const humidityData = {
  labels: timestampArr,
  datasets: [
    {
      label: "Humidity",
      data: humidityDataArr,
      fill: false,
      borderColor: "rgb(128, 255, 102)",
      tension: 0.1,
    },
  ],
};

const pressureData = {
  labels: timestampArr,
  datasets: [
    {
      label: "Atmospheric Pressure",
      data: pressureDataArr,
      fill: false,
      borderColor: "rgb(75, 192, 192)",
      tension: 0.1,
    },
    {
      label: "Avg. AP at Sea level",
      data: pressureSLDataArr,
      fill: false,
      borderColor: "rgb(54, 162, 235)",
      tension: 0.1,
    },
  ],
};

const luxData = {
  labels: timestampArr,
  datasets: [
    {
      label: "Light Intensity",
      data: luxDataArr,
      fill: false,
      borderColor: "rgb(255, 250, 102)",
      tension: 0.1,
    },
    {
      label: "Min Avg Daylight",
      data: luxDataMinAvgArr,
      fill: {
        target: 2,
        above: "rgba(255, 199, 102, 0.02)",
        below: "rgba(255, 199, 102, 0.02)",
      },
      borderColor: "rgba(255, 199, 102, 0.2)",
      tension: 0.1,
    },
    {
      label: "Max Avg Daylight",
      data: luxDataMaxAvgArr,
      fill: false,
      borderColor: "rgba(255, 199, 102, 0.2)",
      tension: 0.1,
    },
  ],
};

// Define the settings used for each chart
const temperatureConfig = {
  type: "line",
  data: temperatureData,
  options: {
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function (value) {
            return value + "°C";
          },
        },
      },
    },
  },
};

const humidityConfig = {
  type: "line",
  data: humidityData,
  options: {
    scaleLabel: "<%=value%>%",
    scales: {
      y: {
        beginAtZero: true,
        max: 100,
        ticks: {
          callback: function (value) {
            return value + "%";
          },
        },
      },
    },
  },
};

const pressureConfig = {
  type: "line",
  data: pressureData,
  options: {
    scales: {
      y: {
        beginAtZero: false,
        min: 950,
        max: 1050,
        ticks: {
          callback: function (value) {
            return commafy(value) + " hPa";
          },
        },
      },
    },
  },
};

const luxConfig = {
  type: "line",
  data: luxData,
  options: {
    scaleLabel: "<%=value%>%",
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function (value) {
            return commafy(value) + " lux";
          },
        },
      },
    },
  },
};

// Render the charts in their corresponding divs
const temperatureChart = new Chart(
  document.getElementById("temperatureChart"),
  temperatureConfig
);

const humidityChart = new Chart(
  document.getElementById("humidityChart"),
  humidityConfig
);

const pressureChart = new Chart(
  document.getElementById("pressureChart"),
  pressureConfig
);

const luxChart = new Chart(document.getElementById("luxChart"), luxConfig);
