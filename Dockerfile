FROM php:8.4-apache

ENV DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libjpeg-dev libonig-dev libxml2-dev libzip-dev \
    libpq-dev libicu-dev libcurl4-openssl-dev pkg-config default-mysql-client \
 && docker-php-ext-configure gd --with-jpeg \
 && docker-php-ext-install -j$(nproc) pdo_mysql mbstring exif pcntl bcmath gd zip intl opcache \
 && pecl install redis && docker-php-ext-enable redis \
 && rm -rf /var/lib/apt/lists/*

RUN a2enmod rewrite headers

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction || true

COPY . .

RUN composer dump-autoload --optimize --no-dev

RUN chown -R www-data:www-data /var/www/html \
 && chmod -R 755 /var/www/html/storage /var/www/html/bootstrap/cache

RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' /etc/apache2/sites-available/000-default.conf \
 && printf '<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' >> /etc/apache2/apache2.conf

EXPOSE 80

CMD ["apache2-foreground"]
