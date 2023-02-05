FROM php:8.2-fpm-alpine

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

CMD ["chown", "-R", "www-data:www-data", "/srv/www"]
CMD ["chmod", "-R", "755", "/srv/www/storage"]

CMD ["php-fpm"]
