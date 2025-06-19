FROM php:8.2-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install mysqli (json and curl are built-in)
RUN docker-php-ext-install mysqli

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
