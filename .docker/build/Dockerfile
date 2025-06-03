FROM debian:12.10

# Argumentos
ARG GIT_VERSION=main
ARG NODE_VERSION=22
ARG APP_URL=http://localhost
ARG FRONTEND_URL=http://localhost
ARG APP_ENV=production
ARG APP_DEBUG=false

# Variáveis de ambiente
ENV DEBIAN_FRONTEND=noninteractive \
    APP_URL=${APP_URL} \
    FRONTEND_URL=${FRONTEND_URL} \
    APP_ENV=${APP_ENV} \
    APP_DEBUG=${APP_DEBUG} \
    GIT_VERSION=${GIT_VERSION}

# 1. Instalação de dependências de sistema
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    unzip \
    curl \
    libzip-dev \
    zip \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    libmagickwand-dev \
    libpq-dev \
    openssh-client \
    nano \
    iputils-ping \
    net-tools \
    telnet \
    cron \
    netcat-openbsd \
    ca-certificates \
    software-properties-common \
    supervisor \
    && apt-get clean


# 2. PHP 8.4 (oficial da Sury)
RUN curl -sSL https://packages.sury.org/php/README.txt | bash -x && \
    apt-get update && \
    apt-get install -y php8.4 php8.4-fpm php8.4-cli php8.4-mysql php8.4-xml php8.4-mbstring \
    php8.4-curl php8.4-zip php8.4-gd php8.4-imagick php8.4-bcmath php8.4-intl \
    php8.4-pgsql php8.4-redis && \
    apt-get clean


# 3. Instalação do Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# 4. Configura o SSH para git clone via SSH
RUN mkdir -p /root/.ssh && ssh-keyscan github.com >> /root/.ssh/known_hosts

# 5. Configuração do Nginx e PHP-FPM
COPY nginx.conf /etc/nginx/nginx.conf

# 6. Diretório da aplicação
WORKDIR /var/www/html

RUN rm -rf /var/www/html/*

# 7. Clona o repositório privado (pode adaptar para HTTPS se necessário)
# (Requer --mount=type=ssh em buildkit)
RUN --mount=type=ssh git clone --branch ${GIT_VERSION} git@github.com:lvluanvinicius/driver-tools.git .

# 8. Copia .env.example para .env
RUN cp .env.example .env

# 9. Ajusta variáveis de ambiente no .env
RUN sed -i "s|^APP_URL=.*|APP_URL=${APP_URL}|" .env \
 && sed -i "s|^APP_ENV=.*|APP_ENV=${APP_ENV}|" .env \
 && sed -i "s|^APP_DEBUG=.*|APP_DEBUG=${APP_DEBUG}|" .env

# 10. Instala dependências do Laravel
RUN composer install --optimize-autoloader --no-dev

# 11. Permissões e cache
RUN chown -R www-data:www-data storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# 12. Gera key e link storage
RUN php artisan key:generate && php artisan storage:link

# 13. Entrypoint customizado
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

# Copia o arquivo de configuração do Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

ENTRYPOINT ["/entrypoint.sh"]
# CMD ["nginx", "-g", "daemon off;"]

# Entry point via Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
