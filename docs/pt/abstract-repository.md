# AbstractRepository

<VersionBadge version="v1.1.0"/>

O `AbstractRepository` oferece operações reutilizáveis de banco para todos os Repositories gerados.

---

## Métodos Inclusos

- `all()`
- `find()`
- `create()`
- `update()`
- `delete()`
- `paginate()`

---

## Exemplo

```php
$this->repository->paginate(15);
```

Resposta:

```json
{
  "current_page": 1,
  "per_page": 15,
  "total": 120
}
```

---

## Relacionamentos

É possível utilizar eager loading.

```php
$this->repository->with(['municipio'])->find($id);
```

---

## Por que Repository Pattern?

Ele separa acesso ao banco da regra de negócio, tornando o código mais organizado e testável.

<Callout type="success">

Todo domínio já nasce com um Repository pronto para uso.

</Callout>