# https://phpfpm85.webhosting-infos.hosting.ovh.net/

FROM composer:latest AS composer
FROM php:8.5.7-fpm-bookworm

WORKDIR /var/www/ghibliql

RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        unzip \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/* \
    && chown -R www-data:www-data /var/www/ghibliql

COPY --from=composer /usr/bin/composer /usr/local/bin/composer

CMD ["php-fpm"]

EXPOSE 9000
