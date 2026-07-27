FROM node:23-alpine AS frontend_assets
WORKDIR /app

COPY . .
RUN npm ci
RUN npm run build

FROM php:8.3-fpm-alpine AS app
WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    fcgi \
    git \
    libzip-dev \
    nginx \
    supervisor \
    unzip \
    && docker-php-ext-install -j"$(nproc)" \
    opcache \
    pcntl \
    pdo_mysql \
    zip \
    && rm -rf /var/cache/apk/*

COPY --from=composer:2.9.8 /usr/bin/composer /usr/bin/composer

COPY . .
COPY --from=frontend_assets /app/public/build ./public/build

RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --prefer-dist \
    --optimize-autoloader

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/supervisord.conf /etc/supervisor.d/supervisord.ini
COPY docker/start.sh /usr/local/bin/start-container

RUN chmod +x /usr/local/bin/start-container \
    && mkdir -p storage/app/public storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache /run/nginx \
    && chown -R www-data:www-data /var/www/html /run/nginx \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 8080

CMD ["start-container"]
