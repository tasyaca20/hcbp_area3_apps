#!/bin/sh
set -eu

# Database connection settings are provided by Vercel environment variables.
# Supabase PostgreSQL settings are configured separately for the preview environment.
# Keep bootstrap/cache available during Composer package discovery.
mkdir -p bootstrap/cache
chmod 775 bootstrap/cache 2>/dev/null || true

# Vercel's PHP builder installs Composer dependencies.
npm run build
