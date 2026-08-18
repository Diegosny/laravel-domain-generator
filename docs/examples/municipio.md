# Municipio Example

<VersionBadge version="v1.1.0"/>

This example demonstrates relationships using the generated Repository Pattern.

---

## Scenario

A `User` belongs to a `Municipio`.

<svg viewBox="0 0 320 140" width="100%" role="img" aria-label="User belongs to Municipio">
  <rect x="25" y="45" width="110" height="50" rx="12" fill="none" stroke="currentColor"/>
  <text x="80" y="73" text-anchor="middle" font-size="16">User</text>
  <path d="M135 70 L205 70" stroke="currentColor" stroke-width="2"/>
  <path d="M205 70 l-8 -6 M205 70 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="205" y="45" width="90" height="50" rx="12" fill="none" stroke="currentColor"/>
  <text x="250" y="73" text-anchor="middle" font-size="16">Municipio</text>
</svg>

---

## Model Relationship

```php
public function municipio(): BelongsTo
{
    return $this->belongsTo(Municipio::class);
}
```

---

## Repository

```php
$this->repository
    ->with(['municipio'])
    ->find($publicId);
```

---

## API Response

<ApiMethod method="GET"/> `/api/users/{public_id}`

```json
{
  "public_id": "01JXYZABCDEF",
  "nome": "John",
  "municipio": {
    "nome": "São Paulo"
  }
}
```

---

## Internal Flow

```text
Controller
    ↓
Service
    ↓
Repository::with()
    ↓
Eloquent
    ↓
Resource
```

The relationship is loaded only once, preventing unnecessary queries.

<Callout type="success">

Generated Repositories fully support eager loading through `with()`.

</Callout>