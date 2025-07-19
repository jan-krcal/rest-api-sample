FROM php:8.2-cli
RUN docker-php-ext-install pdo pdo_mysql mysqli
WORKDIR /var/www/html
COPY . .
CMD ["php", "-S", "0.0.0.0:8000"]