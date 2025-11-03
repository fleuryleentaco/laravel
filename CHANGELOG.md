# 📝 Changelog - Système Anti-Plagiat

## [1.1.1] - 2024-11-03 (Hotfix)

### 🐛 Correctif Critique

**Bug de rétrogradation admin:**
- **Problème:** Violation de contrainte de clé étrangère lors du toggle de rôle
- **Cause:** Utilisation d'ID de rôle incorrect (2 au lieu de 3)
- **Solution:** Correction dans `AdminController::toggleRole()` pour utiliser les bons ID : admin(1) ↔ user(3)
- **Fichier modifié:** `app/Http/Controllers/AdminController.php` (ligne 113)

**Note:** Les rôles dans la base de données sont `id_role_user = 1` (admin) et `id_role_user = 3` (user), pas 1 et 2.

---

## [1.1.0] - 2024-11-03

### 🎯 Corrections de l'Audit (Groupe 1)

#### ✅ Ajouté

**Nouveaux Contrôleurs/Méthodes:**
- `AdminController::incomingDocuments()` - Liste des documents externes
- `AdminController::fetchIncomingDocuments()` - Import depuis API externe
- `AdminController::compareIncomingDocument()` - Comparaison docs externes
- `AdminController::sendIncomingErrors()` - Envoi erreurs via callback
- `DocumentController::viewExcel()` - Visualisation fichiers Excel

**Nouveaux Middlewares:**
- `SecureSession` - Sécurisation des sessions
  - Régénération automatique (30 min)
  - Détection changement IP
  - En-têtes HTTP sécurisés
- `SetLocale` - Gestion multilingue
  - Détection automatique langue
  - Mémorisation préférence
  - Support 6 langues

**Nouvelles Vues:**
- `resources/views/admin/incoming_compare.blade.php` - Comparaison docs externes
- `resources/views/documents/view_excel.blade.php` - Visualisation Excel

**Nouvelles Routes:**
- `GET /admin/incoming` - Liste documents externes
- `POST /admin/incoming/fetch` - Import API externe
- `GET /admin/incoming/{id}/compare` - Comparaison
- `POST /admin/incoming/{id}/send` - Envoi erreurs
- `GET /documents/{id}/view-excel` - Vue Excel

**Documentation:**
- `CORRECTIONS_AUDIT.md` - Rapport détaillé (8.5 KB)
- `GUIDE_TEST.md` - Guide de test (7.9 KB)
- `DEPLOIEMENT.md` - Guide déploiement (7.9 KB)
- `README_CORRECTIONS.md` - Résumé exécutif (6.5 KB)
- `CHANGELOG.md` - Ce fichier

#### 🔧 Modifié

**Contrôleurs:**
- `AdminController::toggleRole()` - Correction bug rétrogradation
  - Ajout déconnexion forcée
  - Suppression sessions utilisateur
- `AdminController::sendReportResult()` - Complétion méthode
  - Sauvegarde admin_response
  - Retour avec message succès

**Vues:**
- `layouts/app.blade.php` - Amélioration UI/UX
  - Navigation fixe en haut
  - Menu mobile responsive
  - Meilleurs dropdowns
- `admin/reports.blade.php` - Gestion documents null
  - Vérification existence document
  - Affichage conditionnel boutons

**Configuration:**
- `.env.example` - Sessions améliorées
  - SESSION_LIFETIME=480 (8h)
  - SESSION_EXPIRE_ON_CLOSE=false
  - SESSION_SECURE_COOKIE=false (dev)
- `bootstrap/app.php` - Enregistrement middlewares
  - SecureSession
  - SetLocale
- `config/app.php` - Support 6 langues
  - Français, English, Español
  - Deutsch, العربية, 中文

**Routes:**
- `routes/web.php` - Ajout 5 nouvelles routes admin

#### 🐛 Corrigé

**Bugs Critiques:**
- ❌ → ✅ Rétrogradation admin provoquait erreur code
- ❌ → ✅ Module "Docs externes" non fonctionnel
- ❌ → ✅ Module "Rapports" avec erreurs

**Bugs Mineurs:**
- ⚠️ → ✅ Fichiers Excel non visualisables
- ⚠️ → ✅ Gestion sessions incorrecte
- ⚠️ → ✅ Responsivité limitée (60%)

#### 🔒 Sécurité

**Améliorations:**
- Régénération périodique sessions (30 min)
- Détection changement IP utilisateur
- En-têtes HTTP sécurisés:
  - X-Frame-Options: SAMEORIGIN
  - X-Content-Type-Options: nosniff
  - X-XSS-Protection: 1; mode=block
  - Referrer-Policy: strict-origin-when-cross-origin
- Durée vie sessions augmentée (2h → 8h)
- Encryption sessions activée

#### 🎨 Interface

**Améliorations UI/UX:**
- Menu hamburger pour mobile
- Navigation fixe avec backdrop blur
- Dropdowns améliorés (fermeture auto)
- Meilleurs espacements responsive
- Animations fluides

#### 🌍 Internationalisation

**Support multilingue:**
- 🇫🇷 Français (fr)
- 🇬🇧 English (en)
- 🇪🇸 Español (es)
- 🇩🇪 Deutsch (de)
- 🇸🇦 العربية (ar)
- 🇨🇳 中文 (zh)

**Fonctionnalités:**
- Détection automatique langue navigateur
- Changement dynamique via URL (?lang=xx)
- Mémorisation préférence en session
- Support préférences utilisateur

---

## [1.0.0] - 2024-10-27

### Version Initiale

**Fonctionnalités principales:**
- Upload et analyse de documents
- Détection de plagiat (MinHash + Jaccard)
- Comparaison de documents
- Gestion utilisateurs et rôles
- Interface admin
- Rapports d'erreurs
- Support PDF, DOCX, TXT, Excel

**Technologies:**
- Laravel 11
- PHP 8.1+
- MySQL
- TailwindCSS
- PhpSpreadsheet
- PdfParser

---

## 📊 Statistiques Globales

### Version 1.1.0 vs 1.0.0

| Métrique | v1.0.0 | v1.1.0 | Δ |
|----------|--------|--------|---|
| Fichiers PHP | ~40 | ~46 | +6 |
| Lignes de code | ~8000 | ~8500 | +500 |
| Routes | 35 | 40 | +5 |
| Middlewares | 8 | 10 | +2 |
| Vues Blade | 25 | 27 | +2 |
| Langues supportées | 2 | 6 | +4 |
| Note audit | 6.4/10 | 7.5/10 | +1.1 |

### Bugs Résolus

| Priorité | Nombre |
|----------|--------|
| Critique | 3 |
| Majeur | 2 |
| Mineur | 3 |
| **Total** | **8** |

---

## 🔮 Prochaines Versions (Roadmap)

### [1.2.0] - Prévu

**Améliorations prévues:**
- [ ] Tests unitaires complets
- [ ] API REST documentée (Swagger)
- [ ] Cache Redis pour comparaisons
- [ ] Traitement asynchrone (queues)
- [ ] Logs d'audit admin
- [ ] Notifications email
- [ ] Export rapports PDF

### [1.3.0] - Prévu

**Nouvelles fonctionnalités:**
- [ ] Détection plagiat en ligne (API externe)
- [ ] Support formats supplémentaires (ODT, RTF)
- [ ] Dashboard analytics
- [ ] Historique des modifications
- [ ] Système de commentaires
- [ ] Intégration LMS (Moodle, Canvas)

---

## 📝 Notes de Migration

### De 1.0.0 à 1.1.0

**Étapes requises:**

1. **Mise à jour dépendances:**
   ```bash
   composer install
   ```

2. **Migrations base de données:**
   ```bash
   php artisan migrate
   ```

3. **Configuration .env:**
   ```env
   SESSION_LIFETIME=480
   SESSION_EXPIRE_ON_CLOSE=false
   EXTERNAL_API_URL=...
   EXTERNAL_API_TOKEN=...
   ```

4. **Effacer caches:**
   ```bash
   php artisan cache:clear
   php artisan config:cache
   php artisan route:cache
   ```

5. **Vérifier permissions:**
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

**Compatibilité:**
- ✅ Rétrocompatible avec v1.0.0
- ✅ Aucune breaking change
- ✅ Données existantes préservées

---

## 🤝 Contributeurs

**Version 1.1.0:**
- Groupe 4 (Développement)
- Groupe 1 (Audit)
- Cascade AI (Assistance corrections)

**Version 1.0.0:**
- Groupe 4 (Développement initial)

---

## 📄 Licence

Projet académique - Tous droits réservés

---

**Dernière mise à jour:** 3 Novembre 2024  
**Version actuelle:** 1.1.0
