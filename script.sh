#!/usr/bin/env bash

set -e

# ==============================
# CONFIG
# ==============================
APP_NAME="todo"
APP_DIR="/var/www/${APP_NAME}"
REPO_URL="https://github.com/m-miftakhul-ulum/todo.git"


# ==============================
# SYSTEM UPDATE
# ==============================
sudo apt update -y

# ==============================
# INSTALL FRANKENPHP
# ==============================
curl -LO https://github.com/dunglas/frankenphp/releases/latest/download/frankenphp-linux-x86_64
chmod +x frankenphp-linux-x86_64
sudo mv frankenphp-linux-x86_64 /usr/local/bin/frankenphp

frankenphp --version

# ==============================
# INSTALL PHP EXTENSIONS
# ==============================
sudo apt install -y \
    php-cli \
    php-mbstring \
    php-xml \
    php-curl \
    php-zip \
    php-gd \
    php-intl \
    php-mysql

# ==============================
# SETUP APP DIRECTORY
# ==============================
sudo mkdir -p /var/www
sudo chown -R $USER:$USER /var/www

cd /var/www
git clone ${REPO_URL} ${APP_NAME}

cd ${APP_DIR}

# ==============================
# INSTALL COMPOSER
# ==============================
cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

cd ${APP_DIR}
composer install --no-dev --optimize-autoloader

# ==============================
# LARAVEL SETUP
# ==============================
cp .env.example .env
php artisan key:generate

# ==============================
# CADDYFILE CONFIG
# ==============================
cat <<EOF > ${APP_DIR}/Caddyfile
:80 {
    root * public
    php_server
    file_server
}

EOF

# ==============================
# SYSTEMD SERVICE
# ==============================
sudo tee /etc/systemd/system/frankenphp.service > /dev/null <<EOF
[Unit]
Description=FrankenPHP Server
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=${APP_DIR}
ExecStart=/usr/local/bin/frankenphp run --config ${APP_DIR}/Caddyfile
Restart=always
AmbientCapabilities=CAP_NET_BIND_SERVICE
CapabilityBoundingSet=CAP_NET_BIND_SERVICE

[Install]
WantedBy=multi-user.target
EOF

# ==============================
# PERMISSIONS
# ==============================
sudo chown -R www-data:www-data ${APP_DIR}
sudo chmod -R 775 ${APP_DIR}/storage ${APP_DIR}/bootstrap/cache

# ==============================
# ENABLE SERVICE
# ==============================
sudo systemctl daemon-reload
sudo systemctl enable frankenphp
sudo systemctl start frankenphp

# ==============================
# STATUS
# ==============================
systemctl status frankenphp --no-pager

echo "======================================"
echo "FrankenPHP + Laravel installed"
echo "App Dir : ${APP_DIR}"

echo "======================================"
