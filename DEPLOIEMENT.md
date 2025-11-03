# 🚀 Guide de Déploiement - Corrections Système Anti-Plagiat

## 📦 Prérequis

- PHP >= 8.1
- Composer
- MySQL/MariaDB
- Extensions PHP requises:
  - `php-mbstring`
  - `php-xml`
  - `php-zip`
  - `php-gd`
  - `php-mysql`

## 🔧 Installation des Corrections

### 1. Mise à jour des dépendances

```bash
cd /home/sm/Documents/School/Laravel/Anti-Plagiat/laravel

# Installer/mettre à jour les dépendances
composer install --no-dev --optimize-autoloader

# Vérifier que PhpSpreadsheet est installé (pour Excel)
composer show phpoffice/phpspreadsheet
```

### 2. Configuration de l'environnement

```bash
# Copier le fichier d'exemple si nécessaire
cp .env.example .env

# Générer la clé d'application si pas déjà fait
php artisan key:generate
```

### 3. Configuration du fichier `.env`

Ajouter/modifier les variables suivantes:

```env
# Application
APP_NAME="AntiPlag"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=antiplag_db
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

# Sessions (IMPORTANT - Nouvelles configurations)
SESSION_DRIVER=database
SESSION_LIFETIME=480
SESSION_ENCRYPT=true
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=true  # true en production avec HTTPS
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
SESSION_EXPIRE_ON_CLOSE=false

# API externe (pour module Docs externes)
EXTERNAL_API_URL=https://api-externe.com/documents
EXTERNAL_API_TOKEN=votre_token_api
EXTERNAL_API_CALLBACK_URL=https://api-externe.com/callback

# Locale
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en

# Mail (optionnel pour notifications)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@antiplag.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Migrations et cache

```bash
# Exécuter les migrations si nécessaire
php artisan migrate --force

# Vérifier que la table sessions existe
php artisan migrate:status

# Effacer tous les caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reconstruire les caches pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5. Permissions des fichiers

```bash
# Donner les bonnes permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Vérifier les permissions
ls -la storage/
```

### 6. Vérification des routes

```bash
# Lister toutes les routes pour vérifier
php artisan route:list

# Vérifier spécifiquement les nouvelles routes admin
php artisan route:list --path=admin | grep -E "(incoming|reports|users)"
```

## ✅ Vérifications Post-Déploiement

### 1. Test de syntaxe PHP

```bash
php -l app/Http/Controllers/AdminController.php
php -l app/Http/Controllers/DocumentController.php
php -l app/Http/Middleware/SecureSession.php
php -l app/Http/Middleware/SetLocale.php
```

### 2. Test des middlewares

```bash
php artisan tinker
>>> app()->make('App\Http\Middleware\SecureSession');
>>> app()->make('App\Http\Middleware\SetLocale');
>>> exit
```

### 3. Test de connexion base de données

```bash
php artisan tinker
>>> DB::connection()->getPdo();
>>> DB::table('users')->count();
>>> exit
```

### 4. Test des routes critiques

Accéder aux URLs suivantes (en tant qu'admin):

- ✅ `/admin/users` - Gestion utilisateurs
- ✅ `/admin/documents` - Liste documents
- ✅ `/admin/incoming` - Documents externes (NOUVEAU)
- ✅ `/admin/reports` - Rapports
- ✅ `/admin/errors` - Erreurs

### 5. Test de sécurité

Vérifier les en-têtes HTTP:

```bash
curl -I https://votre-domaine.com | grep -E "(X-Frame|X-Content|X-XSS|Referrer)"
```

Résultat attendu:
```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

## 🔐 Configuration Serveur Web

### Apache (.htaccess)

Le fichier `public/.htaccess` devrait déjà être configuré. Vérifier:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ index.php [L]
</IfModule>

# Sécurité additionnelle
<IfModule mod_headers.c>
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-Content-Type-Options "nosniff"
    Header set X-XSS-Protection "1; mode=block"
</IfModule>
```

### Nginx

Configuration recommandée:

```nginx
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/antiplag/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Referrer-Policy "strict-origin-when-cross-origin";

    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 📊 Monitoring

### Logs à surveiller

```bash
# Logs Laravel
tail -f storage/logs/laravel.log

# Logs serveur web
tail -f /var/log/nginx/error.log
tail -f /var/log/apache2/error.log

# Logs PHP
tail -f /var/log/php8.1-fpm.log
```

### Métriques importantes

- Temps de réponse des pages
- Taux d'erreurs 500
- Utilisation mémoire PHP
- Nombre de sessions actives

```bash
# Vérifier les sessions actives
php artisan tinker
>>> DB::table('sessions')->count();
```

## 🔄 Mise à jour Future

Pour appliquer de futures mises à jour:

```bash
# 1. Backup de la base de données
mysqldump -u user -p antiplag_db > backup_$(date +%Y%m%d).sql

# 2. Mettre le site en maintenance
php artisan down

# 3. Pull des changements
git pull origin main

# 4. Mise à jour des dépendances
composer install --no-dev --optimize-autoloader

# 5. Migrations
php artisan migrate --force

# 6. Effacer et reconstruire les caches
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 7. Remettre le site en ligne
php artisan up
```

## 🐛 Dépannage

### Problème: Erreur 500 après déploiement

```bash
# Vérifier les logs
tail -50 storage/logs/laravel.log

# Vérifier les permissions
ls -la storage/
ls -la bootstrap/cache/

# Recréer les caches
php artisan cache:clear
php artisan config:clear
```

### Problème: Routes non trouvées

```bash
php artisan route:clear
php artisan route:cache
php artisan route:list
```

### Problème: Sessions ne fonctionnent pas

```bash
# Vérifier la table sessions
php artisan migrate:status

# Créer la table si nécessaire
php artisan session:table
php artisan migrate

# Vérifier la configuration
php artisan config:show session
```

### Problème: Middlewares non actifs

```bash
# Effacer le cache de configuration
php artisan config:clear

# Vérifier bootstrap/app.php
cat bootstrap/app.php | grep -A 5 "withMiddleware"
```

## 📞 Support

En cas de problème:

1. Consulter `CORRECTIONS_AUDIT.md` pour les détails des corrections
2. Consulter `GUIDE_TEST.md` pour les procédures de test
3. Vérifier les logs: `storage/logs/laravel.log`
4. Vérifier la configuration: `php artisan config:show`

## ✅ Checklist de Déploiement

- [ ] Dépendances installées (`composer install`)
- [ ] Fichier `.env` configuré
- [ ] Clé d'application générée (`php artisan key:generate`)
- [ ] Migrations exécutées (`php artisan migrate`)
- [ ] Permissions correctes sur `storage/` et `bootstrap/cache/`
- [ ] Caches construits (`config:cache`, `route:cache`, `view:cache`)
- [ ] Routes vérifiées (`php artisan route:list`)
- [ ] Tests de syntaxe passés
- [ ] En-têtes de sécurité vérifiés
- [ ] Logs accessibles et surveillés
- [ ] Backup de la base de données effectué

---

**Date:** 3 Novembre 2024  
**Version:** 1.0  
**Auteur:** Groupe 4
