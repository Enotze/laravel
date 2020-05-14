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

COPY /deployment/gitlab/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

#composer
RUN curl -sS https://getcomposer.org/installer | php -- \
    --filename=composer \
    --install-dir=/usr/local/bin

RUN composer global require hirak/prestissimo --no-plugins --no-scripts

ENV PATH="${PATH}:/var/www:/var/www/vendor/bin"

WORKDIR /var/www

COPY composer.*  ./

RUN composer install \
      --no-interaction \
      --no-plugins \
      --no-suggest \
      --no-scripts \
      --no-autoloader \
      --prefer-dist \
      && rm -rf /root/.composer

COPY ./ .

RUN composer dump-autoload -o

RUN apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*
