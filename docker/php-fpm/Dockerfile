FROM php:8-fpm-alpine

RUN apk update \
  && apk add autoconf g++ make \
  && rm -rf /tmp/pear

RUN apk add postgresql-dev \
  && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
  && docker-php-ext-install pdo_pgsql \
  && apk del autoconf g++ make

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./common/php/conf.d /usr/local/etc/php/conf.d
COPY ./php-cli/docker-php-entrypoint.sh /usr/local/bin/docker-php-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-php-entrypoint.sh

WORKDIR /app