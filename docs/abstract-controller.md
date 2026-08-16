# AbstractController

<VersionBadge version="v1.1.0"/>

The `AbstractController` is the foundation for every generated controller.

It standardizes API responses and eliminates repetitive boilerplate.

---

## Features

- Standard JSON responses
- Success helper
- Error helper
- HTTP status support
- Consistent API structure

---

## Success Response

Generated controllers can return:

```php
return $this->success($user);
```

Response:

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "name": "John Doe"
  }
}
```

---

## Error Response

```php
return $this->error('User not found.', 404);
```

Response:

```json
{
  "type": "error",
  "status": 404,
  "message": "User not found."
}
```

---

## Why use it?

Instead of repeating response structures across controllers, every generated endpoint follows the same predictable format.

<Callout type="success">

Every generated controller extends `AbstractController` automatically.

</Callout>