// Round a timestamp to nearest hour
function roundHour(date) {
  date.setHours(date.getHours() + Math.round(date.getMinutes() / 60));
  date.setMinutes(0, 0, 0); // Resets also seconds and milliseconds

  return date;
}

// Convert a timestamp to AM/PM format
function formatAMPM(date) {
  var hours = date.getHours();
  var minutes = date.getMinutes();
  var ampm = hours >= 12 ? "pm" : "am";
  hours = hours % 12;
  hours = hours ? hours : 12; // the hour '0' should be '12'
  minutes = minutes < 10 ? "0" + minutes : minutes;
  var strTime = hours + ":" + minutes + " " + ampm;
  return strTime;
}

timestampArr24H = timestampArr.map((i) => formatAMPM(roundHour(new Date(i))));

// Data Block
const temperatureData = {
  labels: timestampArr24H,
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
  labels: timestampArr24H,
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
  labels: timestampArr24H,
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
