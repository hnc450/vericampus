# VeriCampus

Plateforme multi-universités de vérification académique — voir l'analyse technique complète (architecture, schéma de base de données, risques de sécurité) fournie séparément.

## Ce qui est implémenté dans ce dépôt

- **Schéma complet** (21 migrations, toutes les tables du domaine)
- **Modèles Eloquent** avec isolation multi-tenant automatique (`UniversityScope`)
- **RBAC** via `spatie/laravel-permission` (rôles : `super_admin`, `university_admin`, `company_user`, `student`)
- **Policies** pour chaque entité sensible (Université, Faculté, Étudiant, Signature, Document, Entreprise)
- **Middleware** de scope (`university.scope`, `company.scope`, `active`)
- **Services métier** : `AcademicVerificationService`, `PalmaresImportService`, `VerificationDocumentService`, `QRCodeService`, `SignatureService`, `AuditLogService`, `FieldVisibilityService`
- **Import de palmarès** CSV/XLSX en deux temps (aperçu → confirmation), traitement asynchrone via `ProcessPalmaresImport`
- **Génération de documents PDF** avec QR Code (jeton opaque, jamais de PII dans le QR) et signature d'autorité
- **Page publique de vérification** `/verify/{token}` avec statut recalculé en direct (authentique / révoqué / expiré)
- **Espace entreprise** : recherche détaillée et rapide, désambiguïsation des homonymes, historique
- **Espace Super Admin** : gestion universités/entreprises/comptes
- **API REST** (Sanctum) : login, liste universités, recherche étudiants, statut/génération de vérification
- **Tests** : isolation multi-tenant (le contrôle de sécurité le plus critique du cahier des charges)

Ce qui reste volontairement plus léger pour une itération suivante : audit UI dédiée (le journal `audit_logs` est déjà alimenté), permissions par champ configurables depuis l'interface (le modèle `CompanyFieldPermission` existe, pas encore d'écran CRUD dessus), 2FA (les colonnes sont prêtes sur `users`).

## Prérequis

PHP 8.3+, Composer, MySQL 8+, Node 18+.

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate

# Auth Blade (génère resources/views/auth/*, routes/auth.php, layout par défaut).
# ⚠️ Notre resources/views/layouts/app.blade.php et nos routes réécrivent
# volontairement ce que Breeze pose par défaut — ne pas relancer breeze:install
# après avoir personnalisé ces fichiers, ça écraserait vos changements.
composer require laravel/breeze --dev
php artisan breeze:install blade
npm install && npm run build

php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag=permission-config
```

**Important — désactiver l'auto-inscription publique.** Breeze crée par défaut une page
« Créer un compte » accessible sans authentification. Ce n'est pas notre modèle : seuls le
Super Admin (comptes université/entreprise) et le flux d'invitation créent des comptes.
Après `breeze:install`, supprimez ou commentez les routes `register` dans
`routes/auth.php`, et retirez le lien « Register » du layout invité.

```bash
mysql -u root -e "CREATE DATABASE vericampus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

Renseignez `.env` (voir `.env.example`) puis :

```bash
php artisan migrate

# Définissez un mot de passe fort pour le premier compte Super Admin, sinon
# un mot de passe aléatoire sera généré et affiché une seule fois.
SUPER_ADMIN_EMAIL=admin@vericampus.test SUPER_ADMIN_PASSWORD=ChangeMe!2026 php artisan db:seed
```

## Lancer l'application

```bash
php artisan serve
php artisan queue:work   # nécessaire pour l'import de palmarès (traitement asynchrone)
npm run dev               # ou npm run build en production
```

En environnement `local`, `DatabaseSeeder` charge aussi `DemoUniversitySeeder` :
une université de démonstration (UNIKIN-DEMO), un admin (`admin@unikin-demo.test`
/ `password`), trois étudiants dont un avec un dossier académique validé, et un
compte entreprise (`rh@demo-rh.test` / `password`) — de quoi tester la recherche
→ vérification → génération de document → scan QR de bout en bout sans rien saisir.

## Tests

```bash
./vendor/bin/pest
```

Le test `tests/Feature/UniversityIsolationTest.php` vérifie noir sur blanc la
propriété de sécurité la plus critique du cahier des charges (§17) : une
université ne peut jamais lire les données d'une autre.

## Prochaines étapes suggérées

1. Écran de gestion de `CompanyFieldPermission` côté Super Admin (actuellement modifiable seulement via seed/tinker).
2. Vue dédiée pour consulter `audit_logs` (le service `AuditLogService` écrit déjà tout).
3. Activation de la 2FA (colonnes déjà en place sur `users`).
4. Tests de couverture sur `PalmaresImportService` (doublons, lignes invalides) et `VerificationDocumentService` (révocation, expiration).
