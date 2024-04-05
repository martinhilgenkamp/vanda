FROM php:8.2-apache

# Install system dependencies.
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache modules required for Laravel.
RUN a2enmod rewrite

# Install PHP extensions.
#RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
#    && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql

# Enable mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install Composer globally.
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create a directory for your Laravel application.
WORKDIR /var/www/html

# Copy the Vanda application files into the container.
COPY . .

# Copy custom php.ini
COPY php.ini /usr/local/etc/php/php.ini

# Install Vanda dependencies using Composer.
#RUN composer install --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

# Expose port 80 for Apache.
EXPOSE 80

# Start Apache web server.
CMD ["apache2-foreground"]
