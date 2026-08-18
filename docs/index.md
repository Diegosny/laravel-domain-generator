---
layout: home

hero:
  name: Laravel Domain Generator
  text: Build Laravel domains with DDD and Clean Architecture
  tagline: Generate Controllers, DTOs, Services, Repositories, JWT-ready APIs and Public Identifiers with a single Artisan command.

  image:
    src: /logo.svg
    alt: Laravel Domain Generator

  actions:
    - theme: brand
      text: Get Started
      link: /getting-started

    - theme: alt
      text: GitHub
      link: https://github.com/Diegosny/laravel-domain-generator

features:
  - title: DDD Architecture
    details: Organize Controllers, Services, Repositories and DTOs from day one.

  - title: One Command
    details: Generate an entire Laravel domain with a single Artisan command.

  - title: JWT Ready
    details: Authentication endpoints already integrated.

  - title: Public Identifiers
    details: Native support for ULID, UUID and UUID32.

  - title: Repository Pattern
    details: Generic repositories with pagination, filters and relationships.

  - title: Professional CI/CD
    details: GitHub Actions, Releases, PHPStan, Laravel Pint and automatic documentation deployment.
---

<HeroDashboard/>

> Built for developers who care about architecture, maintainability and automation.

## Quick Start

Install the package.

```bash
composer require domain/laravel-domain-generator
```

Generate your first domain.

```bash
php artisan make:domain User
```

<Callout type="success">

Your first complete Laravel domain is generated in seconds.

</Callout>

---

## Why Laravel Domain Generator?

Instead of manually creating Controllers, DTOs, Services, Repositories and authentication boilerplate, the package generates a scalable Domain Driven Design structure automatically.

Perfect for projects using:

- Domain Driven Design (DDD)
- Clean Architecture
- Repository Pattern
- REST APIs
- JWT Authentication
- Public Identifiers

---

## Trusted Development Workflow

The package follows an automated quality pipeline inspired by Laravel, Filament and Spatie.

| Feature | Description |
|---------|-------------|
| 🚀 Automated Releases | Semantic versioning with GitHub Releases |
| 📖 Live Documentation | Automatic deployment with VitePress |
| 🔒 Security | Composer Audit and CodeQL |
| ⚙️ Code Quality | Laravel Pint and PHPStan |
| 📦 Package Validation | Composer validation on every push |
| 🧪 CI Pipeline | Lint, Build and Documentation deployment |

---

## Generated Architecture

The package generates a structure similar to this.

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       ├── Repositories/
│       └── Service/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
└── Models/
```

Every generated file follows Laravel conventions while keeping business logic isolated inside the Domain layer.

---

## Why developers choose this package

| Feature | Benefit |
|---------|---------|
| 🧱 **DDD-first** | Domain-oriented architecture from day one. |
| 🔐 **JWT Ready** | Authentication endpoints already included. |
| 🆔 **Public IDs** | ULID, UUID and UUID32 support. |
| 📦 **Generic Repository** | Pagination, filters and relationships built in. |
| 🏗️ **Clean Architecture** | Business logic isolated from HTTP. |
| 🚀 **Professional CI/CD** | GitHub Actions, Releases and automated documentation. |

---

## Next Step

Continue with the Installation guide and generate your first production-ready Laravel domain.

**→ Next:** [Installation](/installation)