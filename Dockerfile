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

# Install and enable extensions
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

# Use the default development configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

# Set permissions for development
RUN chown -R www-data:www-data /var/www/html

USER www-data

# ==========================================
# STAGE 3: Production Environment
# ==========================================
FROM base AS production

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Copy project files
COPY . .

# Move assets to public if they are in root (for modern routing)
# Note: In development, we use volumes, so we'll symlink or just access them.
# For production, we copy them to the public folder.
RUN if [ -d "assets" ]; then \
        mkdir -p public/assets && \
        cp -r assets/* public/assets/; \
    fi

# Install production dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

USER www-data

EXPOSE 9000

CMD ["php-fpm"]
