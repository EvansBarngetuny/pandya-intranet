# =========================================================
# Stage 1: PHP dependencies (Builder)
# =========================================================
FROM php:8.4-cli AS php-builder

ENV PKG_CONFIG_PATH=/usr/lib/x86_64-linux-gnu/pkgconfig:/usr/lib/pkgconfig:/usr/share/pkgconfig

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    pkg-config \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg=/usr \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        opcache \
        intl \
        zip \
        bcmath \
        gd \
        xml \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

# Remove dev-only config file that breaks package:discover with --no-dev
RUN rm -f config/scribe.php || true

# Install dependencies WITHOUT running scripts
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --no-scripts

# Manually generate optimized autoloader
RUN composer dump-autoload --optimize --no-dev --no-scripts


# =========================================================
# Stage 2: Frontend (Vite build)
# =========================================================
FROM node:22-alpine AS frontend

WORKDIR /var/www/html

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build

# ✅ Verify the build produced output - fail early if not
RUN test -f public/build/manifest.json && echo "✓ Vite manifest exists" || \
    (echo "✗ ERROR: public/build/manifest.json not found!" && exit 1)

RUN ls -la public/build/ && ls -la public/build/assets/ | head -20


# =========================================================
# =========================================================
# =========================================================
# Stage 3: Production (Final image with FrankenPHP)
# =========================================================
FROM dunglas/frankenphp:php8.4 AS production

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxml2-dev \
    libonig-dev \
    && install-php-extensions \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        opcache \
        intl \
        zip \
        bcmath \
        gd \
        xml \
        redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# Copy application from builder + Vite assets from frontend
COPY --from=php-builder /var/www/html .

COPY --from=frontend /var/www/html/public/build ./public/build

# Create writable directories only — no artisan calls at build time
# Note: Do NOT chown public/storage here — it doesn't exist until storage:link runs at runtime
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    storage/app/public/memo-attachments \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/build

EXPOSE 8000

CMD ["sh", "-c", "\
    php artisan storage:link && \
    chown -R www-data:www-data storage public/storage && \
    php artisan package:discover --ansi && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan migrate --force && \
    exec frankenphp php-server --listen :${PORT:-8000} --root public/"]