# Installation

<VersionBadge version="v1.1.0"/>

Installing the package takes only a few minutes.

---

## Requirements

| Requirement | Version |
|------------|---------|
| PHP | 8.2+ |
| Laravel | 11, 12 or 13 |
| Composer | Latest |

---

## Install

Run:

```bash
composer require domain/laravel-domain-generator
```

<Callout type="info">

The package supports Laravel 11, 12 and 13.

</Callout>

---

## Publish Configuration

```bash
php artisan vendor:publish --provider="Domain\DomainGenerator\DomainGeneratorServiceProvider"
```

This publishes the configuration file:

```text
config/domain-generator.php
```

---

## Generate JWT Secret

```bash
php artisan jwt:secret
```

<Callout type="success">

Your application is now ready to generate domains and use the built-in authentication endpoints.

</Callout>

---

## Generate your first domain

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
│   ├── Requests/
│   └── Resources/
└── Models/
```

---

## Next Step

Continue to **JWT Authentication**.