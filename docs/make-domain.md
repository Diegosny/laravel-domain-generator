# make:domain

<VersionBadge version="v2.0"/>

The `make:domain` command generates a complete Domain Driven Design structure with a single command.

## Usage

```bash
php artisan make:domain User
```

To overwrite existing files:

```bash
php artisan make:domain User --force
```

<Callout type="success">

The command uses Laravel's native generators whenever possible, preserving framework conventions.

</Callout>

## Generated Structure

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       │   └── UserDTO.php
│       ├── Repositories/
│       │   └── UserRepository.php
│       └── Service/
│           └── UserService.php
├── Http/
│   ├── Controllers/
│   │   └── UserController.php
│   └── Requests/
│       └── UserRequest.php
└── Models/
    └── User.php
```

## Generation Flow

<svg viewBox="0 0 760 120" width="100%" role="img" aria-label="make:domain generation flow">
  <rect x="20" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="170" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="320" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="470" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="620" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <text x="75" y="62" text-anchor="middle" font-size="13">Command</text>
  <text x="225" y="62" text-anchor="middle" font-size="13">Model</text>
  <text x="375" y="62" text-anchor="middle" font-size="13">Migration</text>
  <text x="525" y="62" text-anchor="middle" font-size="13">Domain</text>
  <text x="675" y="62" text-anchor="middle" font-size="13">HTTP</text>
  <path d="M130 57 H170 M280 57 H320 M430 57 H470 M580 57 H620" stroke="currentColor" fill="none"/>
</svg>

## What is generated?

| Component | Purpose |
|-----------|---------|
| Model | Eloquent model |
| Migration | Database schema |
| Controller | HTTP entry point |
| FormRequest | Validation |
| DTO | Data transport |
| Service | Business logic |
| Repository | Persistence |