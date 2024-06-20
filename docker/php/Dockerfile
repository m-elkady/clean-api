FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y git

RUN apt-get update -yqq && apt-get install -yqq git curl zip unzip gzip libzip-dev libicu-dev nano vim

# Install PHP extensions
RUN docker-php-ext-install intl pdo pdo_mysql zip opcache bcmath sockets

RUN pecl install xdebug && docker-php-ext-enable xdebug opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/backend