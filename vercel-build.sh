#!/bin/sh
set -eu

# Never ship stale Laravel compiled manifests from a previous build cache.
rm -f bootstrap/cache/*.php 2>/dev/null || true

# Vercel's PHP builder installs Composer dependencies.
# Only build the frontend assets here.
npm run build
