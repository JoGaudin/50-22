# Laravel Boilerplate

Un boilerplate Laravel moderne et prêt à l'emploi, intégrant une stack complète pour démarrer rapidement un projet web robuste.

## Stack technique

**Backend**
- [Laravel 13](https://laravel.com) — PHP 8.4
- [Inertia.js](https://inertiajs.com) — bridge SPA sans API REST
- [Laravel Breeze](https://laravel.com/docs/starter-kits) — authentification
- [Laravel Sanctum](https://laravel.com/docs/sanctum) — gestion des tokens
- [Google 2FA](https://github.com/antonioribeiro/google2fa-laravel) — double authentification TOTP
- [WebPush](https://github.com/laravel-notification-channels/webpush) — notifications push navigateur

**Frontend**
- [Vue 3](https://vuejs.org) + [TypeScript](https://www.typescriptlang.org)
- [Tailwind CSS v3](https://tailwindcss.com)
- [Reka UI](https://reka-ui.com) — composants headless (style shadcn/ui)
- [TanStack Vue Table](https://tanstack.com/table) — tableaux de données
- [VeeValidate](https://vee-validate.logaretm.com) + [Zod](https://zod.dev) — validation de formulaires
- [VueUse](https://vueuse.org) — composables utilitaires
- [Lucide Vue](https://lucide.dev) — icônes
- [vue-sonner](https://github.com/wobsoriano/vue-sonner) — notifications toast

**Infrastructure (Docker)**
- **Traefik** — reverse proxy, accès via `{APP_SLUG}.localhost`
- **PHP-FPM** — service applicatif
- **Nginx** — serveur web
- **PostgreSQL 16** — base de données (Neon en production)
- **Node** — serveur Vite en développement
- **Adminer** — `db.{APP_SLUG}.localhost`
- **MailHog** — capture d'emails `mail.{APP_SLUG}.localhost`

## Architecture

Le projet suit une architecture en couches :

```
app/
├── Contracts/Repositories/   # Interfaces des repositories
├── Http/                     # Controllers, Middleware, Requests
├── Models/                   # Modèles Eloquent
├── Notifications/            # Notifications Laravel
├── Providers/                # Service Providers
├── Repositories/             # Implémentations des repositories
└── UseCases/                 # Logique métier (Auth, Right, Role, Setting, User)
```

```
resources/js/
├── Components/               # Composants Vue réutilisables (+ composants UI)
├── Layouts/                  # Layouts (AuthenticatedLayout, GuestLayout…)
├── Pages/                    # Pages Inertia (Admin, Auth, Dashboard, Profile)
├── composables/              # Composables Vue
└── lib/                      # Utilitaires (cn, etc.)
```

## Démarrage rapide

### Sans Docker (développement local)

```bash
# 1. Installer les dépendances et configurer l'environnement
composer setup

# 2. Lancer tous les services en parallèle
composer dev
```

`composer dev` démarre simultanément : serveur PHP, worker de queue, Pail (logs) et Vite.

### Avec Docker

```bash
# 1. Copier et configurer l'environnement
cp .env.example .env
# Décommenter les variables Docker dans .env (DB_HOST=postgres, APP_URL, MAIL_HOST…)
# Définir APP_SLUG pour personnaliser les sous-domaines

# 2. Lancer les conteneurs
make up

# 3. Installer les dépendances et migrer
make bbash
# Dans le conteneur :
composer setup
```

L'application est ensuite accessible sur `http://{APP_SLUG}.localhost`.

## Commandes utiles

### Makefile

| Commande | Description |
|---|---|
| `make up` | Démarrer les conteneurs Docker |
| `make down` | Arrêter les conteneurs Docker |
| `make bbash` | Ouvrir un shell dans le conteneur PHP |
| `make migrate` | Lancer les migrations via Docker |
| `make import-db` | Importer `database.sql` depuis la racine |

### Composer

| Commande | Description |
|---|---|
| `composer setup` | Installation complète (deps, .env, clé, migrations, npm, build) |
| `composer dev` | Lancer tous les services de développement |
| `composer test` | Lancer la suite de tests PHPUnit |

## Variables d'environnement clés

| Variable | Description |
|---|---|
| `APP_SLUG` | Préfixe des sous-domaines locaux (ex. `myapp` → `myapp.localhost`) |
| `APP_URL` | URL principale (Docker : `http://{APP_SLUG}.localhost`) |
| `DB_*` | Connexion PostgreSQL (Neon en production) |
| `QUEUE_CONNECTION` | `sync` en local, `database` ou `redis` en production |
| `MAIL_HOST` | `mailhog` en Docker, SMTP en production |

Consultez `.env.example` pour la liste complète et les commentaires.

## Tests

```bash
composer test
```

## Licence

[MIT](https://opensource.org/licenses/MIT)
