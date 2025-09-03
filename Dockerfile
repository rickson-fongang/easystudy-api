# Use PHP with Apache
FROM php:8.2-apache

# Enable mysqli (needed for MySQL connection)
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Copy all your project files into the web root
COPY . /var/www/html/

# Give Apache access
RUN chown -R www-data:www-data /var/www/html

# Expose web port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
