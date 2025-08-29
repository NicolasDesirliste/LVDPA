# Annexe - Plan de déploiement Docker pour LVDPA

## 1. Introduction à Docker pour LVDPA

### 1.1 Objectifs du déploiement Docker

- **Portabilité** : Garantir que LVDPA fonctionne identiquement en développement et production
- **Isolation** : Éviter les conflits de versions entre projets
- **Scalabilité** : Faciliter la montée en charge future
- **Reproductibilité** : Permettre à tout développeur de lancer l'environnement en une commande

### 1.2 Architecture Docker proposée

```
┌─────────────────────────────────────────────┐
│           Docker Host (Serveur)             │
├─────────────────────────────────────────────┤
│                                             │
│  ┌─────────────────────────────────────┐   │
│  │     Conteneur: lvdpa-app            │   │
│  │  ┌───────────────────────────────┐  │   │
│  │  │  Apache 2.4 + PHP 8.2         │  │   │
│  │  │  Application LVDPA            │  │   │
│  │  │  Volume: ./app:/var/www/html │  │   │
│  │  └───────────────────────────────┘  │   │
│  │  Ports: 8080:80, 8443:443          │   │
│  └─────────────────────────────────────┘   │
│                    │                        │
│                    │ Réseau Docker          │
│                    │ lvdpa-network          │
│                    │                        │
│  ┌─────────────────────────────────────┐   │
│  │     Conteneur: lvdpa-db             │   │
│  │  ┌───────────────────────────────┐  │   │
│  │  │  MySQL 5.7                    │  │   │
│  │  │  Base de données LVDPA        │  │   │
│  │  │  Volume: ./data:/var/lib/mysql│  │   │
│  │  └───────────────────────────────┘  │   │
│  │  Port interne: 3306                 │   │
│  └─────────────────────────────────────┘   │
│                                             │
└─────────────────────────────────────────────┘
```

## 2. Fichiers de configuration Docker

### 2.1 Dockerfile (Construction de l'image)

```dockerfile
# Dockerfile
FROM php:8.2-apache

# Maintainer
LABEL maintainer="Nicolas DESIRLISTE <contact@lvdpa.fr>"
LABEL description="Image Docker pour LVDPA - La Voix Des Pères Abandonnés"

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP nécessaires
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_mysql \
        mysqli \
        zip \
        opcache

# Configuration PHP pour production
COPY docker/php/php.ini /usr/local/etc/php/php.ini

# Activation des modules Apache nécessaires
RUN a2enmod rewrite headers expires

# Configuration Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/security.conf /etc/apache2/conf-available/security.conf
RUN a2enconf security

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier les fichiers de l'application
COPY ./app /var/www/html

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Permissions appropriées
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/public/uploads \
    && chmod -R 775 /var/www/html/logs

# Script de démarrage personnalisé
COPY docker/scripts/entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/entrypoint.sh

# Exposition des ports
EXPOSE 80 443

# Point d'entrée
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
```

### 2.2 docker-compose.yml (Orchestration)

```yaml
# docker-compose.yml
version: '3.8'

services:
  # Service Application Web
  lvdpa-app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: lvdpa-app
    restart: unless-stopped
    ports:
      - "8080:80"
      - "8443:443"
    volumes:
      - ./app:/var/www/html
      - ./logs/apache:/var/log/apache2
      - ./docker/ssl:/etc/apache2/ssl
    environment:
      - APP_ENV=production
      - APP_DEBUG=false
      - DB_HOST=lvdpa-db
      - DB_NAME=lvdpa
      - DB_USER=lvdpa_user
      - DB_PASS=${DB_PASSWORD}
    depends_on:
      - lvdpa-db
    networks:
      - lvdpa-network

  # Service Base de données
  lvdpa-db:
    image: mysql:5.7
    container_name: lvdpa-db
    restart: unless-stopped
    ports:
      - "3307:3306"  # Port différent pour éviter conflit avec MySQL local
    volumes:
      - db_data:/var/lib/mysql
      - ./docker/mysql/init.sql:/docker-entrypoint-initdb.d/init.sql
    environment:
      - MYSQL_ROOT_PASSWORD=${MYSQL_ROOT_PASSWORD}
      - MYSQL_DATABASE=lvdpa
      - MYSQL_USER=lvdpa_user
      - MYSQL_PASSWORD=${DB_PASSWORD}
    networks:
      - lvdpa-network

  # Service phpMyAdmin (optionnel, pour administration)
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    container_name: lvdpa-phpmyadmin
    restart: unless-stopped
    ports:
      - "8081:80"
    environment:
      - PMA_HOST=lvdpa-db
      - PMA_PORT=3306
      - UPLOAD_LIMIT=50M
    depends_on:
      - lvdpa-db
    networks:
      - lvdpa-network

networks:
  lvdpa-network:
    driver: bridge

volumes:
  db_data:
    driver: local
```

### 2.3 Fichier .env (Variables d'environnement)

```bash
# .env
# NE JAMAIS COMMITER CE FICHIER !

# Base de données
MYSQL_ROOT_PASSWORD=root_password_super_secure_2025
DB_PASSWORD=lvdpa_password_very_secure_2025

# Application
APP_ENV=production
APP_URL=https://lvdpa.fr

# Email
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=contact@lvdpa.fr
MAIL_PASSWORD=email_password_secure

# Sécurité
JWT_SECRET=your_jwt_secret_key_here_very_long_and_secure
```

### 2.4 Configuration PHP (php.ini)

```ini
; docker/php/php.ini
[PHP]
; Sécurité
expose_php = Off
display_errors = Off
display_startup_errors = Off
log_errors = On
error_log = /var/log/apache2/php_errors.log

; Performance
memory_limit = 128M
max_execution_time = 30
max_input_time = 60
post_max_size = 20M
upload_max_filesize = 20M
max_file_uploads = 20

; Sessions
session.cookie_httponly = 1
session.cookie_secure = 1
session.cookie_samesite = Strict
session.use_strict_mode = 1
session.use_only_cookies = 1

; Timezone
date.timezone = Europe/Paris

; OPcache pour performance
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 60
```

### 2.5 Configuration Apache

```apache
# docker/apache/000-default.conf
<VirtualHost *:80>
    ServerName lvdpa.local
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    # Logs
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    # Sécurité Headers
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "DENY"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    
    # Redirection HTTPS en production
    <IfModule mod_rewrite.c>
        RewriteEngine On
        RewriteCond %{HTTP_HOST} ^lvdpa\.fr$ [NC]
        RewriteCond %{HTTPS} off
        RewriteRule ^(.*)$ https://%{HTTP_HOST}/$1 [R=301,L]
    </IfModule>
</VirtualHost>

<VirtualHost *:443>
    ServerName lvdpa.local
    DocumentRoot /var/www/html/public

    SSLEngine on
    SSLCertificateFile /etc/apache2/ssl/cert.pem
    SSLCertificateKeyFile /etc/apache2/ssl/key.pem

    <Directory /var/www/html/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 2.6 Script d'entrée

```bash
#!/bin/bash
# docker/scripts/entrypoint.sh

echo "Starting LVDPA container..."

# Attendre que MySQL soit prêt
echo "Waiting for MySQL..."
while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" --silent; do
    sleep 1
done
echo "MySQL is ready!"

# Vérifier si l'application est installée
if [ ! -f "/var/www/html/.installed" ]; then
    echo "First run detected, initializing application..."
    
    # Créer les répertoires nécessaires
    mkdir -p /var/www/html/logs
    mkdir -p /var/www/html/public/uploads
    
    # Permissions
    chown -R www-data:www-data /var/www/html
    
    # Marquer comme installé
    touch /var/www/html/.installed
fi

# Démarrer Apache
echo "Starting Apache..."
exec apache2-foreground
```

## 3. Structure des fichiers du projet

```
/LVDPA
├── app/                      # Code source de l'application
│   ├── config/
│   ├── controllers/
│   ├── models/
│   ├── views/
│   └── public/
├── docker/                   # Fichiers de configuration Docker
│   ├── apache/
│   │   ├── 000-default.conf
│   │   └── security.conf
│   ├── mysql/
│   │   └── init.sql         # Script d'initialisation BDD
│   ├── php/
│   │   └── php.ini
│   ├── scripts/
│   │   └── entrypoint.sh
│   └── ssl/                 # Certificats SSL (dev)
│       ├── cert.pem
│       └── key.pem
├── logs/                    # Logs de l'application
├── .env                     # Variables d'environnement
├── .env.example            # Exemple de configuration
├── .dockerignore           # Fichiers à ignorer
├── docker-compose.yml      # Orchestration
├── Dockerfile              # Construction image
└── README.md               # Documentation
```

## 4. Commandes de déploiement

### 4.1 Développement local

```bash
# Cloner le projet
git clone https://github.com/nicolas/lvdpa.git
cd lvdpa

# Copier et configurer l'environnement
cp .env.example .env
# Éditer .env avec vos valeurs

# Construire et lancer les conteneurs
docker-compose up -d --build

# Vérifier que tout fonctionne
docker-compose ps

# Voir les logs
docker-compose logs -f

# Accéder à l'application
# http://localhost:8080
```

### 4.2 Commandes utiles

```bash
# Arrêter les conteneurs
docker-compose down

# Arrêter et supprimer les volumes (ATTENTION: supprime la BDD)
docker-compose down -v

# Reconstruire après modification du Dockerfile
docker-compose build --no-cache

# Entrer dans le conteneur PHP
docker exec -it lvdpa-app bash

# Backup de la base de données
docker exec lvdpa-db mysqldump -u root -p lvdpa > backup.sql

# Restaurer la base de données
docker exec -i lvdpa-db mysql -u root -p lvdpa < backup.sql

# Voir les logs Apache
docker exec lvdpa-app tail -f /var/log/apache2/error.log
```

### 4.3 Déploiement en production

```bash
# Sur le serveur de production
# 1. Installer Docker et Docker Compose
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh
sudo apt-get install docker-compose

# 2. Cloner le projet
git clone https://github.com/nicolas/lvdpa.git
cd lvdpa

# 3. Configuration production
cp .env.production .env
# Éditer avec les vraies valeurs sécurisées

# 4. Certificats SSL (Let's Encrypt)
# À configurer avec Certbot ou Traefik

# 5. Lancer en production
docker-compose -f docker-compose.prod.yml up -d

# 6. Configurer les sauvegardes automatiques
crontab -e
# Ajouter: 0 2 * * * cd /path/to/lvdpa && ./backup.sh
```

## 5. Sécurité Docker

### 5.1 Bonnes pratiques appliquées

1. **Images officielles** : Utilisation d'images Docker Hub officielles
2. **Utilisateur non-root** : Apache s'exécute en tant que www-data
3. **Secrets externalisés** : Mots de passe dans .env, jamais dans le code
4. **Réseau isolé** : Communication inter-conteneurs via réseau Docker
5. **Volumes persistants** : Données MySQL dans volumes nommés
6. **Restart policy** : Redémarrage automatique en cas de crash

### 5.2 Checklist sécurité

- [ ] Scanner les images avec `docker scan`
- [ ] Utiliser des tags spécifiques (pas `latest`)
- [ ] Limiter les ressources CPU/RAM
- [ ] Configurer un firewall (iptables/ufw)
- [ ] Mettre à jour régulièrement les images
- [ ] Auditer les logs régulièrement
- [ ] Sauvegardes automatiques quotidiennes

## 6. Monitoring et maintenance

### 6.1 Surveillance

```bash
# Utilisation des ressources
docker stats

# Santé des conteneurs
docker-compose ps

# Espace disque
docker system df
```

### 6.2 Maintenance

```bash
# Nettoyer les images inutilisées
docker system prune -a

# Mettre à jour les images
docker-compose pull
docker-compose up -d

# Rotation des logs
docker-compose logs --tail=100 > logs/archive_$(date +%Y%m%d).log
```

## 7. Avantages pour LVDPA

1. **Déploiement rapide** : Un nouveau développeur peut lancer LVDPA en 5 minutes
2. **Cohérence** : Même environnement partout (dev, test, prod)
3. **Isolation** : Pas de conflit avec d'autres projets
4. **Scalabilité** : Facile d'ajouter des instances si le trafic augmente
5. **Rollback facile** : Retour arrière simple en cas de problème

## 8. Évolutions futures

### Phase 1 (actuelle)
- Configuration Docker simple
- 2 conteneurs (app + db)
- Adapté pour < 1000 utilisateurs

### Phase 2 (croissance)
- Ajout de Redis pour le cache
- Nginx en reverse proxy
- Elasticsearch pour la recherche
- Surveillance avec Prometheus/Grafana

### Phase 3 (scale)
- Kubernetes pour l'orchestration
- Microservices (API séparée)
- CDN pour les assets
- Load balancing

## 9. Conclusion

Ce plan de déploiement Docker permet à LVDPA de :
- Démarrer rapidement avec une architecture simple
- Garantir la reproductibilité de l'environnement
- Faciliter la collaboration entre développeurs
- Préparer la montée en charge future

La conteneurisation avec Docker représente un investissement initial en temps d'apprentissage, mais offre des bénéfices considérables pour la maintenance et l'évolution de LVDPA.

---

*Document préparé pour le Titre Professionnel CDA - Session juin 2025*