FROM php:8.3-apache
RUN docker-php-ext-install pdo_mysql
COPY php.ini /usr/local/etc/php/conf.d/api.ini
COPY src/ /var/www/html/
