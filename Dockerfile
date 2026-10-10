# Fixcycle — Render Docker runtime
# Render has no native PHP runtime (blueprint schema enum: docker, node, python,
# ruby, go, rust, elixir, image, static), so PHP services run from this image.
# Requirements from composer.json: php ^8.1, ext-gd, ext-soap.
# Port target (Neon/Postgres): ext-pdo_pgsql.

# Layer 0: Vite asset build (Tailwind/Alpine resources/views wired via inputs).
FROM node:20-bookworm-slim AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY . .
RUN npm run build

FROM php:8.2-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        libonig-dev \
        libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        gd \
        pdo_pgsql \
        zip \
        intl \
        soap \
        bcmath \
        mbstring \
        exif \
        pcntl \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1 \
    PHP_CLI_SERVER_WORKERS=4

WORKDIR /var/www/html

# Layer 1: dependencies from the lockfile only (cached unless composer files change).
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist

# Layer 2: application code.
# .dockerignore excludes .env, vendor, storage contents and .git — the image
# must never contain secrets; Render injects env vars at runtime.
COPY . .
# Vite build output (public/build is gitignored, produced by the assets stage).
COPY --from=assets /app/public/build /var/www/html/public/build
RUN mkdir -p \
        storage/app \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && composer dump-autoload --no-dev --optimize --no-interaction

# Entrypoint creates storage dirs again at boot: the Render disk mounts
# empty over /var/www/html/storage on first run.
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/bin/sh", "/usr/local/bin/docker-entrypoint.sh"]

# Web service default. Render's $PORT is injected at runtime.
# PHP_CLI_SERVER_WORKERS gives artisan serve basic concurrency.
CMD ["sh", "-c", "exec php artisan serve --host 0.0.0.0 --port ${PORT:-10000}"]
