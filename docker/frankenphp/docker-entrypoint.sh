#!/bin/bash
set -e

# If the first argument is "composer", run composer directly
if [ "$1" = "composer" ]; then
    exec composer "$@"
fi

# Install dependencies if vendor directory doesn't exist
if [ ! -d "/app/vendor" ]; then
    echo "Vendor directory not found. Installing Composer dependencies..."
    composer install --no-interaction --no-progress
fi

# Run FrankenPHP
exec frankenphp run --config /etc/caddy/Caddyfile "$@"