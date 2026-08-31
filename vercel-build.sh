#!/bin/sh
set -eu
npm install --include=dev --no-audit --no-fund
npm run build
