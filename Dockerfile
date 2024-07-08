FROM php:8.1-fpm

WORKDIR /var/www/html

# Установите любые необходимые зависимости и расширения PHP
RUN docker-php-ext-install pdo pdo_mysql

# Копирование всех файлов в контейнер
COPY ./src /var/www/html

EXPOSE 9000
