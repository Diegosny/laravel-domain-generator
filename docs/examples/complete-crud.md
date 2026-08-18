# Complete CRUD Example

<VersionBadge version="v1.1.0"/>

This guide demonstrates the complete lifecycle of a generated domain, from running the Artisan command to exposing a production-ready REST API.

<Callout type="info">

This is the recommended starting point for understanding how Laravel Domain Generator structures a new domain.

</Callout>

---

## What you'll build

Running a single Artisan command generates a complete CRUD following DDD and Clean Architecture.

Included components:

- Model
- Migration
- Controller
- Form Requests
- DTO
- Service
- Repository
- Resource
- API Routes

---

## Generation

Run:

```bash
php artisan make:domain User
```

The generator creates every layer already connected.

---

## Generated Structure

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

Each file has a single responsibility.

---

## Request Flow

Every request follows this pipeline.

<svg viewBox="0 0 760 180" width="100%" role="img" aria-label="CRUD request flow">
  <rect x="20" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="65" y="84" text-anchor="middle" font-size="14">Request</text>
  <path d="M110 80 L165 80" stroke="currentColor" stroke-width="2"/>
  <path d="M165 80 l-8 -6 M165 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="165" y="55" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="220" y="84" text-anchor="middle" font-size="14">FormRequest</text>
  <path d="M275 80 L340 80" stroke="currentColor" stroke-width="2"/>
  <path d="M340 80 l-8 -6 M340 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="340" y="55" width="70" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="375" y="84" text-anchor="middle" font-size="14">DTO</text>
  <path d="M410 80 L470 80" stroke="currentColor" stroke-width="2"/>
  <path d="M470 80 l-8 -6 M470 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="470" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="515" y="84" text-anchor="middle" font-size="14">Service</text>
  <path d="M560 80 L620 80" stroke="currentColor" stroke-width="2"/>
  <path d="M620 80 l-8 -6 M620 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="620" y="55" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="675" y="84" text-anchor="middle" font-size="14">Repository</text>
</svg>

This architecture keeps HTTP concerns separated from business rules.

---

## Generated Controller

```php
class UserController extends AbstractController
{
    protected mixed $service;

    protected ?string $requestValidate = UserRequest::class;

    protected ?string $requestDto = UserDTO::class;

    protected ?string $resource = UserResource::class;
}
```

Notice that almost no CRUD logic is required.

---

## Generated Service

```php
public function create(UserDTO $dto)
{
    return $this->repository->create(
        $dto->toArray()
    );
}
```

Business rules belong here.

---

## Generated Repository

```php
public function model(): string
{
    return User::class;
}
```

Database operations stay isolated.

---

## Create User

<ApiMethod method="POST"/> `/api/users`

Request:

```json
{
  "nome": "John Doe",
  "email": "john@example.com",
  "password": "secret123"
}
```

Response:

```json
{
  "type": "success",
  "status": 201,
  "data": {
    "public_id": "01JXYZABCDEF123456789",
    "nome": "John Doe",
    "email": "john@example.com"
  }
}
```

---

## List Users

<ApiMethod method="GET"/> `/api/users`

Response:

```json
{
  "data": [],
  "links": {},
  "meta": {}
}
```

Pagination works automatically.

---

## Update User

<ApiMethod method="PUT"/> `/api/users/{public_id}`

Request:

```json
{
  "nome": "John Updated"
}
```

The update pipeline uses its own FormRequest and DTO.

---

## Delete User

<ApiMethod method="DELETE"/> `/api/users/{public_id}`

If SoftDeletes is enabled, the record is archived instead of permanently removed.

---

## Best Practices

- Keep Controllers thin.
- Put business rules inside Services.
- Return Resources.
- Use public identifiers.
- Let Repositories own persistence.

<Callout type="success">

The generated CRUD already follows the same layered architecture used throughout Laravel Domain Generator.

</Callout>