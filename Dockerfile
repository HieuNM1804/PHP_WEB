FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql

COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html/assets/uploads
