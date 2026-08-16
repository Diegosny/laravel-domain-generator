# DTO

<VersionBadge version="v1.1.0"/>

Data Transfer Objects (DTOs) provide a clean way to move validated data across your application.

Instead of passing `Request` objects into Services, generated domains use strongly typed DTOs.

---

## Why DTOs?

- Separate HTTP from business logic
- Strong typing
- Cleaner Services
- Easier testing

---

## Generated DTO

Example:

```php
class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly bool $ativo
    ) {}
}
```

---

## Creating from Request

```php
$dto = UserDTO::fromRequest($request);
```

This extracts validated values automatically.

---

## Passing to Service

```php
$this->service->create($dto);
```

---

## Converting to Array

```php
$dto->toArray();
```

Output:

```php
[
    'nome' => 'John',
    'email' => 'john@example.com',
    'ativo' => true
]
```

<Callout type="success">

Generated Services are designed to receive DTOs instead of Request objects.

</Callout>