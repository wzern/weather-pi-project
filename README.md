<div align="center">
  <img src="https://cdn.lansec.net/wzernikow/github/weather-pi-project/hero-img.png" width=900" alt="hero image" />
</div>
<div align="center">
  <h1 align="center">⛅ ESPi Weather Station | 2022</h1>
  <h3>Powered by ESP8266 and Raspberry Pi</h3>
</div>
<p align="center">
  <a href="https://github.com/wzern/weather-pi-project/commits/main" target="_blank">
    <img src="https://img.shields.io/github/last-commit/wzern/weather-pi-project?" alt="Badge showing when the last commit was made"/>
  </a>

  <a href="https://github.com/wzern/weather-pi-project/issues" target="_blank">
    <img src="https://img.shields.io/github/issues/wzern/weather-pi-project?" alt="Badge showing the total of project issues"/>
  </a>
  
  <a href="https://github.com/wzern/weather-pi-project/blob/main/LICENSE" target="_blank">
    <img alt="Badge showing project license type" src="https://img.shields.io/github/license/wzern/weather-pi-project?color=f85149">
  </a>
</p>
<div align="center">
  <a href="#about">About</a> &#xa0; | &#xa0;
  <a href="#technologies">Technologies</a> &#xa0; | &#xa0;
  <a href="#requirements">Requirements</a> &#xa0; | &#xa0;
  <a href="#getting-started">Get Started</a> &#xa0; | &#xa0;
  <a href="#license">License</a> &#xa0; | &#xa0;
  <a href="#author">Author</a>
</div>
<h2 id="about">🎯 About</h2>
This is a year 13 digital and electronics project in which we build a weather station using a Raspberry Pi 4 and some ESP8266 micro-controllers with sensors

<h2 id="technologies">⚙️ Main Technologies</h2>
<div align="left">
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/html5/html5-original.svg" height="40" width="52" alt="html5 logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/css3/css3-original.svg" height="40" width="52" alt="css3 logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/javascript/javascript-original.svg" height="40" width="52" alt="javascript logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" height="40" width="52" alt="php logo"  />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/arduino/arduino-original.svg" height="40" width="52" alt="arduino logo"  />
</div>

<h2 id="requirements">✅ Requirements</h2>
<ul>
  <li><a href="#">Raspberry Pi 4</a></li>
  <li><a href="#">ESP8266</a></li>
  <li><a href="#">Temperature & Humidity Sensor (DHT11)</a></li>
  <li><a href="#">Barometric Pressue Sensor (BMP180)</a></li>
  <li><a href="#">Digital Light Sensor (BH1750FVI)</a></li>
</ul>

<h2 id="getting-started">🚀 Getting Started</h2>

<details>
<summary markdown="span"> Downloading and Installing Raspberry Pi OS</summary>
<h3>Downloading and Installing Raspberry Pi OS</h3>
<p>Once you have all the required components, use the next steps to prepare your Raspberry Pi 4 to act as a database and webserver for your weather station system. This is where the ESP8266 sensor units will send their sensor readings for us to see in our web-browser.</p>
<p>The following steps will work on Linux, Windows and MacOS</p>
<ol>
  <li>Insert a microSD card / reader into your computer</li>
  <li>Download and install the <a href="https://www.raspberrypi.com/software/" target="_blank">official Raspberry Pi Imager</a></li>
  <li>Click Choose OS and select 'Raspberry Pi OS (Other)'. Then choose 'Raspberry Pi OS Lite (64-bit)'</li>
  <li>Click Choose Storage and choose your SD Card</li>
  <li>Click the Settings icon in the bottom right corner. Set the hostname to espi-weather, enable SSH, and set your password for the 'pi' user. Please DO NOT change the username, keep it as pi or else the installer script for this project will not work properly.</li>
  <li>Finally, click Write</li>
</ol>
</details>

<h2 id="license">📝 License</h2>
<h3>This project is under license from MIT.</h3>
<p>A short and simple permissive license with conditions only requiring preservation of copyright and license notices. Licensed works, modifications, and larger works may be distributed under different terms and without source code.</p>

<p>For more details, see the <a href="https://github.com/wzern/weather-pi-project/blob/main/LICENSE">LICENSE</a> file.</p>

<h2 id="author">🙋‍♂️ Author</h2>
<p>Made with ❤️ by <a href="https://github.com/wzern">William Zernikow</a></p>
