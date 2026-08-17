FROM php:8.4-cli

WORKDIR /app

RUN apt-get update \
    && apt-get install -y libpq-dev unzip \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --prefer-dist

COPY . .

RUN php bin/console cache:clear --env=prod

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]