FROM php:8.2-apache

# Install PDO MySQL extension for DB connection
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache rewrite module
RUN a2enmod rewrite

# Copy PHP application files
COPY . /var/www/html/

# Expose port 80 inside container
EXPOSE 80