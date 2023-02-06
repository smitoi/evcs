FROM php:8.2-fpm-alpine

ARG UID
ARG GID

RUN apk add --no-cache \
    g++ \
    nodejs \
    npm \
    make \
    zip \
    unzip \
    libpq-dev

RUN docker-php-ext-install \
        pdo_pgsql

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /srv/www

RUN addgroup -g ${GID} --system laravel
RUN adduser -u ${UID} -G laravel -s /bin/sh --system -D laravel

RUN chown -R laravel:laravel /srv/www
RUN chmod -R 755 /srv/www/storage

USER laravel

CMD ["php-fpm"]
