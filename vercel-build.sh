#!/bin/sh
set -eu

# Laravel's ProviderRepository needs bootstrap/cache to exist during Composer
# package discovery. Keep the directory in the deployment and let the Vercel
# PHP builder generate fresh manifests instead of deleting them here.
mkdir -p bootstrap/cache
chmod 775 bootstrap/cache 2>/dev/null || true

# Vercel's PHP builder installs Composer dependencies.
npm run build
