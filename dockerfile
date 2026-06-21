FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev \
    && docker-php-ext-install pdo pdo_mysql

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --optimize-autoloader --no-dev

RUN php artisan config:cache || true

EXPOSE 8000


CMD php artisan serve --host=0.0.0.0 --port=8000