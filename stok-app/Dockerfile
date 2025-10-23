FROM php:8.2-apache

# Install dependency dan ekstensi PHP
RUN apt-get update && apt-get install -y libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql gd

# Copy semua file project
COPY ./src /var/www/html

# Set permission
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Pastikan Apache menggunakan dokumen root yang benar
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -ri -e 's!/var/www/html!/var/www/html!g' /etc/apache2/sites-available/*.conf && \
    sed -ri -e 's!/var/www/!/var/www/html!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

EXPOSE 80
