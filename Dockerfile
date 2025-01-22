FROM php:8.2-fpm

# Instala extensiones y herramientas
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev zip \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev \
        zlib1g-dev libicu-dev g++ libxml2-dev libssl-dev libonig-dev \
        curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install mysqli pdo pdo_mysql zip gd intl soap mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/*

# Instala Node.js y Composer
RUN curl -fsSL https://deb.nodesource.com/setup_current.x | bash - \
    && apt-get install -y --no-install-recommends nodejs \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instala Xdebug
RUN pecl install xdebug-3.2.2 && docker-php-ext-enable xdebug
COPY ./build/php/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Instala Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Copia el código de la aplicación
COPY . /var/www/html

# Establece el directorio de trabajo
WORKDIR /var/www/html