#!/bin/sh
set -eu

# Production deployment trigger: Laravel reads the Railway MySQL settings from
# Vercel Production Environment Variables / vercel.json at runtime.
# Keep bootstrap/cache available during Composer package discovery.
mkdir -p bootstrap/cache
chmod 775 bootstrap/cache 2>/dev/null || true

# Vercel's PHP builder installs Composer dependencies.
npm run build
