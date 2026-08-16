# Resources

<VersionBadge version="v1.1.0"/>

Os Resources gerados padronizam a serialização da API.

Todo domínio já nasce com um Resource pronto para produção.

---

## Exemplo

```php
return new UserResource($user);
```

Resposta:

```json
{
  "public_id": "01JXYZABCDEF123456789",
  "nome": "João",
  "email": "joao@email.com",
  "ativo": true
}
```

---

## Coleções

```php
return UserResource::collection($users);
```

---

## Personalização

Basta editar o Resource gerado.

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

Os Resources ocultam IDs internos e expõem apenas identificadores públicos.

</Callout>