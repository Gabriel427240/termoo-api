FROM php:8.4-cli

RUN apt-get update && apt-get install -y \
    curl zip unzip git \
    sqlite3 \
    libsqlite3-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install curl pdo pdo_sqlite

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

RUN cp .env.example .env \
    && touch database/database.sqlite \
    && chmod -R 777 database \
    && chmod -R 777 storage \
    && chmod -R 777 bootstrap/cache \
    && php artisan key:generate \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan migrate --force

EXPOSE 8080

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]