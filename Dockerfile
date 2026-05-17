FROM php:8.2.28-fpm AS base

RUN apt update
RUN apt install -y libxml2-dev libpng-dev libwebp-dev libfreetype6-dev libjpeg62-turbo-dev
RUN apt install -y libzip-dev zip
RUN apt install -y build-essential libmagickwand-dev
RUN apt clean all
RUN pecl install imagick

RUN apt install -y libpq-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql
RUN docker-php-ext-configure zip
RUN docker-php-ext-install zip
RUN docker-php-ext-configure gd --enable-gd --with-freetype --with-jpeg --with-webp
RUN docker-php-ext-install gd
RUN docker-php-ext-install xml
RUN docker-php-ext-enable pdo_pgsql
RUN docker-php-ext-enable imagick

# Update pm.max_children
RUN sed -i 's/^pm\.max_children = .*/pm.max_children = 20/' /usr/local/etc/php-fpm.d/www.conf

# install composer
COPY --from=composer/composer:2.2.18 /usr/bin/composer /usr/bin/composer

ADD ./docker/gke/php.ini /usr/local/etc/php/php.ini

# set time zone
ENV TZ=Asia/Jakarta
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

FROM base AS app

WORKDIR /var/www
ADD . /var/www/

# RUN mkdir -p storage/logs && ln -sf /dev/stdout storage/logs/laravel.log

RUN composer install
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear
RUN php artisan storage:link