<p align="center">
  <img src="docs/public/logo.svg" alt="Laravel Domain Generator" width="140">
</p>

<h1 align="center">Laravel Domain Generator</h1>

<p align="center">
  Generate Laravel Domains using <strong>DDD</strong> and <strong>Clean Architecture</strong>.
</p>

<p align="center">
  Automate Controllers, DTOs, Services, Repositories, Resources, JWT authentication and public identifiers with a single Artisan command.
</p>

<p align="center">

![Packagist Version](https://img.shields.io/packagist/v/domain/laravel-domain-generator?style=for-the-badge)

![Packagist Downloads](https://img.shields.io/packagist/dt/domain/laravel-domain-generator?style=for-the-badge)

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge)

![Laravel](https://img.shields.io/badge/Laravel-11%20%7C%2012%20%7C%2013-FF2D20?style=for-the-badge)

</p>

<p align="center">

![CI Lint](https://github.com/SEU_USUARIO/laravel-domain-generator/actions/workflows/ci-lint.yml/badge.svg)

![PHPStan](https://github.com/SEU_USUARIO/laravel-domain-generator/actions/workflows/ci-phpstan.yml/badge.svg)

![Tests](https://github.com/SEU_USUARIO/laravel-domain-generator/actions/workflows/ci-tests.yml/badge.svg)

![Docs](https://github.com/SEU_USUARIO/laravel-domain-generator/actions/workflows/deploy-docs.yml/badge.svg)

![CodeQL](https://github.com/SEU_USUARIO/laravel-domain-generator/actions/workflows/security-codeql.yml/badge.svg)

</p>

---

## Why Laravel Domain Generator?

Building Laravel applications usually means repeating the same boilerplate over and over.

Instead of manually creating Controllers, Services, DTOs, Repositories and wiring everything together, this package generates an organized Domain structure following **DDD** and **Clean Architecture** from day one.

```bash
php artisan make:domain User
```

Generated structure:

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       ├── Repositories/
│       └── Service/
├── Http/
│   ├── Controllers/
│   └── Requests/
└── Models/
```

---

## Features

- Domain Driven Design structure
- Clean Architecture
- Automatic DTO conversion
- Repository Pattern
- API Resources
- Pagination
- JWT Authentication
- Public identifiers (ULID, UUID and UUID32)
- Safe relationship loading (`with=`)
- Automatic filtering
- GitHub Actions
- Professional VitePress documentation

---

## Quick Start

Install the package:

```bash
composer require domain/laravel-domain-generator
```

Generate the JWT secret:

```bash
php artisan jwt:secret
```

Create your first domain:

```bash
php artisan make:domain User
```

---

## Architecture

The package keeps responsibilities separated.

<svg viewBox="0 0 900 140" width="100%" role="img" aria-label="Fluxo Controller DTO Service Repository Model">
  <rect x="20" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="180" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="340" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="500" y="40" width="140" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="680" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="80" y="70" text-anchor="middle" font-size="14">Controller</text>
  <text x="240" y="70" text-anchor="middle" font-size="14">DTO</text>
  <text x="400" y="70" text-anchor="middle" font-size="14">Service</text>
  <text x="570" y="70" text-anchor="middle" font-size="14">Repository</text>
  <text x="740" y="70" text-anchor="middle" font-size="14">Model</text>
  <path d="M140 65 H180 M300 65 H340 M460 65 H500 M640 65 H680" stroke="currentColor" fill="none"/>
</svg>

| Layer | Responsibility |
|--------|----------------|
| Controller | HTTP |
| FormRequest | Validation |
| DTO | Data transport |
| Service | Business rules |
| Repository | Persistence |
| Model | Database |
| Resource | HTTP response |

---

## JWT Authentication

Built-in endpoints:

| Method | Endpoint |
|---------|----------|
| POST | `/api/auth/login` |
| GET | `/api/auth/me` |
| POST | `/api/auth/refresh` |
| POST | `/api/auth/logout` |

---

## Public Identifiers

The package supports:

| Strategy | Size | Sortable |
|----------|------|----------|
| ULID | 26 | Yes |
| UUID | 36 | No |
| UUID32 | 32 | No |

Example:

```
GET /api/users/01K2JP6D7N7YF5YJ9W3X1M8Q2R
```

---

# Quality Gates

This project follows an automated quality pipeline inspired by projects like Laravel, Filament and Spatie.

| Pipeline | Purpose |
|----------|---------|
| **CI • Lint** | Laravel Pint + PSR-4 validation |
| **CI • PHPStan** | Static analysis |
| **CI • Tests** | Multi-version testing |
| **Security • Audit** | Composer vulnerability scan |
| **Security • CodeQL** | GitHub security analysis |
| **Deploy • VitePress** | Documentation deployment |
| **Release** | Automatic semantic releases |

Pipeline overview:

```text
Push / PR
    │
    ▼
CI Lint
    │
    ▼
PHPStan
    │
    ▼
Tests
    │
    ▼
Release
    │
    ▼
Deploy Docs
```

Every push to `master` automatically:

- formats the code with Laravel Pint;
- validates PSR-4 namespaces;
- runs PHPStan;
- executes the test suite;
- performs security analysis;
- creates a GitHub Release;
- publishes the documentation.

---

## Documentation

The complete documentation is available at:

> **https://Diegosny.github.io/laravel-domain-generator**

It includes:

- Installation
- DDD architecture
- `make:domain`
- DTOs
- AbstractController
- AbstractService
- AbstractRepository
- JWT
- Hash (ULID/UUID)
- Resources
- Troubleshooting
- Complete CRUD examples

---

## Roadmap

- Automatic Resource generation
- Store/Update DTO generation
- Domain Events
- Value Objects
- Policies
- Multi-tenancy
- Cache layer
- Integration Smoke Tests

---

## Contributing

Contributions are welcome.

Please read:

- Pull Request Template
- Issue Templates
- Coding Standards (Laravel Pint + PHPStan)

before opening a Pull Request.

---

## License

Released under the **MIT License**.