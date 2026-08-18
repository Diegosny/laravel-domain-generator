# Troubleshooting

## JWT Login Error

**Error**

> `EloquentUserProvider::validateCredentials()`

### Cause

The User model extended `Model` instead of `Authenticatable`.

### Fix

```php
class User extends Authenticatable
```

---

## GitHub Pages 404

### Cause

GitHub Pages wasn't configured.

### Fix

Settings → Pages → Source → GitHub Actions.

---

## Route file not found

### Cause

Wrong package path.

### Fix

```php
__DIR__.'/../../routes/api.php'
```

---

## PHPStan LARAVEL_VERSION

### Cause

Larastan was executed inside the package instead of a Laravel application.

### Fix

Use PHPStan inside the package and Larastan in integration tests.

---

## Composer PSR-4

### Fix

Run:

```bash
composer dump-autoload --optimize --strict-psr
```