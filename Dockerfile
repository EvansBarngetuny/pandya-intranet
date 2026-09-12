# =========================================================
# Stage 1: PHP dependencies
# =========================================================
FROM php:8.3-cli AS builder

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    curl \
    unzip \
    git \
    libpq-dev \
    libonig-dev \
    libssl-dev \
    libxml2-dev \
    libcurl4-openssl-dev \
    libicu-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        opcache \
        intl \
        zip \
        bcmath \
        gd \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 \
    composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --prefer-dist


# =========================================================
# Stage 2: Frontend build
# =========================================================
FROM node:22-alpine AS frontend

WORKDIR /var/www/html

# Copy package files first for Docker cache
COPY package*.json ./

# Install Node dependencies
RUN npm ci

# Copy frontend source files
COPY . .

# Build Vite assets
RUN npm run build


# =========================================================
# Stage 3: Production
# =========================================================
FROM php:8.3-cli

# Runtime dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libfreetype6 \
    libjpeg62-turbo \
    libpng16-16 \
    procps \
    && docker-php-ext-install \
        pdo_mysql \
        pdo_pgsql \
        pgsql \
        opcache \
        intl \
        zip \
        bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# PHP production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Working directory
WORKDIR /var/www/html

# Copy Laravel application from builder
COPY --from=builder /var/www/html /var/www/html

# Copy compiled Vite assets
COPY --from=frontend /var/www/html/public/build /var/www/html/public/build

# Laravel permissions
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache

# Use non-root user
USER www-data

# Railway will provide the PORT environment variable
EXPOSE 8000

# Start Laravel
CMD php artisan migrate --force \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache \
    && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}