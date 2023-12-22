FROM php:8.2-fpm-alpine  AS base

ENV NEWUSER='dev'

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache $PHPIZE_DEPS oniguruma-dev
RUN adduser -D -g "${NEWUSER}" $NEWUSER
RUN docker-php-ext-install pdo_mysql mbstring bcmath

FROM base as local

RUN apk add --no-cache openssl-dev linux-headers sudo

# Adding the the dev user to sudoers
RUN echo "$NEWUSER ALL=(ALL) NOPASSWD: ALL" > /etc/sudoers.d/$NEWUSER && chmod 0440 /etc/sudoers.d/$NEWUSER

# Enabling XDebug
COPY .docker/config/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN pecl install xdebug-3.2.2
RUN docker-php-ext-enable xdebug
