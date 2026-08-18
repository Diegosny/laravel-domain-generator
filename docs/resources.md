# API Resources

<VersionBadge version="v1.1.0"/>

Generated Resources standardize API serialization.

Every generated domain includes a Resource class ready for production.

---

## Example

```php
return new UserResource($user);
```

Response:

```json
{
  "public_id": "01JXYZABCDEF123456789",
  "nome": "John Doe",
  "email": "john@example.com",
  "ativo": true
}
```

---

## Collections

```php
return UserResource::collection($users);
```

Produces a consistent collection response.

---

## Customizing

Simply edit the generated Resource.

```php
public function toArray($request): array
{
    return [
        'public_id' => $this->public_id,
        'nome' => $this->nome,
        'email' => $this->email
    ];
}
```

<Callout type="info">

Resources hide internal IDs by default, exposing only public identifiers.

</Callout>