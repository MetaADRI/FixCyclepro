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

# Passport signing keys are gitignored (.gitignore: /storage/oauth-*.key) and the
# Render disk mounts empty over storage/, so they are absent on a fresh boot.
# Without them every token request fails with "Invalid key supplied", which the
# web login/signup flow surfaces as "Attempt to read property access_token on null".
# Generate once; the Render disk keeps them for subsequent deploys.
if [ ! -f storage/oauth-private.key ] || [ ! -f storage/oauth-public.key ]; then
    php artisan passport:keys --force || echo "WARNING: passport:keys failed"
fi

exec "$@"
