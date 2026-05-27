FROM php:8.4-cli

# Instala as dependências do sistema necessárias para o Laravel e SQLite
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev

# Limpa o cache do gerenciador de pacotes para reduzir o tamanho da imagem
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instala as extensões PHP obrigatórias para o Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd pdo_sqlite

# Instala a versão mais recente do Composer v2 universal
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho dentro do contêiner
WORKDIR /app

# Copia todos os arquivos do repositório para o contêiner
COPY . /app

# Instala as dependências do projeto otimizando o carregamento das classes
RUN composer install --no-dev --optimize-autoloader

# Garante que o arquivo do banco de dados SQLite exista e tenha permissões de escrita
RUN mkdir -p database && touch database/database.sqlite

# Executa as tabelas do banco de dados (migrations) de forma forçada em produção
RUN php artisan migrate --force

# Comando de inicialização oficial do servidor de desenvolvimento do Laravel
CMD php artisan serve --host=0.0.0.0 --port=$PORT
