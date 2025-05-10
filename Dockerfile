ARG PHP_VERSION=8.3.13

FROM php:${PHP_VERSION}-fpm-alpine AS php_base

ARG APCU_VERSION=5.1.22
ENV APP_DEBUG=0

USER root

RUN apk add --no-cache \
		autoconf \
        fcgi \
		git \
		g++ \
        icu-dev \
		make \
        libpq-dev \
        icu-dev \
        icu-data-full \
        tzdata \
        nginx \
        freetype-dev \
        libjpeg-turbo-dev \
        libpng-dev;

RUN apk update && apk upgrade

RUN set -eux; \
	pecl install \
		apcu-${APCU_VERSION} \
        redis \
        igbinary \
	; \
	pecl clear-cache; \
    docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd ; \
    docker-php-ext-configure intl \
    ; \
    docker-php-ext-install \
        bcmath \
        intl \
        pdo \
        pdo_pgsql \
        pgsql \
    ;

RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');" \
    && mv composer.phar /usr/local/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1
ENV TZ=Europe/Rome

FROM php_base AS php_dev

ENV APP_ENV=dev APP_DEBUG=1 GOOGLE_APPLICATION_CREDENTIALS="/tmp/application_default_credentials.json"
ARG INFECTION_VERSION=0.26.16

USER root

WORKDIR /app

RUN apk add --no-cache \
		bash \
		zsh \
		make \
        libpq-dev \
        libc-dev \
        linux-headers \
	;

RUN	pecl install \
		pcov \
        xdebug \
	;

RUN docker-php-ext-enable xdebug;

RUN git config --global --add safe.directory /app

RUN sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" \
    && sed -i -E "s/^plugins=\((.*)\)$/plugins=(\1 git git-flow)/" /root/.zshrc
COPY docker/php/shell-aliases.rc /tmp/shell-aliases.rc
RUN cat /tmp/shell-aliases.rc >> /root/.bashrc \
    && cat /tmp/shell-aliases.rc >> ~/.zshrc

RUN apk add --no-cache bash \
    && curl -1sLfvk 'https://dl.cloudsmith.io/public/symfony/stable/setup.alpine.sh' | distro=alpine version=3.17.1 bash \
    && apk add symfony-cli

COPY docker/build build/
RUN cp build/*.ini $PHP_INI_DIR/conf.d/ \
    && rm -rf "$PHP_INI_DIR/conf.d/php_custom.ini" \
    && rm -rf "$PHP_INI_DIR/conf.d/php_opcache.ini" \
    && echo "zend.assertions = 1" >> $PHP_INI_DIR/conf.d/php_opcache.ini \
    && echo "assert.exception = 1" >> $PHP_INI_DIR/conf.d/php_opcache.ini \
    && echo "extension=pcov.so" >> $PHP_INI_DIR/conf.d/pcov.ini ;

COPY  composer.json composer.lock symfony.lock ./
COPY bin bin/
RUN echo "APP_ENV=dev" > .env
RUN composer install --no-scripts --no-progress --no-ansi

ENV XDEBUG_CONF_FILE=$PHP_INI_DIR/conf.d/docker-php-ext-xdebug.ini
COPY --chown=php:www docker/php/xdebug.ini $XDEBUG_CONF_FILE
COPY docker/php/xdebug-starter.sh /usr/local/bin/xdebug-starter

COPY . .

EXPOSE 8000

FROM php_base AS php_prod

ENV APP_DEBUG=0
ENV APP_ENV=prod


WORKDIR /app

RUN adduser -u 1000 -S php -G www-data \
&& (id -u nginx &>/dev/null || adduser -u 1001 -S nginx) \
&& addgroup nginx www-data

COPY docker/build build/
COPY docker/nginx nginx/
COPY bin bin/
RUN cp build/*.ini $PHP_INI_DIR/conf.d/ \
  && mv $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini \
  && rm -rf /usr/localetc/php-fpm.d \
  && rm  -f /usr/local/etc/php-fpm.conf \
  && cp build/www.conf /usr/local/etc/php-fpm.conf \
  && mkdir -p var/cache var/log \
  && chmod +x bin/console \
  && chown -R php:www-data /app \
  && cp  nginx/nginx.conf /etc/nginx/nginx.conf \
  && mkdir -p /var/lib/nginx/logs \
  && touch /var/lib/nginx/logs/error.log \
  && chown -R php:www-data /var/lib/nginx \
  && chown -R php:www-data /etc/nginx \
  && ln -sf /dev/stdout /var/log/nginx/access.log \
  && ln -sf /dev/stderr /var/log/nginx/error.log

COPY --chown=php:www-data composer.json composer.lock symfony.lock ./
COPY --chown=php:www-data config/preload.php /app/config/preload.php
RUN set -eux && composer install --prefer-dist --no-dev --no-autoloader --no-scripts --no-progress --no-ansi


COPY --chown=php:www-data templates templates/
COPY --chown=php:www-data migrations migrations/
COPY --chown=php:www-data public public/
COPY --chown=php:www-data config config/
COPY --chown=php:www-data src src/

RUN composer clear-cache --no-ansi \
    && composer dump-autoload --optimize --classmap-authoritative --no-dev

RUN touch .env

USER php

EXPOSE 8080

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
