# AbstractRepository

<VersionBadge version="v1.1.0"/>

The `AbstractRepository` provides reusable database operations for every generated Repository.

---

## Included Methods

- `all()`
- `find()`
- `create()`
- `update()`
- `delete()`
- `paginate()`

---

## Example

```php
$this->repository->paginate(15);
```

Returns:

```json
{
  "current_page": 1,
  "per_page": 15,
  "total": 120
}
```

---

## Relationships

Generated repositories support eager loading.

```php
$this->repository->with(['municipio'])->find($id);
```

---

## Why Repository Pattern?

It separates persistence from business logic, making applications easier to maintain and test.

<Callout type="success">

Repositories are generated automatically with every domain.

</Callout>