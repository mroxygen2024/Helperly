# ==========================================
# STAGE 1: Base Image with Dependencies
# ==========================================
FROM php:8.2-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    bash \
    curl \
    linux-headers \
    openssl-dev \
    $PHPIZE_DEPS

# Install PHP extensions
RUN docker-php-ext-install \
    bcmath \
    mbstring \
    intl \
    zip \
    opcache \
    gd

# Install MongoDB and Redis extensions
RUN pecl install mongodb redis && \
    docker-php-ext-enable mongodb redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# ==========================================
# STAGE 2: Development Environment
# ==========================================
FROM base AS development

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Copy composer files first (layer caching)
COPY composer.json composer.lock ./

# Install dependencies during build
RUN composer install --no-interaction

RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000
CMD ["php-fpm"]

# ==========================================
# STAGE 3: Production Environment
# ==========================================
FROM base AS production

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy project files
COPY . .

# Copy assets if they exist
RUN if [ -d "assets" ]; then \
        mkdir -p public/assets && \
        cp -r assets/* public/assets/; \
    fi

# Install production dependencies only
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000
CMD ["php-fpm"]