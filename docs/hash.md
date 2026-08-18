# Public Identifiers

<VersionBadge version="v1.1.0"/>

Laravel Domain Generator includes built-in support for public identifiers.

Instead of exposing incremental database IDs, generated models can expose secure public IDs.

---

## Supported Formats

| Identifier | Example |
|------------|---------|
| ULID | `01JXYZABCDEF1234567890` |
| UUID | `550e8400-e29b-41d4-a716-446655440000` |
| UUID32 | `550e8400e29b41d4a716446655440000` |

---

## Why use Public IDs?

- Prevent ID enumeration
- Cleaner APIs
- Safer public endpoints
- Better frontend URLs

Example:

```text
/api/users/01JXYZABCDEF1234567890
```

instead of

```text
/api/users/12
```

---

## HasHash Trait

Generated models include:

```php
use HasHash;
```

The trait automatically manages public identifiers.

---

## Configuration

Choose your preferred identifier format inside:

```text
config/domain-generator.php
```

<Callout type="success">

Public IDs work transparently with generated Repositories and Resources.

</Callout>