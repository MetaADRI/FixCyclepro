#!/bin/sh
# Runs before CMD or any dockerCommand override (web + worker).
# storage/ is empty when the Render disk first mounts, and Laravel requires
# these directories to exist (sessions, view cache, log channel "daily").
set -e

cd /var/www/html

mkdir -p \
    storage/app \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

exec "$@"
