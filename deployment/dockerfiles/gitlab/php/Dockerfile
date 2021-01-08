FROM php:7.4.2-fpm-buster as base

WORKDIR /var/www

RUN apt-get update \
    && apt-get install -y \
       rsync \
       libpq-dev \
       zip \
       unzip \
       --no-install-recommends \
       && docker-php-ext-install \
          pdo \
          pdo_pgsql \
          bcmath \
          opcache

COPY /deployment/dockerfiles/gitlab/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

ENV PATH="${PATH}:/var/www:/var/www/vendor/bin"

COPY composer.*  ./

RUN composer install \
      --no-interaction \
      --no-plugins \
      --no-suggest \
      --no-scripts \
      --no-autoloader \
      --prefer-dist \
      && rm -rf /root/.composer

COPY app        ./app
COPY bootstrap  ./bootstrap
COPY config     ./config
COPY database   ./database
COPY public     ./public
COPY resources  ./resources
COPY routes     ./routes
COPY storage    ./storage
COPY artisan    ./artisan

RUN composer dump-autoload -o

RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
