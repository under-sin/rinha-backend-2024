FROM dunglas/frankenphp:php8.4

WORKDIR /app

RUN install-php-extensions \
    pdo_pgsql \
    intl \
	zip \
    opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock symfony.lock ./

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --no-scripts \
    --prefer-dist

COPY . .

ENV APP_ENV=prod
ENV APP_DEBUG=0

RUN php bin/console cache:clear --env=prod

# Caddyfile is used by FrankenPHP to configure the built-in Caddy web server.
COPY Caddyfile /etc/frankenphp/Caddyfile

EXPOSE 8080

#CMD ["sh", "-c", "exec php -S 0.0.0.0:${HTTP_PORT:-8080} -t public"]