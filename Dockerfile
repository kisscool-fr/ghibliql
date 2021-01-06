# https://devcenter.heroku.com/articles/php-support#supported-versions
FROM php:7.4.13-fpm

RUN apt-get update \
    && apt-get install -y zlib1g-dev git libzip-dev zip unzip \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer --version=1.10.19 \
    && rm composer-setup.php

CMD ["php-fpm"]

EXPOSE 9000
