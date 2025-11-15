FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Setar diretório de trabalho
WORKDIR /var/www

# Instalar dependências do Laravel
RUN chown -R www-data:www-data /var/www

CMD ["php-fpm"]

