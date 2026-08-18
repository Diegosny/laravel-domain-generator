# Smart Public IDs

<VersionBadge version="v1.2.0"/>

O Laravel Domain Generator gera automaticamente identificadores públicos legíveis para todas as Models que utilizam o trait `HasHash`.

Em vez de expor IDs sequenciais ou UUIDs longos nas rotas, a biblioteca cria identificadores curtos, seguros e fáceis de reconhecer.

<Callout type="success">

Os Smart Public IDs são gerados automaticamente. Nenhuma lógica adicional precisa ser implementada na Model.

</Callout>

---

# Como funciona

Internamente, o banco continua utilizando o `id` como chave primária.

Já a API trabalha utilizando o campo `hash`.

| Campo | Finalidade |
|-------|------------|
| `id` | Chave primária interna |
| `hash` | Identificador público da API |

Exemplo salvo no banco.

| id | hash |
|---|---|
| `1` | `PAT_K7XM4Q2R` |
| `2` | `PAT_J8L4M7XP` |

---

# Prefixo automático

O prefixo é gerado dinamicamente a partir do nome da Model.

| Model | Hash |
|--------|------|
| `Patient` | `PAT_K7XM4Q2R` |
| `Product` | `PRO_J8N4W6XM` |
| `Organization` | `ORG_A7KM9Q2R` |
| `MedicalRecord` | `MRE_R6X3K8QP` |
| `UserSessionToken` | `UST_T9L2P7WK` |

Regras utilizadas:

- Models simples usam as três primeiras letras.
- Models compostas utilizam um acrônimo inteligente.
- O código aleatório utiliza um alfabeto sem caracteres ambíguos (`O`, `0`, `I` e `1`).

---

# Configurando a Migration

Após gerar um domínio, edite a migration antes de executá-la.

A estrutura recomendada é:

```php
Schema::create('patients', function (Blueprint $table) {

    $table->id();

    // Smart Public ID
    $table->string('hash', 20)
        ->unique()
        ->index();

    $table->timestamps();
    $table->softDeletes();
});
```

### Por que `20` caracteres?

O formato atual ocupa aproximadamente 12 caracteres.

```text
PAT_K7XM4Q2R
```

Utilizar `20` deixa margem para futuras alterações sem necessidade de novas migrations.

---

# Model gerada automaticamente

A biblioteca gera uma Model semelhante a esta.

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

Nenhuma configuração adicional é necessária.

---

# Rotas automaticamente protegidas

O trait `HasHash` altera automaticamente o Route Model Binding.

Isso significa que a API passa a utilizar o `hash` em vez do `id`.

```text
GET /api/patients/PAT_K7XM4Q2R
```

Internamente o Laravel continua resolvendo a Model corretamente.

---

# Criando um registro

<ApiMethod method="POST"/> `/api/patients`

Request.

```json
{
  "name": "João da Silva",
  "cpf": "12345678901"
}
```

Resposta.

```json
{
  "type": "success",
  "status": 201,
  "data": {
    "hash": "PAT_K7XM4Q2R",
    "name": "João da Silva"
  }
}
```

Observe que o `id` não é exposto.

---

# Listando registros

<ApiMethod method="GET"/> `/api/patients`

Resposta.

```json
{
  "data": [
    {
      "hash": "PAT_K7XM4Q2R",
      "name": "João da Silva"
    },
    {
      "hash": "PAT_J8L4M7XP",
      "name": "Maria Oliveira"
    }
  ]
}
```

A API trabalha apenas com identificadores públicos.

---

# Buscando um registro

<ApiMethod method="GET"/> `/api/patients/PAT_K7XM4Q2R`

Resposta.

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "hash": "PAT_K7XM4Q2R",
    "name": "João da Silva"
  }
}
```

---

# Atualizando um registro

<ApiMethod method="PUT"/> `/api/patients/PAT_K7XM4Q2R`

Request.

```json
{
  "phone": "11999999999"
}
```

Resposta.

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

# Removendo um registro

<ApiMethod method="DELETE"/> `/api/patients/PAT_K7XM4Q2R`

Resposta.

```json
{
  "type": "success",
  "status": 200,
  "message": "Registro removido com sucesso."
}
```

---

# Como funciona internamente

Durante a criação do registro, o trait `HasHash` executa automaticamente:

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
Banco de Dados
```

O código gerado possui bilhões de combinações possíveis e a coluna `hash` permanece protegida por uma constraint `UNIQUE`, eliminando colisões na prática.

<Callout type="success">

Os Smart Public IDs tornam a API mais legível, evitam expor IDs internos e oferecem uma experiência semelhante a plataformas como Stripe e GitHub.

</Callout>