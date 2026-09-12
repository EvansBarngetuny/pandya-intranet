# =========================================================
# Stage 1: PHP dependencies (Builder)
# =========================================================
FROM php:8.3-cli AS php-builder

# Fix: Tell pkg-config where to find .pc files in Debian Trixie
ENV PKG_CONFIG_PATH=/usr/lib/x86_64-linux-gnu/pkgconfig:/usr/lib/pkgconfig:/usr/share/pkgconfig

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
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

# Copy the entire application first so artisan scripts work
COPY . .

# Create a temporary .env so Composer's post-autoload scripts can run
RUN cp .env.example .env || true

# Remove dev-only config files that reference classes not present with --no-dev
# Scribe is a dev-only package; its config references classes that break package:discover
RUN rm -f config/scribe.php || true

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist


# =========================================================
# Stage 2: Frontend (Vite build)
# =========================================================
FROM node:22-alpine AS frontend

WORKDIR /var/www/html

COPY package*.json ./

RUN npm ci

COPY . .

RUN npm run build


# =========================================================
# Stage 3: Production (Final image)
# =========================================================
FROM php:8.3-cli

# Fix: Tell pkg-config where to find .pc files in Debian Trixie
ENV PKG_CONFIG_PATH=/usr/lib/x86_64-linux-gnu/pkgconfig:/usr/lib/pkgconfig:/usr/share/pkgconfig

# Fix: Use -dev packages so pkg-config can find libjpeg and freetype2
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

# Use production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Copy the application with all installed dependencies from builder
COPY --from=php-builder /var/www/html .

# Copy the compiled frontend assets from the frontend stage
COPY --from=frontend /var/www/html/public/build ./public/build

# Ensure Laravel's writable directories exist
RUN mkdir -p storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

# Set correct ownership for Laravel's writable directories
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

USER www-data

EXPOSE 8000

# Use JSON array form to prevent signal handling issues
CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]