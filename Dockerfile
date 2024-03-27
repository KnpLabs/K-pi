FROM composer:2.7.2 AS composer

##################################

FROM composer AS vendor

WORKDIR /K-pi

COPY composer.* ./

RUN composer install --ignore-platform-reqs --no-scripts --no-plugins --no-dev

##################################

FROM vendor AS vendor-dev

RUN composer install --ignore-platform-reqs --no-scripts --no-plugins

##################################

FROM php:8.3.4-cli-alpine3.19 AS base

WORKDIR /K-pi

ENTRYPOINT ["php", "/K-pi/bin/console"]

##################################

FROM base AS test

RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
 && pecl install \
        xdebug-3.3.1 \
 && docker-php-ext-enable \
        xdebug

COPY --from=vendor-dev /K-pi/vendor /K-pi/vendor

ENV XDEBUG_MODE=coverage

COPY . .

##################################

FROM base AS prod

COPY --from=vendor /K-pi/vendor /K-pi/vendor

COPY . .
