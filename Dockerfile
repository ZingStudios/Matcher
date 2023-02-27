FROM composer:2.3.5 as composer

FROM php:8.1-cli

RUN apt-get update \
  && apt-get install -y libzip-dev git cron --no-install-recommends \
  && pecl install xdebug \
  && docker-php-ext-enable xdebug

RUN docker-php-ext-install zip

COPY --from=composer /usr/bin/composer /usr/bin/composer

# https://getcomposer.org/doc/03-cli.md#composer-allow-superuser
ENV COMPOSER_ALLOW_SUPERUSER=1
ENV PATH="${PATH}:/root/.composer/vendor/bin"

WORKDIR /var/www

WORKDIR /var/www