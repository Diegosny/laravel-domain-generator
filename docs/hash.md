# Identificadores Públicos (Hash)

O Laravel Domain Generator possui suporte nativo para identificadores públicos através do trait `HasHash`.

Essa funcionalidade permite expor URLs seguras utilizando **ULID**, **UUID** ou **UUID32**, sem substituir a chave primária do Eloquent.

:::tip Por que isso existe?

Expor IDs sequenciais em APIs facilita enumeração de registros.

Em vez de:

```
/users/15
```

é possível utilizar:

```
/users/01K2JP6D7N7YF5YJ9W3X1M8Q2R
```

:::

---

# Como funciona

A chave primária do banco continua sendo:

```php
id
```

O trait adiciona um identificador público.

| Campo | Uso |
|--------|-----|
| id | Banco de dados |
| hash | API pública |

---

# Habilitando

No Model:

```php
use Domain\DomainGenerator\Traits\HasHash;

class User extends Authenticatable
{
    use HasHash;
}
```

---

# Migration

Para ULID:

```php
$table->char('hash', 26)->unique();
```

Para UUID:

```php
$table->uuid('hash')->unique();
```

---

# Estratégias suportadas

A estratégia é configurada em:

```env
DOMAIN_GENERATOR_IDENTIFIER=ulid
```

## ULID

Exemplo:

```
01K2JP6D7N7YF5YJ9W3X1M8Q2R
```

Características:

- 26 caracteres
- ordenável
- recomendado

## UUID

Exemplo:

```
550e8400-e29b-41d4-a716-446655440000
```

Características:

- 36 caracteres
- padrão universal

## UUID32

Exemplo:

```
550e8400e29b41d4a716446655440000
```

Características:

- sem hífens
- menor

---

# Comparação

| Estratégia | Tamanho | Ordenável |
|------------|----------|-----------|
| ULID | 26 | Sim |
| UUID | 36 | Não |
| UUID32 | 32 | Não |

---

# Geração automática

Durante o `creating`, o trait gera automaticamente:

```php
$model->hash
```

Nenhuma configuração adicional é necessária.

---

# Route Model Binding

O trait altera automaticamente:

```php
getRouteKeyName()
```

Isso permite:

```
GET /api/users/01K2JP...
```

sem configuração manual.

---

# Buscando registros

O Repository detecta automaticamente o identificador.

```php
$this->repository->find(1);

$this->repository->find($hash);
```

Também funciona em:

- `findOrFail()`
- `delete()`
- `update()`

---

# Obtendo o identificador público

```php
$user->getPublicIdentifier();
```

Retorna:

```
01K2JP...
```

---

# Fluxo

<svg viewBox="0 0 260 220" width="100%" role="img" aria-label="Fluxo do HasHash">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">Model</text>
  <path d="M130 56 V84" stroke="currentColor"/>
  <rect x="50" y="84" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="107" text-anchor="middle" font-size="13">HasHash</text>
  <path d="M130 120 V148" stroke="currentColor"/>
  <rect x="50" y="148" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="171" text-anchor="middle" font-size="13">Hash Gerado</text>
</svg>

---

# Boas práticas

- Nunca substitua `id` pela coluna `hash`.
- Utilize `hash` apenas como identificador público.
- Prefira ULID para novos projetos.