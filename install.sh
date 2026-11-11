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

# --- PHP settings ---------------------------------------------------------
PHP_MINOR="8.5"          # branch to track; apt upgrade keeps it on the latest 8.5.x
PHP_MIN="8.5.12"         # refuse to install anything older than this
AUTO_PHP_UPDATES="yes"   # yes = unattended security updates for the PHP repo
PHP_PACKAGES=(
  "php${PHP_MINOR}-cli"
  "libapache2-mod-php${PHP_MINOR}"
  "php${PHP_MINOR}-mysql"
  "php${PHP_MINOR}-curl"
  "php${PHP_MINOR}-gd"
  "php${PHP_MINOR}-zip"
)                        # json is built into PHP 8, there is no php8.5-json package
export DEBIAN_FRONTEND=noninteractive
# ---------------------------------------------------------------------------

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

# Clean failure with our own message (error_exit expects $EXIT/$LINE from the ERR trap)
function fail() {
  trap - ERR
  msg_error "$1"
  exit 1
}

# --- Install / update helpers ---------------------------------------------
WEB_ROOT="/var/www/html"
MANIFEST="/var/lib/espi-weather/web-manifest"   # files we installed (outside the web root), so updates can remove stale ones
DB_NAME="weatherPiProject"
MODE=""                                 # update | fresh (chosen by flag or by the menu)
ASSUME_YES="no"
BACKUP_FILE=""

function usage() {
  echo "Usage: sudo bash install.sh [--update | --fresh] [--yes]"
  echo ""
  echo "  (no option)  Fresh install. If an existing install is found you are asked"
  echo "               whether to update it or reinstall it from scratch."
  echo "  --update     Update the web interface code only. The database (accounts,"
  echo "               API token and sensor data) is NOT touched."
  echo "  --fresh      Reinstall from scratch. Resets accounts and DELETES all"
  echo "               collected data (a backup of the old database is saved first)."
  echo "  --yes, -y    Do not ask for confirmation."
  echo "  --help, -h   Show this help."
}

# Is there already an ESPi database on this machine?
function existing_install() {
  local found
  found="$(mysql -u root -N -e "SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA='${DB_NAME}' AND TABLE_NAME='sensor_data'" 2>/dev/null || true)"
  [ "$found" = "1" ]
}

# Ask a yes/no question. Returns 0 for yes, 1 for no.
function confirm() {
  local yn
  if [ "$ASSUME_YES" = "yes" ]; then
    return 0
  fi
  while true; do
    read -r -p "$1 (y/n) " yn
    case $yn in
      [yY] ) return 0;;
      [nN] ) return 1;;
      * ) echo Invalid response;;
    esac
  done
}

# Menu shown when an existing installation is detected
function choose_mode() {
  local choice
  echo "An existing ESPi installation was found on this system."
  echo ""
  echo "  1) Update    - install the latest web interface code and KEEP your"
  echo "                 database (accounts, API token and sensor data)"
  echo "  2) Reinstall - wipe EVERYTHING (accounts, API token and all sensor"
  echo "                 data) and set up from scratch"
  echo "  3) Cancel"
  echo ""
  while true; do
    read -r -p "Choose an option [1]: " choice
    case "${choice:-1}" in
      1 ) MODE="update"; return;;
      2 ) MODE="fresh"; return;;
      3 ) echo "Exiting..."; exit 0;;
      * ) echo Invalid response;;
    esac
  done
}

# Last chance before an existing database is destroyed
function confirm_wipe() {
  local answer
  echo ""
  echo -e "${RD}WARNING!${CL} This will permanently delete all user accounts, the API token"
  echo "and ALL collected sensor data. A backup of the current database will be"
  echo "saved first."
  echo ""
  if [ "$ASSUME_YES" != "yes" ]; then
    read -r -p "Type RESET to continue: " answer
    if [ "$answer" != "RESET" ]; then
      echo "Exiting..."
      exit 0
    fi
  fi
}

# Dump the current database to /root before it is replaced (readable by root only)
function backup_database() {
  local dest
  dest="/root/${DB_NAME}-backup-$(date +%Y%m%d-%H%M%S).sql"
  ( umask 077; mysqldump -u root --single-transaction "$DB_NAME" > "$dest" ) \
    || fail "Could not back up the database - nothing has been changed"
  BACKUP_FILE="$dest"
}

# Copy the web interface into place.
#   fresh  - empties the web root first
#   update - only removes the files the previous install put there, then copies the new ones
function install_web_files() {
  local f
  mkdir -p "$WEB_ROOT" "$(dirname "$MANIFEST")"
  if [ "$1" = "fresh" ]; then
    rm -rf "${WEB_ROOT:?}"/* &>/dev/null
  elif [ -f "$MANIFEST" ]; then
    while IFS= read -r f; do
      case "$f" in
        ""|/*|*..*) continue;;
      esac
      rm -f -- "${WEB_ROOT}/${f}"
    done < "$MANIFEST"
  fi
  cp -r frontend/. "${WEB_ROOT}/" &>/dev/null
  cp -r backend/. "${WEB_ROOT}/" &>/dev/null
  { (cd frontend && find . -type f); (cd backend && find . -type f); } | sed 's|^\./||' | sort -u > "$MANIFEST"
}

# Best guess at the address the user should browse to
function server_ip() {
  local addr
  addr="$(/sbin/ip -o -4 addr list eth0 2>/dev/null | awk '{print $4}' | cut -d/ -f1 || true)"
  if [ -z "$addr" ]; then
    addr="$(hostname -I 2>/dev/null | awk '{print $1}' || true)"
  fi
  printf '%s' "${addr:-<raspberry-pi-ip>}"
}

# Update an existing install: new web interface code, database left exactly as it is
function run_update() {
  msg_info "Updating web interface"
  install_web_files update
  msg_ok "Updated web interface"

  echo ""
  echo "-------------------------------------------------------------------"
  echo ""
  echo "Update complete. Your database was not touched - accounts, the API"
  echo "token and all collected sensor data are unchanged."
  echo ""
  echo "Server Address: https://$(server_ip)/"
  echo ""
  echo "-------------------------------------------------------------------"
  echo ""
}

# Add the repo that carries current PHP builds (Debian / Raspberry Pi OS / Ubuntu)
function add_php_repo() {
  local os_id codename
  os_id="$(. /etc/os-release && printf '%s' "${ID:-}")"
  codename="$(. /etc/os-release && printf '%s' "${VERSION_CODENAME:-}")"
  case "$os_id" in
    debian|raspbian)
      apt-get install -y ca-certificates curl lsb-release apt-transport-https &>/dev/null
      curl -fsSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb
      dpkg -i /tmp/debsuryorg-archive-keyring.deb &>/dev/null
      rm -f /tmp/debsuryorg-archive-keyring.deb
      echo "deb [signed-by=/usr/share/keyrings/debsuryorg-archive-keyring.gpg] https://packages.sury.org/php/ ${codename} main" > /etc/apt/sources.list.d/php.list
      ;;
    ubuntu)
      apt-get install -y software-properties-common ca-certificates &>/dev/null
      add-apt-repository -y ppa:ondrej/php &>/dev/null
      ;;
    *)
      fail "Unsupported OS '${os_id}' - need Debian, Raspberry Pi OS or Ubuntu"
      ;;
  esac
  apt-get update &>/dev/null
}

# Make sure the repo really offers PHP_MIN or newer before we touch anything
function check_php_candidate() {
  local cand upstream
  cand="$(apt-cache policy "php${PHP_MINOR}-cli" | awk '/Candidate:/ {print $2}')"
  if [ -z "$cand" ] || [ "$cand" = "(none)" ]; then
    fail "PHP ${PHP_MINOR} is not available for this OS (Ubuntu 20.04 and other EOL releases are not supported)"
  fi
  upstream="$(printf '%s' "$cand" | sed -E 's/^[0-9]+://; s/-.*$//')"
  if ! dpkg --compare-versions "$upstream" ge "$PHP_MIN"; then
    fail "Repository only offers PHP ${upstream}, need ${PHP_MIN} or newer - try again later"
  fi
  PHP_CANDIDATE="$upstream"
}

# Unattended security updates for the PHP repo only (plus whatever is already allowed)
function enable_php_auto_updates() {
  local os_id pattern
  os_id="$(. /etc/os-release && printf '%s' "${ID:-}")"
  apt-get install -y unattended-upgrades &>/dev/null
  if [ "$os_id" = "ubuntu" ]; then
    pattern='"origin=LP-PPA-ondrej-php,codename=${distro_codename}";'
  else
    pattern='"site=packages.sury.org";'
  fi
  {
    printf 'Unattended-Upgrade::Origins-Pattern {\n\t%s\n};\n' "$pattern"
    printf 'APT::Periodic::Update-Package-Lists "1";\n'
    printf 'APT::Periodic::Unattended-Upgrade "1";\n'
  } > /etc/apt/apt.conf.d/52espi-php-autoupdate
}

echo ""
echo "-------------------------------------------------------------------"
echo "  ______  _____ _____ _     ____             _                  _  "
echo " |  ____|/ ____|  __ (_)   |  _ \    V1.3   | |  github/wzern  | | "
echo " | |__  | (___ | |__) |    | |_) | __ _  ___| | _____ _ __   __| | "
echo " |  __|  \___ \|  ___/ |   |  _ < / _' |/ __| |/ / _ \ '_ \ / _' | "
echo " | |____ ____) | |   | |   | |_) | (_| | (__|   <  __/ | | | (_| | "
echo " |______|_____/|_|   |_|   |____/ \__,_|\___|_|\_\___|_| |_|\__,_| "
echo ""
echo "-------------------------------------------------------------------"
echo ""
# Always work from the folder this script lives in (it copies ./frontend, ./backend and ./conf)
cd "$(dirname "$(readlink -f "${BASH_SOURCE[0]}")")"

for arg in "$@"; do
  case "$arg" in
    --update ) MODE="update";;
    --fresh|--reinstall ) MODE="fresh";;
    -y|--yes ) ASSUME_YES="yes";;
    -h|--help ) usage; exit 0;;
    * ) usage; fail "Unknown option: $arg";;
  esac
done

if [ "$(id -u)" -ne 0 ]; then
  fail "Please run this script as root, e.g. sudo bash install.sh"
fi

echo "ESPi Weather Station Backend: Stable"
echo ""

# Decide between a fresh install and an update
HAS_INSTALL="no"
if existing_install; then
  HAS_INSTALL="yes"
fi

if [ "$MODE" = "update" ] && [ "$HAS_INSTALL" = "no" ]; then
  fail "No existing ESPi installation was found to update - run without --update for a fresh install"
fi

if [ -z "$MODE" ]; then
  if [ "$HAS_INSTALL" = "yes" ]; then
    choose_mode
  else
    MODE="fresh"
  fi
fi

if [ "$MODE" = "update" ]; then
  echo "Updating the web interface only. Your database will not be touched."
  echo ""
  run_update
  exit 0
fi

# ---- Fresh install from here on ----
echo "This script is designed for use on Raspberry Pi 4 (Raspberry Pi OS)."
echo "Other Debian 11/12/13 and Ubuntu 24.04+ systems should also work."
echo "PHP ${PHP_MINOR} (${PHP_MIN} or newer) is installed from the Sury repository."
echo ""

if [ "$HAS_INSTALL" = "yes" ]; then
  confirm_wipe
  msg_info "Backing up the existing database"
  backup_database
  msg_ok "Backed up the existing database to ${BACKUP_FILE}"
else
  echo "This will install and configure Apache2, MariaDB and PHP, and set up a"
  echo "new database and web interface."
  echo ""
  if ! confirm "Do you want to proceed?"; then
    echo "Exiting..."
    exit 0
  fi
fi
echo ""

msg_info "Updating system packages"
apt update &>/dev/null
apt upgrade -y &>/dev/null
msg_ok "Updated system packages"

msg_info "Installing Fail2Ban"
apt-get install fail2ban -y &>/dev/null
msg_ok "Installed Fail2Ban"

msg_info "Installing Apache2"
apt-get install apache2 -y &>/dev/null
msg_ok "Installed Apache2"

msg_info "Installing MariaDB"
apt-get install mariadb-server -y &>/dev/null
msg_ok "Installed MariaDB"

msg_info "Adding PHP ${PHP_MINOR} repository"
add_php_repo
check_php_candidate
msg_ok "Added PHP repository (PHP ${PHP_CANDIDATE} available)"

msg_info "Installing PHP ${PHP_MINOR} and modules"
apt-get install -y "${PHP_PACKAGES[@]}" &>/dev/null
a2enmod "php${PHP_MINOR}" &>/dev/null
update-alternatives --set php "/usr/bin/php${PHP_MINOR}" &>/dev/null
systemctl restart apache2 &>/dev/null
msg_ok "Installed PHP $(php -r 'echo PHP_VERSION;')"

if [ "$AUTO_PHP_UPDATES" = "yes" ]; then
  msg_info "Enabling automatic PHP security updates"
  enable_php_auto_updates
  msg_ok "Enabled automatic PHP security updates"
fi

msg_info "Enabling Apache2 rewrite module"
a2enmod rewrite &>/dev/null
systemctl restart apache2 &>/dev/null
msg_ok "Enabled Apache2 rewrite module"

msg_info "Enabling HTTPS"
a2dismod mpm_event mpm_worker &>/dev/null || true
a2enmod mpm_prefork &>/dev/null
a2enmod ssl &>/dev/null
systemctl reset-failed apache2.service || true
systemctl restart apache2 &>/dev/null

openssl req -x509 -sha256 -days 3560 -nodes -newkey rsa:2048 \
  -subj "/CN=weatherpi.local/C=US/L=San Fransisco" \
  -keyout /etc/ssl/private/apache-selfsigned.key \
  -out /etc/ssl/certs/apache-selfsigned.crt &>/dev/null

rm -rf /etc/apache2/sites-enabled/* &>/dev/null
cp ./conf/apache2/weatherpi.conf /etc/apache2/sites-enabled/weatherpi.conf &>/dev/null

if ! apache2ctl configtest &>/dev/null; then
  fail "Apache configuration test failed after enabling weatherpi.conf"
fi

systemctl reset-failed apache2.service || true
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
install_web_files fresh
msg_ok "Initialised web interface"
  
msg_info "Cleaning up"
apt-get autoremove >/dev/null
apt-get autoclean >/dev/null
msg_ok "Cleaned"

echo ""
echo "-------------------------------------------------------------------"
echo ""
echo "Server Address: https://$(server_ip)/"
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
