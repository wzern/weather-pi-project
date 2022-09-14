#include <ESP8266WiFi.h>
#include <SFE_BMP180.h>
#include <Wire.h>
#include "DHT.h"        // DHT11 temperature and humidity sensor Predefined library

#define DHTTYPE DHT11   // DHT 11
#define dht_dpin 0      //GPIO-0 D3 pin of nodemcu

const char* ssid = "REDACTED";
const char* password = "REDACTED";
const char* host = "REDACTED";
const char* api_key = "REDACTED";
const char* node_id = "REDACTED";
char status;
double T, P, p0, a;
const int httpPort = 80;

DHT dht(dht_dpin, DHTTYPE);
SFE_BMP180 pressure;

void setup() {
  dht.begin();
  Serial.begin(115200);
  delay(10); // We start by connecting to a WiFi network Serial.println();
  Serial.println();
  Serial.print("Connecting to ");
  Serial.println(ssid);
  /* Explicitly set the ESP8266 to be a WiFi-client, otherwise, it by default, would try to act as both a client and an access-point and could cause network-issues with your other WiFi-devices on your WiFi-network. */
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED)
  {
    delay(500);
    Serial.print(".");
  }
  Serial.println("");
  Serial.println("WiFi connected");
  Serial.println("IP address: ");
  Serial.println(WiFi.localIP());

  if (pressure.begin())
    Serial.println("BMP180 init success");
  else
  {
    // Oops, something went wrong, this is usually a connection problem,
    // see the comments at the top of this sketch for the proper connections.

    Serial.println("BMP180 init fail\n\n");
    while (1); // Pause forever.
  }
}

void loop() {
  delay(55000);
  Serial.print("connecting to ");
  Serial.println(host); // Use WiFiClient class to create TCP connections
  WiFiClient client;

  if (!client.connect(host, httpPort)) {
    Serial.println("connection failed");
    return;
  }

  float h = dht.readHumidity(); //Humidity level
  float t = dht.readTemperature(); //Temperature in celcius

  status = pressure.getTemperature(T);
  if (status != 0)
  {
    // Print out the measurement:
    Serial.print("temperature: ");
    Serial.print(T, 2);
    Serial.print(" deg C, ");
    Serial.print((9.0 / 5.0)*T + 32.0, 2);
    Serial.println(" deg F");

    // Start a pressure measurement:
    // The parameter is the oversampling setting, from 0 to 3 (highest res, longest wait).
    // If request is successful, the number of ms to wait is returned.
    // If request is unsuccessful, 0 is returned.

    status = pressure.startPressure(3);
    if (status != 0)
    {
      // Wait for the measurement to complete:
      delay(status);

      // Retrieve the completed pressure measurement:
      // Note that the measurement is stored in the variable P.
      // Note also that the function requires the previous temperature measurement (T).
      // (If temperature is stable, you can do one temperature measurement for a number of pressure measurements.)
      // Function returns 1 if successful, 0 if failure.

      status = pressure.getPressure(P, T);
      if (status != 0)
      {
        // Print out the measurement:
        Serial.print("absolute pressure: ");
        Serial.print(P, 2);
        Serial.print(" mb, ");
        Serial.print(P * 0.0295333727, 2);
        Serial.println(" inHg");
      }
      else Serial.println("error retrieving pressure measurement\n");
    }
    else Serial.println("error starting pressure measurement\n");
  }
  else Serial.println("error retrieving temperature measurement\n");



  // We now create a URI for the request
  //this url contains the informtation we want to send to the server
  //if esp8266 only requests the website, the url is empty
  String url = "/weather-pi-project/backend/api/import.php/";
  url += "?api_key=";
  url += api_key;
  url += "&node_id=";
  url += node_id;
  url += "&temperature=";
  url += String(t);
  url += "&humidity=";
  url += String(h);
  url += "&pressure=";
  url += String(P);
  url += "&light=";
  url += "N/A";


  Serial.print("Requesting URL: ");
  Serial.println(url); // This will send the request to the server
  client.print(String("GET ") + url + " HTTP/1.1\r\n" + "Host: " + host + "\r\n" + "Connection: close\r\n\r\n");
  unsigned long timeout = millis();
  while (client.available() == 0) {
    if (millis() - timeout > 5000)
    {
      Serial.println(">>> Client Timeout !");
      client.stop(); return;
    }
  } // Read all the lines of the reply from server and print them to Serial
  while (client.available())
  {
    String line = client.readStringUntil('\r');
    Serial.print(line);
  }
  Serial.println();
  Serial.println("closing connection");
}
