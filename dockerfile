FROM php:8.2-cli

WORKDIR /var/www

COPY . .

RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN composer install --no-dev

EXPOSE 10000

CMD php artisan serve --host=0.0.0.0 --port=10000
