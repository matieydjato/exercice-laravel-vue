FROM php:8.3-cli

# Installer les dépendances système
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_sqlite

# Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY . .

# Installer les dépendances PHP
RUN composer install --no-interaction --optimize-autoloader

# Créer la base de données SQLite
RUN touch /var/www/html/database/database.sqlite

# Copier .env.example vers .env
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Générer la clé d'application
RUN php artisan key:generate

# Exécuter les migrations
RUN php artisan migrate --force

# Exposer le port
EXPOSE 8000

# Commande par défaut
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]