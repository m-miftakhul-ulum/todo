FROM dunglas/frankenphp:php8.3

ENV SERVER_NAME=":80"

WORKDIR /app
COPY . /app

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

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN composer install