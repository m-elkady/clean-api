FROM dunglas/frankenphp:latest AS frankenphp_base

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends git curl zip unzip nano vim bash

# Install PHP extensions that are needed
RUN install-php-extensions intl pdo_mysql zip bcmath sockets opcache

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

VOLUME /app/var/

# Copy Caddyfile for FrankenPHP configuration
COPY Caddyfile /etc/caddy/Caddyfile

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose port for HTTP
EXPOSE 8080

# Use entrypoint script
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
