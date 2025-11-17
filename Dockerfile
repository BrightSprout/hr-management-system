FROM php:8.2-apache

# Enable required PHP extensions
RUN docker-php-ext-install pdo_mysql

# Enable mod_rewrite
RUN a2enmod rewrite

# Set default ServerName to suppress notice
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Configure Apache to listen on port 8080
RUN sed -i 's/80/8080/' /etc/apache2/ports.conf
RUN sed -i 's/:80>/:8080>/' /etc/apache2/sites-available/000-default.conf

# Copy your app
COPY . /var/www/html/

# Allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Set permissions for Apache
RUN chown -R www-data:www-data /var/www/html

# Expose Railway default port
EXPOSE 8080

# Start Apache in foreground
CMD ["apache2-foreground"]

