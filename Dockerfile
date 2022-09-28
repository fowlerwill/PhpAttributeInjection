FROM php:8.1-cli
RUN pecl install xdebug-3.1.5 \
    && docker-php-ext-enable xdebug
COPY . /usr/app
WORKDIR /usr/app
ENV XDEBUG_MODE=coverage
CMD [ "php", "./vendor/bin/phpunit", "test", "--coverage-text" ]