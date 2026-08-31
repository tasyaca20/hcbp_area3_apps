#!/bin/sh
set -eu

# Never reuse Laravel's compiled service/config manifests from a previous Vercel build.
rm -f bootstrap/cache/*.php

# Build the PHP dependency tree from composer.lock on every deployment.
composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

# Rebuild Laravel's package manifest after the fresh dependency install.
php artisan package:discover --ansi
php artisan config:clear --ansi || true
php artisan route:clear --ansi || true
php artisan view:clear --ansi || true

# Build frontend assets.
npm install --include=dev --no-package-lock --no-audit --no-fund --ignore-scripts=false
npm run build
