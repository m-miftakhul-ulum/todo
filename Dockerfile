# Menggunakan image resmi FrankenPHP
FROM dunglas/frankenphp:latest-php8.3

# 1. Install dependencies sistem dan ekstensi PHP yang dibutuhkan Laravel
RUN apt-get update && apt-get install -y \
    libzip-dev \
    libpng-dev \
    libicu-dev \
    unzip \
    && docker-php-ext-install \
    zip \
    gd \
    intl \
    pdo_mysql \
    bcmath \
    opcache

# 2. Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 3. Pengaturan Working Directory
WORKDIR /app

# 4. Copy source code Laravel
COPY . .

# 5. Install dependencies Laravel (tanpa dev dependencies untuk prod)
RUN composer install --no-dev --optimize-autoloader

# 6. Set permission untuk storage dan cache
RUN chown -R www-data:www-data storage bootstrap/cache

# 7. Konfigurasi Environment FrankenPHP
# Mengaktifkan Laravel Worker Mode untuk performa maksimal
ENV FRANKENPHP_CONFIG="worker ./public/index.php"
ENV PHP_INI_SCAN_DIR=":/usr/local/etc/php/conf.d"

# 8. Ekspos port (Caddy default 80 dan 443)
EXPOSE 80
EXPOSE 443
EXPOSE 443/udp

# 9. Jalankan FrankenPHP
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]