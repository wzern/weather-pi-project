// Data Block
const temperatureData = {
  labels: timestampArr,
  datasets: [
    {
      label: "Temperature 24H",
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
      label: "Humidity 24H",
      data: humidityDataArr,
      fill: false,
      borderColor: "rgb(75, 192, 192)",
      tension: 0.1,
    },
  ],
};

const pressureData = {
  labels: timestampArr,
  datasets: [
    {
      label: "Pressure 24H",
      data: pressureDataArr,
      fill: false,
      borderColor: "rgb(153, 102, 255)",
      tension: 0.1,
    },
  ],
};

// Config Block
const temperatureConfig = {
  type: "line",
  data: temperatureData,
  options: {
    scales: {
      y: {
        beginAtZero: true,
      },
    },
  },
};

const humidityConfig = {
  type: "line",
  data: humidityData,
  options: {
    scales: {
      y: {
        beginAtZero: true,
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
        beginAtZero: true,
      },
    },
  },
};

// Render Block
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
