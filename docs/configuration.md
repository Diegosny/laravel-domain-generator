# Configuration

<VersionBadge version="v1.1.0"/>

Laravel Domain Generator can be customized through its configuration file.

---

## Publish

```bash
php artisan vendor:publish --provider="Domain\DomainGenerator\DomainGeneratorServiceProvider"
```

---

## Configuration File

```text
config/domain-generator.php
```

---

## Authentication

```php
'auth' => [
    'guard' => 'api',
    'login_field' => 'email',
],
```

---

## Public IDs

```php
'hash' => [
    'driver' => 'ulid',
],
```

Supported values:

- `ulid`
- `uuid`
- `uuid32`

---

## Namespace

Generated classes respect your application's namespace automatically.

<Callout type="info">

Most projects work perfectly with the default configuration.

</Callout>