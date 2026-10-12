#!/bin/sh
set -eu

# Keep bootstrap/cache available during Composer package discovery.
mkdir -p bootstrap/cache
chmod 775 bootstrap/cache 2>/dev/null || true

# Vercel's PHP builder installs Composer dependencies.
npm run build
