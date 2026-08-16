# Identificadores Públicos

<VersionBadge version="v1.1.0"/>

A biblioteca possui suporte nativo para identificadores públicos.

Em vez de expor IDs incrementais do banco, os Models podem utilizar identificadores seguros.

---

## Formatos Suportados

| Identificador | Exemplo |
|---------------|---------|
| ULID | `01JXYZABCDEF1234567890` |
| UUID | `550e8400-e29b-41d4-a716-446655440000` |
| UUID32 | `550e8400e29b41d4a716446655440000` |

---

## Por que utilizar?

- Evita enumeração de IDs
- APIs mais limpas
- URLs mais seguras
- Melhor integração com frontend

Exemplo:

```text
/api/users/01JXYZABCDEF1234567890
```

em vez de

```text
/api/users/12
```

---

## Trait HasHash

Os Models gerados incluem:

```php
use HasHash;
```

A Trait gerencia automaticamente o identificador público.

---

## Configuração

Escolha o formato desejado em:

```text
config/domain-generator.php
```

<Callout type="success">

Os identificadores funcionam automaticamente com Repositories e Resources.

</Callout>