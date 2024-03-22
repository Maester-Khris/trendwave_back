
FROM php:8.1-apache

# install dependencies and necessary scripts
RUN apt-get update && apt-get install -y \
    libpq-dev \
    libzip-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/* \
    && a2enmod rewrite \
    && docker-php-ext-install pdo_pgsql zip

# set working dir
WORKDIR /var/www/html

# Laravel config: copy the folder and set permission to access and modif storage and bootstrap folder
COPY . /var/www/html
RUN chown -R www-data:www-data /var/www/html /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel: install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader

# we suppose that apache config are unnecessary since we deploying on live server
COPY apache.conf /etc/apache2/sites-available/000-default.conf 
# RUN a2enmod rewrite

EXPOSE 80