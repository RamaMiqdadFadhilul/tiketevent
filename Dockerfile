FROM php:8.2-apache

RUN docker-php-ext-install pgsql

COPY . /var/www/html/

RUN a2enmod rewrite

EXPOSE 10000

ENV APACHE_DOCUMENT_ROOT=/var/www/html

CMD ["apache2-foreground"]