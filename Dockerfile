# Use official PHP with Apache
FROM php:8.2-apache

# Copy your PHP file(s) into Apache's web directory
COPY . /var/www/html/

# Expose port 80 for the web server
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
