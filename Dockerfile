FROM php:8.4.10-fpm-alpine

RUN apk -U upgrade \
    && apk add libzip-dev unzip \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

EXPOSE 9000

CMD ["php-fpm"]
