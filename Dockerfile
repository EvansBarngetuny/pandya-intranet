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
# Stage 3: Production (Final image)
# =========================================================
FROM php:8.4-cli

ENV PKG_CONFIG_PATH=/usr/lib/x86_64-linux-gnu/pkgconfig:/usr/lib/pkgconfig:/usr/share/pkgconfig

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxml2-dev \
    libonig-dev \
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

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Copy application with all dependencies from builder
COPY --from=php-builder /var/www/html .

# Copy compiled frontend assets from the frontend stage
COPY --from=frontend /var/www/html/public/build ./public/build

# Create Laravel's writable directories
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set correct ownership
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache \
    public/build

USER www-data

EXPOSE 8000

# Startup sequence:
# 1. Discover packages (needs real env)
# 2. Cache config/routes/views (uses real env)
# 3. Run migrations + seeders (database is now reachable)
# 4. Start PHP server
CMD ["sh", "-c", "\
    echo '=== [1/4] Discovering packages ===' && \
    php artisan package:discover --ansi && \
    echo '=== [2/4] Caching config, routes, views ===' && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    echo '=== [3/4] Running migrations + seeders ===' && \
    php artisan migrate --force && \
    php artisan db:seed --force && \
    echo '=== [4/4] Starting server on port ${PORT:-8000} ===' && \
    exec php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]