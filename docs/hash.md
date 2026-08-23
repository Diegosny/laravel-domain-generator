# Smart Public IDs

<VersionBadge version="v1.2.0"/>

Laravel Domain Generator automatically generates human-readable public identifiers for every Model using the `HasHash` trait.

Instead of exposing sequential IDs or long UUIDs in your API, the package creates short, readable and secure identifiers.

<Callout type="success">

Smart Public IDs are generated automatically. No additional logic is required in your Models.

</Callout>

---

# How it works

The database continues using the internal `id` as the primary key.

The API uses the `hash` column instead.

| Column | Purpose |
|--------|---------|
| `id` | Internal primary key |
| `hash` | Public API identifier |

Example stored in the database.

| id | hash |
|---|---|
| `1` | `PAT_K7XM4Q2R` |
| `2` | `PAT_J8L4M7XP` |

---

# Automatic prefixes

The prefix is generated dynamically from the Model name.

| Model | Result |
|--------|--------|
| `Patient` | `PAT_K7XM4Q2R` |
| `Product` | `PRO_J8N4W6XM` |
| `Organization` | `ORG_A7KM9Q2R` |
| `MedicalRecord` | `MRE_R6X3K8QP` |
| `UserSessionToken` | `UST_T9L2P7WK` |

Rules:

- Single-word models use the first three letters.
- Multi-word models generate an intelligent acronym.
- The random code avoids ambiguous characters.

---

# Migration

After generating a domain, update the migration before running it.

```php
Schema::create('patients', function (Blueprint $table) {

    $table->id();

    $table->string('hash', 20)
        ->unique()
        ->index();

    $table->timestamps();
    $table->softDeletes();
});
```

The current format fits comfortably within 20 characters.

```text
PAT_K7XM4Q2R
```

---

# Generated Model

The package automatically generates a Model similar to this.

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Domain\DomainGenerator\Traits\HasHash;

class Patient extends Model
{
    use HasFactory;
    use HasHash;

    protected $table = 'patients';

    protected string $hashPrefix = 'PAT';

    protected $fillable = [
        //
    ];
}
```

---

# Route Model Binding

The `HasHash` trait automatically changes Laravel's Route Model Binding.

```text
GET /api/patients/PAT_K7XM4Q2R
```

No controller changes are required.

---

# Creating a record

<ApiMethod method="POST"/> `/api/patients`

Request.

```json
{
  "name": "John Doe",
  "cpf": "12345678901"
}
```

Response.

```json
{
  "type": "success",
  "status": 201,
  "data": {
    "hash": "PAT_K7XM4Q2R",
    "name": "John Doe"
  }
}
```

---

# Listing records

<ApiMethod method="GET"/> `/api/patients`

Response.

```json
{
  "data": [
    {
      "hash": "PAT_K7XM4Q2R",
      "name": "John Doe"
    },
    {
      "hash": "PAT_J8L4M7XP",
      "name": "Jane Doe"
    }
  ]
}
```

---

# Finding a record

<ApiMethod method="GET"/> `/api/patients/PAT_K7XM4Q2R`

Response.

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "hash": "PAT_K7XM4Q2R",
    "name": "John Doe"
  }
}
```

---

# Updating a record

<ApiMethod method="PUT"/> `/api/patients/PAT_K7XM4Q2R`

Request.

```json
{
  "phone": "11999999999"
}
```

Response.

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "hash": "PAT_K7XM4Q2R",
    "phone": "11999999999"
  }
}
```

---

# Deleting a record

<ApiMethod method="DELETE"/> `/api/patients/PAT_K7XM4Q2R`

Response.

```json
{
  "type": "success",
  "status": 200,
  "message": "Record deleted successfully."
}
```

---

# Internal flow

```text
Patient::create(...)
        │
        ▼
HasHash::creating()
        │
        ▼
PublicIdGenerator
        │
        ▼
PAT_K7XM4Q2R
        │
        ▼
Database
```

The generated identifier provides billions of possible combinations while the `UNIQUE` database constraint guarantees integrity.

<Callout type="success">

Smart Public IDs provide cleaner APIs, hide internal IDs and deliver a developer experience similar to Stripe and GitHub.

</Callout>