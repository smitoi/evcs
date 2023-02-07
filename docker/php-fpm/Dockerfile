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
    libpq-dev \
    autoconf

RUN docker-php-ext-install \
    pdo_mysql

RUN pecl install -o -f redis \
    &&  docker-php-ext-enable redis

RUN php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/bin/ --filename=composer

WORKDIR /srv/www

RUN addgroup -g ${GID} --system laravel
RUN adduser -u ${UID} -G laravel -s /bin/sh --system -D laravel

CMD ["chown", "-R", "laravel:laravel", "/srv/www"]
CMD ["chmod", "-R", "755", "/srv/www/storage"]

USER laravel

CMD ["php-fpm"]
