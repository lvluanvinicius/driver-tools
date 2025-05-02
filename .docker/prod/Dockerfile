FROM php:8.4-fpm

USER root

# Argumento para configurar a versão do Node.js
ARG NODE_VERSION=22
ARG APP_URL=http://localhost
ARG APP_ENV=production
ARG APP_DEBUG=false

ENV NODE_VERSION=${NODE_VERSION}
ENV APP_URL=${APP_URL}
ENV APP_ENV=${APP_ENV}
ENV APP_DEBUG=${APP_DEBUG}

RUN apt-get update && apt-get install -y \
    curl \
    gnupg \
    build-essential \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    net-tools \
    zip \
    nano \
    iputils-ping \
    netcat-openbsd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd zip pdo_mysql \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apt-get clean

# Instale o Node.js versão 22
RUN curl -fsSL https://deb.nodesource.com/setup_${NODE_VERSION}.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && npm cache clean --force

# Exibe as versões instaladas
RUN php -v && node -v && npm -v

# Instale o Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


# Definir o diretório de trabalho dentro do contêiner
WORKDIR /var/www/html

# Copiando projeto.
COPY . .

# Instalando dependências.
RUN npm install

# Copindo env de exemplo para produção.
RUN cp .env.example .env

# Altera o valor da URL de aplicativo.
RUN sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env
RUN sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV}|" .env
RUN sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env

# Criando build do node.
RUN npm run build

# Instalando dependências do laravel.
RUN composer install

# Alterando permissões do projeto.
RUN chown -R www-data:www-data ./ && chmod 775 -R ./ && chmod 777 -R storage
RUN php artisan key:gen && php artisan storage:link

# Expor a porta padrão do PHP-FPM
EXPOSE 9000

ENTRYPOINT [ ".docker/prod/entrypoint.sh" ]

# Comando padrão ao iniciar o contêiner
CMD ["php-fpm"]
