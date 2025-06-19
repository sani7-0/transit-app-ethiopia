# Use official PHP with Apache image
FROM php:8.2-apache

# Enable URL rewriting
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all files into web server root
COPY . /var/www/html/

# Optional: give proper permissions
RUN chown -R www-data:www-data /var/www/html

# Expose default port
EXPOSE 80
