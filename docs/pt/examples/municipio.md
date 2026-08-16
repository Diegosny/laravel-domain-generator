# Exemplo Município

<VersionBadge version="v1.1.0"/>

Este exemplo demonstra relacionamentos utilizando o Repository Pattern.

---

## Cenário

Um `User` pertence a um `Municipio`.

```text
Municipio
   ▲
   │ belongsTo
   │
User
```

---

## Model

```php
public function municipio()
{
    return $this->belongsTo(Municipio::class);
}
```

---

## Repository

```php
$this->repository
    ->with(['municipio'])
    ->find($id);
```

---

## Resposta

```json
{
  "public_id": "01JXYZABCDEF",
  "nome": "João",
  "municipio": {
    "nome": "São Paulo"
  }
}
```

<Callout type="success">

O eager loading funciona automaticamente.

</Callout>