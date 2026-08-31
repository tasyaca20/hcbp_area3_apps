#!/bin/sh
set -eu

# Vercel's PHP builder installs Composer dependencies before buildCommand.
# Clear any compiled Laravel manifests restored from a previous deployment.
rm -f bootstrap/cache/*.php

# Rebuild Laravel's package manifest from the freshly installed vendor tree.
php artisan package:discover --ansi
php artisan config:clear --ansi || true
php artisan route:clear --ansi || true
php artisan view:clear --ansi || true

# Build frontend assets.
npm install --include=dev --no-package-lock --no-audit --no-fund --ignore-scripts=false
npm run build
