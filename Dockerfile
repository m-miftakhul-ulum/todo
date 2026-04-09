
FROM composer:2.2 AS vendor

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --ignore-platform-reqs \
    --no-interaction \
    --no-scripts \
    --prefer-dist





FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":8080"

# Buat user non-root
RUN useradd -u 1000 -m appuser

COPY . /app

# Install dependencies (masih root)
RUN apt-get update && apt-get install -y \
    zip \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libcurl4-openssl-dev \
    libonig-dev \
    libicu-dev \
    libsodium-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install \
        zip \
        intl \
        mbstring \
        xml \
        curl \
        mysqli \
        pdo_mysql \
        sodium \
        bcmath \
    && docker-php-ext-enable zip sodium bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*




# Install composer
# COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

# Set permission ke user baru
RUN mkdir -p /config/caddy /data/caddy /etc/frankenphp \
    && chown -R appuser:appuser /app /config /data /etc/frankenphp \
    && chmod -R 775 /app/storage /app/bootstrap/cache


# Pindah ke user non-root
USER appuser

# Install dependency sebagai user biasa (optional tapi lebih aman)
# RUN composer install \
#     --no-dev \
#     --optimize-autoloader \
#     --no-interaction \
#     --prefer-dist \
#     --no-scripts

RUN php artisan package:discover --ansi || true

ENTRYPOINT ["frankenphp", "php-server", "--listen", ":8080", "--root", "public/"]