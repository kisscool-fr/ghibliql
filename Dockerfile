# https://devcenter.heroku.com/articles/php-support#supported-versions
# https://docs.travis-ci.com/user/languages/php/#php-versions
FROM php:8.1.16-fpm-alpine

RUN apk -U upgrade \
    && apk add libzip-dev unzip \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
    && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
    && rm composer-setup.php

CMD ["php-fpm"]

EXPOSE 9000
