FROM php:8.4-fpm

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev \
        zlib1g-dev \
        libxml2-dev \
        libzip-dev \
        libonig-dev \
        libpq-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libicu-dev \
        git \
        curl \
        unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install mbstring exif pcntl zip pdo pdo_pgsql pgsql intl \
    && docker-php-source delete \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY ./docker/php/conf/custom-settings.ini /usr/local/etc/php/conf.d/custom-settings.ini

WORKDIR /var/www/html/cn.bookssmf

# RUN chown -R www-data:www-data /var/www/cn.bookssmf
