#!/bin/sh
set -eu
npm install --include=dev --no-audit --no-fund --ignore-scripts=false
npm run build
