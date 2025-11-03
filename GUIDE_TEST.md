# 🧪 Guide de Test - Corrections Système Anti-Plagiat

## 📋 Checklist de Vérification

### 1. Tests Administrateur

#### ✅ Test 1: Rétrogradation Admin
**Objectif:** Vérifier que la rétrogradation d'un admin fonctionne sans erreur

**Étapes:**
1. Se connecter en tant qu'administrateur
2. Aller sur `/admin/users`
3. Sélectionner un utilisateur avec rôle admin (pas soi-même)
4. Cliquer sur "Basculer rôle" pour le rétrograder en utilisateur
5. **Résultat attendu:** 
   - Message de succès "Rôle mis à jour : utilisateur"
   - L'utilisateur est déconnecté automatiquement
   - Ses sessions sont supprimées

**Commande de test:**
```bash
# Vérifier les sessions avant
php artisan tinker
>>> DB::table('sessions')->where('user_id', USER_ID)->count();

# Après rétrogradation, devrait être 0
```

---

#### ✅ Test 2: Module "Docs externes"
**Objectif:** Vérifier que le module est accessible et fonctionnel

**Étapes:**
1. Se connecter en tant qu'administrateur
2. Aller sur `/admin/incoming`
3. **Résultat attendu:** Page affichée sans erreur avec liste des documents externes
4. Tester le bouton "Récupérer depuis l'API externe"
5. **Note:** Nécessite configuration de `EXTERNAL_API_URL` dans `.env`

**Configuration requise dans `.env`:**
```env
EXTERNAL_API_URL=https://votre-api.com/documents
EXTERNAL_API_TOKEN=votre_token
EXTERNAL_API_CALLBACK_URL=https://votre-api.com/callback
```

**Test manuel sans API:**
```php
// Dans tinker
use App\Models\IncomingDocument;

IncomingDocument::create([
    'external_id' => 'TEST-001',
    'filename' => 'test_document.pdf',
    'content' => 'Contenu de test pour la détection de plagiat',
    'uploader_id' => 'test_user',
    'metadata' => json_encode(['source' => 'test']),
]);
```

---

#### ✅ Test 3: Module "Rapports"
**Objectif:** Vérifier que l'envoi de résultats fonctionne

**Étapes:**
1. Aller sur `/admin/reports`
2. Trouver un rapport avec un document
3. Remplir le champ "Détail des erreurs à envoyer"
4. Cliquer sur "Envoyer erreurs"
5. **Résultat attendu:** Message "Résultat envoyé avec succès"

**Vérification en base:**
```bash
php artisan tinker
>>> App\Models\Report::latest()->first()->admin_response;
```

---

### 2. Tests Visualisation Excel

#### ✅ Test 4: Upload et visualisation Excel
**Objectif:** Vérifier l'extraction et l'affichage du contenu Excel

**Étapes:**
1. Se connecter en tant qu'utilisateur
2. Aller sur `/documents/create`
3. Uploader un fichier Excel (.xlsx ou .xls)
4. Retourner sur `/documents`
5. Cliquer sur le document Excel uploadé
6. Cliquer sur "Voir Excel" ou aller sur `/documents/{id}/view-excel`
7. **Résultat attendu:** 
   - Contenu extrait affiché
   - Formatage par feuilles visible
   - Boutons de téléchargement et comparaison présents

**Test avec fichier Excel:**
```bash
# Créer un fichier Excel de test
# Contenu: Feuille1 avec quelques cellules remplies
```

---

### 3. Tests Sécurité Sessions

#### ✅ Test 5: Régénération de session
**Objectif:** Vérifier que les sessions sont régénérées périodiquement

**Étapes:**
1. Se connecter
2. Noter l'ID de session dans les cookies du navigateur
3. Attendre 31 minutes (ou modifier le middleware pour 1 minute en test)
4. Rafraîchir la page
5. **Résultat attendu:** Nouvel ID de session généré

**Vérification:**
```bash
# Dans les DevTools du navigateur
# Application > Cookies > Vérifier la valeur de laravel_session
```

---

#### ✅ Test 6: En-têtes de sécurité
**Objectif:** Vérifier que les en-têtes HTTP de sécurité sont présents

**Étapes:**
1. Ouvrir n'importe quelle page du site
2. Ouvrir DevTools > Network
3. Sélectionner une requête
4. Vérifier les Response Headers

**En-têtes attendus:**
```
X-Frame-Options: SAMEORIGIN
X-Content-Type-Options: nosniff
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

**Test avec curl:**
```bash
curl -I http://localhost:8000 | grep -E "(X-Frame|X-Content|X-XSS|Referrer)"
```

---

### 4. Tests UI/UX Responsive

#### ✅ Test 7: Menu mobile
**Objectif:** Vérifier que le menu mobile fonctionne correctement

**Étapes:**
1. Ouvrir le site sur un appareil mobile ou réduire la fenêtre (< 1024px)
2. Vérifier que le bouton hamburger (☰) est visible
3. Cliquer sur le bouton hamburger
4. **Résultat attendu:** 
   - Menu déroulant s'affiche
   - Tous les liens sont accessibles
   - Menu se ferme au clic extérieur

**Test responsive:**
```
Tailles à tester:
- Mobile: 375px
- Tablet: 768px
- Desktop: 1024px+
```

---

#### ✅ Test 8: Navigation fixe
**Objectif:** Vérifier que la navigation reste en haut lors du scroll

**Étapes:**
1. Ouvrir une page avec contenu long
2. Scroller vers le bas
3. **Résultat attendu:** La barre de navigation reste visible en haut

---

### 5. Tests Multilingue

#### ✅ Test 9: Changement de langue
**Objectif:** Vérifier que le système détecte et change la langue

**Étapes:**
1. Ouvrir le site
2. Cliquer sur le sélecteur de langue (icône globe)
3. Sélectionner une langue (English, Español, etc.)
4. **Résultat attendu:** 
   - URL contient `?lang=en` (ou autre code)
   - Langue mémorisée en session
   - Interface change de langue (si traductions disponibles)

**Test via URL:**
```
http://localhost:8000?lang=en
http://localhost:8000?lang=es
http://localhost:8000?lang=fr
```

**Vérification en session:**
```bash
php artisan tinker
>>> session()->get('locale');
```

---

#### ✅ Test 10: Détection automatique
**Objectif:** Vérifier la détection depuis le navigateur

**Étapes:**
1. Vider les cookies et la session
2. Changer la langue préférée du navigateur
3. Ouvrir le site
4. **Résultat attendu:** Langue détectée automatiquement

**Langues supportées:**
- 🇫🇷 Français (fr)
- 🇬🇧 English (en)
- 🇪🇸 Español (es)
- 🇩🇪 Deutsch (de)
- 🇸🇦 العربية (ar)
- 🇨🇳 中文 (zh)

---

## 🔧 Commandes Utiles

### Vérifier la syntaxe PHP
```bash
php -l app/Http/Controllers/AdminController.php
php -l app/Http/Middleware/SecureSession.php
php -l app/Http/Middleware/SetLocale.php
```

### Lister les routes
```bash
php artisan route:list
php artisan route:list --path=admin
php artisan route:list --name=incoming
```

### Effacer les caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Tester les middlewares
```bash
php artisan tinker
>>> app()->make('App\Http\Middleware\SecureSession');
>>> app()->make('App\Http\Middleware\SetLocale');
```

---

## 🐛 Dépannage

### Problème: Routes "incoming" non trouvées
**Solution:**
```bash
php artisan route:clear
php artisan route:cache
```

### Problème: Middleware non actif
**Solution:**
```bash
php artisan config:clear
# Vérifier bootstrap/app.php
```

### Problème: Sessions non régénérées
**Solution:**
```bash
# Vérifier .env
SESSION_DRIVER=database
SESSION_LIFETIME=480

# Vérifier la table sessions existe
php artisan migrate
```

### Problème: Excel non visualisable
**Solution:**
```bash
# Vérifier que PhpSpreadsheet est installé
composer show | grep phpspreadsheet

# Si absent:
composer require phpoffice/phpspreadsheet
```

---

## ✅ Checklist Finale

- [ ] Rétrogradation admin fonctionne sans erreur
- [ ] Module "Docs externes" accessible
- [ ] Module "Rapports" envoie les résultats
- [ ] Fichiers Excel visualisables
- [ ] Sessions sécurisées avec régénération
- [ ] En-têtes de sécurité présents
- [ ] Menu mobile fonctionnel
- [ ] Navigation fixe lors du scroll
- [ ] Changement de langue fonctionne
- [ ] Détection automatique de langue active

---

## 📊 Résultats Attendus

Tous les tests doivent passer sans erreur. Si un test échoue:

1. Vérifier les logs: `storage/logs/laravel.log`
2. Vérifier la configuration `.env`
3. Effacer les caches
4. Consulter le fichier `CORRECTIONS_AUDIT.md` pour les détails

---

**Date de création:** 3 Novembre 2024  
**Version:** 1.0
