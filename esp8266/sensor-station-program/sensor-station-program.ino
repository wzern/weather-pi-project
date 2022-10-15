// Include necessary libraries
#include <ESP8266WiFi.h>
#include <Adafruit_BMP085.h>
#include "DHT.h"
#include <Wire.h>
#include <BH1750.h>

// Define DHT sensor type and data pin
#define DHTTYPE DHT11   // DHT 11
#define dht_dpin 0      //GPIO-0 D3 pin of nodemcu

// Define variables for networking and operations
const char* ssid     = "WiFi name";
const char* password = "WiFi Password";
const char* host = "xxx.xxx.xxx.xxx";
const char* api_key = "APIToken";
const char* node_id = "Shed ESP";
const int httpPort = 443;

// Initialise DHT, BH1750 & BMP180 sensors
DHT dht(dht_dpin, DHTTYPE);
BH1750 lightMeter;
Adafruit_BMP085 bmp;

void setup() {
  // Start serial interface
  Serial.begin(9600);

  // Start listening to sensor data
  if (!bmp.begin()) {
    Serial.println("Could not find a valid BMP085 sensor, check wiring!");
    while (1) {}
  }
  dht.begin();
  delay(10);

  Wire.begin();
  lightMeter.begin();

  // Connect to the specified Wi-Fi network
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);

  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());
}

void loop() {
  // Create encrypted connection to the webserver
  Serial.print("connecting to ");
  Serial.println(host);

  WiFiClientSecure client;

  client.setInsecure();

  if (!client.connect(host, httpPort)) { //works!
    Serial.println("connection failed");
    return;
  }

  // Save current sensor values in variables
  float h = dht.readHumidity(); //Humidity level
  float t = dht.readTemperature(); //Temperature in celcius
  float p = bmp.readPressure();
  float lux = lightMeter.readLightLevel();

  // Create a URI for the request
  String url = "/api/import.php/";
  url += "?api_key=";
  url += api_key;
  url += "&node_id=";
  url += node_id;
  url += "&temperature=";
  url += String(t);
  url += "&humidity=";
  url += String(h);
  url += "&pressure=";
  url += String(p/100);
  url += "&light=";
  url += String(lux);

  Serial.print("Requesting URL: ");
  Serial.println(url);

  // Send the request to the server
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  Serial.println();
  Serial.println("closing connection");

  // Wait for one hour until next run
  delay(3600000);
}

// Made with ❤ by William Zernikow
// Project repo: https://github.com/wzern/weather-pi-project
