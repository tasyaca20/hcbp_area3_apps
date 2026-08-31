#!/bin/sh
set -eu

# Do not ship compiled Laravel manifests from an older Vercel cache.
rm -f bootstrap/cache/*.php

# Build frontend assets. PHP dependencies are handled by the Vercel PHP runtime builder.
npm install --include=dev --no-package-lock --no-audit --no-fund --ignore-scripts=false
npm run build
