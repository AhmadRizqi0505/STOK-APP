FROM php:8.2-apache

# Install ekstensi mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy semua file project
COPY ./src /var/www/html

# Set permission
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

EXPOSE 80
