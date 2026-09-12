# =========================================
# Stage 1: Build Environment (Builder)
# =========================================
FROM php:8.4-fpm AS builder

# Install system dependencies required for Laravel and Composer
# Includes git and unzip to resolve your previous build failures
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
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    pdo_pgsql \
    pgsql \
    opcache \
    intl \
    zip \
    bcmath \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www

# Copy the entire application code
COPY . /var/www

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install application dependencies
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --no-progress --prefer-dist

# =========================================
# Stage 2: Production Environment (Runtime)
# =========================================
FROM php:8.3-fpm

# Install only the runtime libraries needed for the production environment
RUN apt-get update && apt-get install -y --no-install-recommends \
    libpq-dev \
    libicu-dev \
    libzip-dev \
    libfcgi-bin \
    procps \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy the compiled PHP extensions and config from the builder stage
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/
COPY --from=builder /usr/local/bin/docker-php-ext-* /usr/local/bin/

# Apply the recommended production PHP settings
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy the application files and vendor directory from the builder
COPY --from=builder /var/www /var/www

# Set working directory
WORKDIR /var/www

# Ensure correct permissions for Laravel's storage and cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Switch to the non-privileged user
USER www-data

# Expose port 9000 and start PHP-FPM
EXPOSE 9000
CMD ["php-fpm"]