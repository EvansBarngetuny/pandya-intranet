# =========================================================
# Stage 1: PHP dependencies + extensions
# =========================================================
FROM php:8.4-cli AS php-builder

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
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j2 \
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

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# Create environment file if required during build
RUN cp .env.example .env || true

# IMPORTANT:
# Temporarily remove Scribe config because your previous build
# failed with Knuckles\Scribe\Config\AuthIn.
RUN rm -f config/scribe.php || true

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist


# =========================================================
# Stage 2: Frontend
# =========================================================
FROM node:22-alpine AS frontend

WORKDIR /var/www/html

COPY package.json package-lock.json ./

RUN npm ci

COPY . .

RUN npm run build


# =========================================================
# Stage 3: Production
# =========================================================
FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq5 \
    libicu76 \
    libzip5 \
    libfreetype6 \
    libjpeg62-turbo \
    libpng16-16t64 \
    libxml2 \
    libonig5 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Copy compiled PHP extensions
COPY --from=php-builder \
    /usr/local/lib/php/extensions/ \
    /usr/local/lib/php/extensions/

# Copy PHP extension configuration
COPY --from=php-builder \
    /usr/local/etc/php/conf.d/ \
    /usr/local/etc/php/conf.d/

# Production PHP configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www/html

# Copy Laravel application + Composer dependencies
COPY --from=php-builder \
    /var/www/html \
    .

# Copy Vite production assets
COPY --from=frontend \
    /var/www/html/public/build \
    ./public/build

# Laravel writable directories
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

USER www-data

EXPOSE 8000

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"]