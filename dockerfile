# Usamos a imagem oficial do PHP com CLI para rodar o artisan serve
FROM php:8.4-cli

# Instalamos as dependências do sistema e extensões do PHP necessárias para o Laravel e MySQL
RUN apt-get update -y && apt-get install -y \
    libicu-dev \
    libmariadb-dev \
    unzip zip \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    && docker-php-ext-install gettext intl pdo_mysql gd

# Instalamos o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definimos o diretório de trabalho
WORKDIR /app

# Copiamos os arquivos do projeto
COPY . .

# Expomos a porta 8000 
EXPOSE 8000

# Comando padrão para manter o container rodando
CMD php artisan serve --host=0.0.0.0 --port=8000