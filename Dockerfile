FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install necessary PHP extensions
RUN apt-get update && apt-get install -y libcurl4-openssl-dev \
    && docker-php-ext-install mysqli curl json

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . /var/www/html/

# Fix permissions
RUN chown -R www-data:www-data /var/www/html
