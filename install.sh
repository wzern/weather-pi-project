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
echo "-------------------------------------------------------------------"
echo "  ______  _____ _____ _     ____             _                  _  "
echo " |  ____|/ ____|  __ (_)   |  _ \    V1.1   | |  github/wzern  | | "
echo " | |__  | (___ | |__) |    | |_) | __ _  ___| | _____ _ __   __| | "
echo " |  __|  \___ \|  ___/ |   |  _ < / _' |/ __| |/ / _ \ '_ \ / _' | "
echo " | |____ ____) | |   | |   | |_) | (_| | (__|   <  __/ | | | (_| | "
echo " |______|_____/|_|   |_|   |____/ \__,_|\___|_|\_\___|_| |_|\__,_| "
echo ""
echo "-------------------------------------------------------------------"
echo ""
echo "You are about to install the ESPi Weather Station Backend: Stable"
echo "This script is designed for use on Rapsberry Pi 4 only. However,"
echo "a system with Ubuntu 20.04 has been tested and works well"
echo ""
echo "WARNING! Running this script more than once will reset all user"
echo "accounts, previously collected data, and will completely re-generate"
echo "the web-interface and databases. Only run this script again if you"
echo "encounter problems or have forgotten your password"
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

msg_info "Updating system packages"
apt update &>/dev/null
apt upgrade -y &>/dev/null
msg_ok "Updated system packages"

msg_info "Installing Apache2"
apt-get install apache2 -y &>/dev/null
msg_ok "Installed Apache2"

msg_info "Installing MariaDB"
apt-get install mariadb-server -y &>/dev/null
msg_ok "Installed MariaDB"

msg_info "Installing PHP Modules"
apt-get install php libapache2-mod-php php-mysql php-curl php-gd php-json php-zip -y &>/dev/null
msg_ok "Installed PHP Modules"

msg_info "Installing phpMyAdmin"
export DEBIAN_FRONTEND=noninteractive
apt-get -yq install phpmyadmin &>/dev/null
cp ./conf/phpmyadmin.conf /etc/dbconfig-common/phpmyadmin.conf &>/dev/null
dpkg-reconfigure --frontend=noninteractive phpmyadmin &>/dev/null
systemctl restart apache2 &>/dev/null
msg_ok "Installed phpMyAdmin"

msg_info "Enabling Apache2 rewrite module"
a2enmod rewrite &>/dev/null
systemctl restart apache2 &>/dev/null
msg_ok "Enabled Apache2 rewrite module"

msg_info "Enabling HTTPS"
a2enmod ssl &>/dev/null
systemctl restart apache2 &>/dev/null
openssl req -x509 -sha256 -days 3560 -nodes -newkey rsa:2048 -subj "/CN=weatherpi.local/C=US/L=San Fransisco" -keyout /etc/ssl/private/apache-selfsigned.key -out /etc/ssl/certs/apache-selfsigned.crt &>/dev/null
rm -rf  /etc/apache2/sites-enabled/* &>/dev/null
cp ./conf/apache2/weatherpi.conf /etc/apache2/sites-enabled/weatherpi.conf &>/dev/null
systemctl restart apache2 &>/dev/null
msg_ok "Enabled HTTPS"

msg_info "Preparing database"
mysql -u root -e "DROP DATABASE IF EXISTS weatherPiProject;"
mysql -u root -e "CREATE DATABASE weatherPiProject;"
mysql -u root -e "CREATE USER IF NOT EXISTS 'weatherPi'@'localhost' IDENTIFIED BY '4Iz0p3hu9nSJujKz3kPM';"
mysql -u root -e "GRANT ALL PRIVILEGES ON weatherPiProject.* TO 'weatherPi'@'localhost' WITH GRANT OPTION;"
mysql -u root < conf/database.sql
msg_ok "Prepared database"

msg_info "Initialising web interface"
rm -rf /var/www/html/* &>/dev/null
cp -r frontend/* /var/www/html/ &>/dev/null
cp -r backend/* /var/www/html/ &>/dev/null
ln -s /usr/share/phpmyadmin /var/www/html &>/dev/null
msg_ok "Initialised web interface"
  
msg_info "Cleaning up"
apt-get autoremove >/dev/null
apt-get autoclean >/dev/null
msg_ok "Cleaned"

echo ""
echo "-------------------------------------------------------------------"
echo ""
echo Server Address: https://$(/sbin/ip -o -4 addr list eth0 | awk '{print $4}' | cut -d/ -f1)/
echo ""
echo "Default Username: admin"
echo "Default Password: password"
echo "Default API Token: demo"
echo ""
echo "Please change the password and token as soon as possible in the"
echo "settings menu!"
echo ""
echo "-------------------------------------------------------------------"
echo ""
