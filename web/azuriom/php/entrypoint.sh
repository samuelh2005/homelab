#!/bin/sh
set -eu

WEB_ROOT="/var/www/html"
TEMPLATE_DIR="/usr/src/azuriom-template"

mkdir -p "$WEB_ROOT"

# Seed the mounted web root only on first start, preserving any user changes later.
if [ ! -f "$WEB_ROOT/public/index.php" ]; then
  cp -a "$TEMPLATE_DIR"/. "$WEB_ROOT"/
fi

# Ensure the PHP process can read/write the application files when the host folder is mounted.
chown -R www-data:www-data "$WEB_ROOT" || true

exec docker-php-entrypoint "$@"
