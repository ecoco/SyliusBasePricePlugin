ARG NODE_VERSION=10
ARG PHP_VERSION=7.3

FROM node:${NODE_VERSION}-alpine AS nodejs
WORKDIR /app
RUN apk add --no-cache --virtual .build-deps g++ gcc git make python
COPY tests/Application/ ./
RUN yarn install; yarn run gulp ; yarn cache clean


FROM php:${PHP_VERSION}-cli-alpine3.10

ENV APP_DIR=/var/www/html
ENV PATH="${PATH}:${APP_DIR}/vendor/bin:${APP_DIR}/tests/Application/bin"
ENV APP_ENV=test

RUN apk add --update --no-cache zip git bash openssh

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && sync
RUN install-php-extensions bcmath intl gd exif fileinfo pdo_mysql xdebug

RUN cp $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini
RUN sed -i 's/memory_limit = 128M/memory_limit = -1/' $PHP_INI_DIR/php.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
RUN wget https://get.symfony.com/cli/installer -O - | bash

WORKDIR ${APP_DIR}

#COPY vendor vendor
#COPY composer.lock ./

COPY composer.json ./
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-scripts --no-progress --no-autoloader

COPY behat.yml.dist   ./
COPY phpspec.yml.dist ./
COPY phpunit.xml.dist ./
COPY psalm.xml        ./
COPY phpstan.neon     ./
COPY bin              bin
COPY build            build
COPY etc              etc
COPY features         features
COPY src              src
COPY tests            tests
COPY --from=nodejs /app/node_modules tests/Application/node_modules

RUN COMPOSER_MEMORY_LIMIT=-1 composer install --optimize-autoloader
RUN ./tests/Application/bin/console assets:install public

RUN ./tests/Application/bin/console cache:clear

RUN mv /root/.symfony/bin/symfony /usr/local/bin/symfony

EXPOSE 8080
RUN symfony server:ca:install

CMD ["symfony", "server:start", "--port=8080", "--allow-http", "--dir=tests/Application/public"]
