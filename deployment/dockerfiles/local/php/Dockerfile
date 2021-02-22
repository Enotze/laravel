FROM php:7.4-fpm

RUN apt-get update \
    && apt-get install -y \
       libpq-dev \
       zip \
       unzip \
       --no-install-recommends \
       && docker-php-ext-install \
          pdo \
          pdo_pgsql \
          bcmath \
          opcache

COPY opcache.ini /usr/local/etc/php/conf.d/opcache.ini

#composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

#xdebug
RUN pecl install xdebug-beta
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN XDEBUG_REMOTE_HOST=$(if ping -q -c1 host.docker.internal > /dev/null 2>&1; then echo "host.docker.internal"; else echo "172.17.0.1"; fi); \
    echo "xdebug.remote_host=${XDEBUG_REMOTE_HOST}" > /usr/local/etc/php/conf.d/xdebug_remote_host.ini

ENV PATH="${PATH}:/var/www:/var/www/vendor/bin"

WORKDIR /var/www

RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
