#!/bin/bash

# Test against all supported PHP versions using Docker
# Requires Docker to be installed and running

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

PHP_VERSIONS=("8.2" "8.3" "8.4")

echo "=========================================="
echo "Testing against PHP versions: ${PHP_VERSIONS[*]}"
echo "=========================================="

for version in "${PHP_VERSIONS[@]}"; do
    echo ""
    echo "=========================================="
    echo "Testing PHP $version"
    echo "=========================================="
    
    docker run --rm -v "$PROJECT_ROOT:/app" -w /app \
        "php:${version}-cli" \
        sh -c "apt-get update && apt-get install -y git unzip && \
               curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
               composer install --no-interaction --prefer-dist && \
               composer test"
    
    echo "✅ PHP $version tests passed"
done

echo ""
echo "=========================================="
echo "✅ All PHP versions passed!"
echo "=========================================="
