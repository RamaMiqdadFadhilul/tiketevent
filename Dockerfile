FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y libpq-dev \
    && docker-php-ext-install pgsql \
    && rm -rf /var/lib/apt/lists/*

RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/html/

# Buat folder upload dan berikan izin ke Apache
RUN mkdir -p /var/www/html/img \
    && chown -R www-data:www-data /var/www/html/img \
    && chmod -R 775 /var/www/html/img

RUN a2enmod rewrite

EXPOSE 10000

CMD ["apache2-foreground"]