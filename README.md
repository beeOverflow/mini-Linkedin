# 🔗 mini-LinkedIn — API REST Laravel

A RESTful back-end API inspired by LinkedIn, built with **Laravel 13** and secured with **JWT authentication**. The platform supports three user roles — candidates, recruiters, and administrators — each with a distinct set of permissions enforced through a hierarchical role middleware.

---

## 📑 Table of Contents

- [Features](#-features)
- [Tech Stack](#-tech-stack)
- [Architecture Overview](#-architecture-overview)
- [Database Schema](#-database-schema)
- [Role System](#-role-system)
- [API Endpoints](#-api-endpoints)
- [Events & Listeners](#-events--listeners)
- [Project Structure](#-project-structure)
- [Installation & Setup](#-installation--setup)
- [Environment Variables](#-environment-variables)
- [Running the Project](#-running-the-project)
- [Testing](#-testing)

---

## ✨ Features

| Feature | Description |
|---|---|
| 🔐 JWT Authentication | Stateless authentication using `tymon/jwt-auth` |
| 👤 Role-based Access Control | Hierarchical roles: `candidat < recruteur < admin` |
| 📄 Profile Management | Candidates can create and update their profile |
| 🧠 Skills (Compétences) | Many-to-many skill system with proficiency levels |
| 📢 Job Offers | Recruiters post, edit, and delete job listings |
| 📬 Applications | Candidates apply to active offers; recruiters review them |
| 📊 Status Tracking | Application status lifecycle: `en_attente → acceptee / refusee` |
| 📝 Event Logging | Application events are automatically logged to `storage/logs/candidatures.log` |
| 🛠️ Admin Panel | Admins manage users and toggle offer visibility |

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 13 |
| Language | PHP 8.3+ |
| Authentication | tymon/jwt-auth ^2.3 |
| Database | SQLite (default) / MySQL-compatible |
| Build tool | Vite |
| Testing | PHPUnit 12 |

---

## 🏗️ Architecture Overview

```
mini-Linkedin/
├── app/
│   ├── Events/             # Domain events
│   ├── Http/
│   │   ├── Controllers/    # Request handlers
│   │   └── Middleware/     # Role-based access guard
│   ├── Listeners/          # Event handlers (logging)
│   ├── Models/             # Eloquent models
│   └── Providers/          # Service & event binding
├── database/
│   ├── migrations/         # Database schema
│   ├── factories/          # Test data factories
│   └── seeders/
├── routes/
│   └── api.php             # All API routes
└── storage/
    └── logs/
        └── candidatures.log  # Application activity log
```

---

## 🗄️ Database Schema

### `users`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint | Primary key |
| `name` | string | Full name |
| `email` | string | Unique |
| `password` | string | Bcrypt hashed |
| `role` | enum | `candidat`, `recruteur`, `admin` |
| `timestamps` | — | `created_at`, `updated_at` |

### `profils`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint | Primary key |
| `user_id` | FK → users | Cascade delete |
| `titre` | string | Job title / headline |
| `bio` | text | nullable |
| `localisation` | string | nullable |
| `disponible` | boolean | Default `true` |
| `timestamps` | — | — |

### `competences`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint | Primary key |
| `nom` | string | Skill name |
| `timestamps` | — | — |

### `profil_competence` *(pivot)*
| Column | Type | Notes |
|---|---|---|
| `profil_id` | FK | — |
| `competence_id` | FK | — |
| `niveau` | enum | `débutant`, `intermédiaire`, `expert` |

### `offres`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint | Primary key |
| `user_id` | FK → users | Recruiter who posted it |
| `titre` | string | Job title |
| `description` | text | Full description |
| `localisation` | string | Location |
| `type` | enum | `CDI`, `CDD`, `stage` |
| `actif` | boolean | Visibility toggle (default `true`) |
| `timestamps` | — | — |

### `candidatures`
| Column | Type | Notes |
|---|---|---|
| `id` | bigint | Primary key |
| `offre_id` | FK → offres | Cascade delete |
| `profil_id` | FK → profils | Cascade delete |
| `message` | text | Cover letter (nullable) |
| `statut` | enum | `en_attente`, `acceptee`, `refusee` |
| `timestamps` | — | — |

**Relationships overview:**

```
User ─── hasOne ──► Profil ─── belongsToMany ──► Competences
User ─── hasMany ──► Offres
Profil ─── hasMany ──► Candidatures
Offre ─── hasMany ──► Candidatures
```

---

## 🔐 Role System

Roles form a strict hierarchy enforced by the `CheckRole` middleware:

```
candidat (1) < recruteur (2) < admin (3)
```

A user with a higher level automatically satisfies lower-level requirements. So an `admin` can access all `recruteur` and `candidat` routes.

**Middleware registration:** `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias(['role' => CheckRole::class]);
})
```

---

## 📡 API Endpoints

All routes are prefixed with `/api`.

### 🔓 Public (no authentication required)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/register` | Create a new account |
| `POST` | `/login` | Authenticate and receive a JWT token |

**Register payload:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "secret123",
  "role": "candidat"
}
```

**Login response:**
```json
{
  "access_token": "<JWT>",
  "token_type": "bearer",
  "expires_in": 3600
}
```

> All protected routes require the header: `Authorization: Bearer <token>`

---

### 🔒 Authenticated — All roles (`auth:api`)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/offres` | List active job offers (paginated, filterable) |
| `GET` | `/offres/{id}` | View a single job offer |

**Query parameters for `GET /offres`:**
- `localisation` — filter by location (partial match)
- `type` — filter by contract type (`CDI`, `CDD`, `stage`)

---

### 👤 Candidat role (`candidat` and above)

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/me` | Get the current authenticated user |
| `POST` | `/refresh` | Refresh the JWT token |
| `POST` | `/logout` | Invalidate the current token |
| `POST` | `/profil` | Create your profile (once only) |
| `GET` | `/profil` | View your profile with skills |
| `PUT` | `/profil` | Update your profile |
| `POST` | `/profil/competences` | Add a skill to your profile |
| `DELETE` | `/profil/competences/{id}` | Remove a skill from your profile |
| `POST` | `/offres/{id}/candidater` | Apply to a job offer |
| `GET` | `/mes-candidatures` | View your applications |

**Apply to an offer payload:**
```json
{
  "message": "I am very interested in this position..."
}
```

**Add a skill payload:**
```json
{
  "competence_id": 3,
  "niveau": "intermédiaire"
}
```

---

### 🏢 Recruteur role (`recruteur` and above)

| Method | Endpoint | Description |
|---|---|---|
| `POST` | `/offres` | Create a new job offer |
| `PUT` | `/offres/{id}` | Update your offer (owner only) |
| `DELETE` | `/offres/{id}` | Delete your offer (owner only) |
| `GET` | `/offres/{id}/candidatures` | View applications received for your offer |
| `PATCH` | `/candidatures/{id}/statut` | Update application status |

**Create offer payload:**
```json
{
  "titre": "Senior PHP Developer",
  "description": "We are looking for...",
  "localisation": "Paris",
  "type": "CDI"
}
```

**Change application status payload:**
```json
{
  "statut": "acceptee"
}
```
> Valid values: `en_attente`, `acceptee`, `refusee`

---

### 🛡️ Admin role

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/admin/users` | List all users with their profiles |
| `DELETE` | `/admin/users/{id}` | Delete a user (cannot delete self) |
| `PATCH` | `/admin/offres/{id}` | Toggle offer visibility (active/inactive) |

---

## 📡 Events & Listeners

The platform uses Laravel's event system to log key actions asynchronously.

| Event | Triggered by | Listener | Log output |
|---|---|---|---|
| `CandidatureDeposee` | `POST /offres/{id}/candidater` | `LogCandidatureDeposee` | Logs candidate name + offer title |
| `StatutCandidatureMis` | `PATCH /candidatures/{id}/statut` | `LogStatutCandidatureMis` | Logs candidature ID + old + new status |

**Log file location:** `storage/logs/candidatures.log`

**Sample log entries:**
```
[2026-04-19 14:32:00] Candidature déposée — Candidat : Jane Doe | Offre : Senior PHP Developer
[2026-04-19 14:45:12] Statut mis à jour — Candidature #7 | Ancien statut : en_attente | Nouveau statut : acceptee
```

Events are registered in `AppServiceProvider::boot()`:
```php
Event::listen(CandidatureDeposee::class, LogCandidatureDeposee::class);
Event::listen(StatutCandidatureMis::class, LogStatutCandidatureMis::class);
```

---

## 📁 Project Structure

```
app/
├── Events/
│   ├── CandidatureDeposee.php       # Fired when a candidate applies
│   └── StatutCandidatureMis.php     # Fired when application status changes
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php        # register, login, logout, me, refresh
│   │   ├── ProfilController.php      # CRUD profile + skills
│   │   ├── OffreController.php       # CRUD job offers
│   │   ├── CandidatureController.php # Apply + review applications
│   │   └── AdminController.php       # Admin user/offer management
│   └── Middleware/
│       └── CheckRole.php             # Hierarchical role guard
├── Listeners/
│   ├── LogCandidatureDeposee.php     # Logs new applications
│   └── LogStatutCandidatureMis.php   # Logs status changes
├── Models/
│   ├── User.php          # Implements JWTSubject; roles: candidat/recruteur/admin
│   ├── Profil.php        # Candidate profile
│   ├── Competence.php    # Skill entity
│   ├── Offre.php         # Job offer
│   └── Candidature.php   # Application record
└── Providers/
    └── AppServiceProvider.php  # Event binding
```

---

## ⚙️ Installation & Setup

### Prerequisites

- **PHP** >= 8.3
- **Composer**
- **Node.js** + **npm**
- **SQLite** (default) or a MySQL/PostgreSQL database

### Steps

**1. Clone the repository**
```bash
git clone <repository-url>
cd mini-Linkedin
```

**2. Install PHP dependencies**
```bash
composer install
```

**3. Copy the environment file and generate the app key**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Generate the JWT secret key**
```bash
php artisan jwt:secret
```

**5. Run database migrations**
```bash
php artisan migrate
```

**6. Install frontend dependencies and build assets**
```bash
npm install
npm run build
```

> **One-command setup:** All the above steps are combined in the `setup` Composer script:
> ```bash
> composer setup
> ```

---

## 🌍 Environment Variables

Key variables to configure in your `.env` file:

| Variable | Default | Description |
|---|---|---|
| `APP_NAME` | `Laravel` | Application name |
| `APP_ENV` | `local` | Environment (`local`, `production`) |
| `APP_KEY` | — | Generated by `artisan key:generate` |
| `APP_URL` | `http://localhost` | Base URL |
| `DB_CONNECTION` | `sqlite` | Database driver |
| `DB_HOST` | `127.0.0.1` | DB host (MySQL/Postgres) |
| `DB_DATABASE` | — | Database name |
| `DB_USERNAME` | — | Database user |
| `DB_PASSWORD` | — | Database password |
| `JWT_SECRET` | — | Generated by `artisan jwt:secret` |
| `QUEUE_CONNECTION` | `database` | Queue driver for async jobs |

---

## 🚀 Running the Project

### Development mode

Starts the Laravel server, queue worker, log watcher, and Vite dev server concurrently:

```bash
composer dev
```

Or individually:

```bash
# API server
php artisan serve

# Queue worker (for async jobs)
php artisan queue:listen --tries=1

# Vite dev server
npm run dev
```

The API will be available at: **`http://localhost:8000/api`**

---

## 🧪 Testing

```bash
composer test
# or
php artisan test
```

---

## 📌 Quick Reference — HTTP Status Codes

| Code | Meaning |
|---|---|
| `200` | OK |
| `201` | Created successfully |
| `401` | Unauthenticated (missing or invalid token) |
| `403` | Forbidden (insufficient role) |
| `404` | Resource not found |
| `422` | Validation error or business rule violation |

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
