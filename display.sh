#!/usr/bin/env bash
YW=`echo "\033[33m"`
RD=`echo "\033[01;31m"`
BL=`echo "\033[36m"`
GN=`echo "\033[1;92m"`
CL=`echo "\033[m"`
RETRY_NUM=10
RETRY_EVERY=3
NUM=$RETRY_NUM
CM="${GN}✓${CL}"
CROSS="${RD}✗${CL}"
BFR="\\r\\033[K"
HOLD="-"
set -o errexit
set -o errtrace
set -o nounset
set -o pipefail
shopt -s expand_aliases
alias die='EXIT=$? LINE=$LINENO error_exit'
trap die ERR

function error_exit() {
  trap - ERR
  local reason="Unknown failure occurred."
  local msg="${1:-$reason}"
  local flag="${RD}‼ ERROR ${CL}$EXIT@$LINE"
  echo -e "$flag $msg" 1>&2
  exit $EXIT
}

function msg_info() {
    local msg="$1"
    echo -ne " ${HOLD} ${YW}${msg}..."
}

function msg_ok() {
    local msg="$1"
    echo -e "${BFR} ${CM} ${GN}${msg}${CL}"
}
function msg_error() {
    local msg="$1"
    echo -e "${BFR} ${CROSS} ${RD}${msg}${CL}"
}

echo ""
echo "--------------------------------------------------------------"
echo "  ______  _____ _____ _     _____  _           _              "
echo " |  ____|/ ____|  __ (_)   |  __ \(_)         | |             "
echo " | |__  | (___ | |__) |    | |  | |_ ___ _ __ | | __ _ _   _  "
echo " |  __|  \___ \|  ___/ |   | |  | | / __| '_ \| |/ _' | | | | "
echo " | |____ ____) | |   | |   | |__| | \__ \ |_) | | (_| | |_| | "
echo " |______|_____/|_|   |_|   |_____/|_|___/ .__/|_|\__,_|\__, | "
echo "                                        | |             __/ | "
echo "        V1.0      github/wzern          |_|            |___/  "
echo "--------------------------------------------------------------"
echo ""
echo "You are about to install the ESPi Display Script: Stable"
echo "This script is for Raspberry Pis ONLY!"
echo ""
echo "WARNING! You need to enable I2C communication."
echo "Before you run this script, please do the following:"
echo "> sudo raspi-config"
echo ""
echo "Select 'Interfacing Options', then select 'I2C', and then choose"
echo "'Yes' when it asks if you would like to enable the feature."
echo "> sudo reboot"
echo ""
echo "After the reboot, please run this script to proceed"
echo ""

while true; do

read -p "Do you want to proceed? (y/n) " yn

case $yn in 
	[yY] )
		break;;
	[nN] ) echo Exiting...;
		exit;;
	* ) echo Invalid response;;
esac

done
echo ""

msg_info "Updating package lists"
apt update &>/dev/null
msg_ok "Updated package lists"

msg_info "Installing Python3"
apt install python3 python3-pip -y &>/dev/null
msg_ok "Installed Python3"

msg_info "Installing Adafruit Display Library"
pip install adafruit-circuitpython-ssd1306 &>/dev/null
msg_ok "Installed Adafruit Display Library"

msg_info "Starting display program"
python3 /home/pi/weather-pi-project/conf/display.py &>/dev/null
msg_ok "Started display program"

msg_info "Cleaning up"
apt-get autoremove >/dev/null
apt-get autoclean >/dev/null
msg_ok "Cleaned"

echo ""
echo "--------------------------------------------------------------"
echo ""
echo "Script has finished successfully!"
echo ""
echo "Next Steps:"
echo "Please run the following command"
echo "> crontab -e"
echo "Press enter to use the default editor (nano)"
echo ""
echo "Paste the following:"
echo ""
echo "@reboot python3 /home/pi/weather-pi-project/conf/display.py"
echo ""
echo "Now use CTRL+S to save and CTRL+X to exit"
echo "> sudo reboot"
echo ""
echo "You should see an IP adddress on your screen after rebooting :)"
echo ""
echo "--------------------------------------------------------------"
echo ""
