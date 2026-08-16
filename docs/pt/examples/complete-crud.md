# Exemplo de CRUD Completo

<VersionBadge version="v1.1.0"/>

Este guia demonstra o ciclo completo de um domínio gerado, desde o comando Artisan até uma API REST pronta para produção.

<Callout type="info">

Este é o melhor ponto de partida para entender a estrutura gerada pela biblioteca.

</Callout>

---

## O que será gerado

Um único comando cria:

- Model
- Migration
- Controller
- Form Requests
- DTO
- Service
- Repository
- Resource
- Rotas

---

## Gerando o domínio

```bash
php artisan make:domain User
```

---

## Estrutura criada

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       ├── Repositories/
│       └── Service/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
└── Models/
```

Cada camada possui uma única responsabilidade.

---

## Fluxo interno

<svg viewBox="0 0 760 180" width="100%" role="img" aria-label="Fluxo do CRUD">
  <rect x="20" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="65" y="84" text-anchor="middle" font-size="14">Request</text>
  <path d="M110 80 L165 80" stroke="currentColor" stroke-width="2"/>
  <path d="M165 80 l-8 -6 M165 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="165" y="55" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="220" y="84" text-anchor="middle" font-size="14">FormRequest</text>
  <path d="M275 80 L340 80" stroke="currentColor" stroke-width="2"/>
  <path d="M340 80 l-8 -6 M340 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="340" y="55" width="70" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="375" y="84" text-anchor="middle" font-size="14">DTO</text>
  <path d="M410 80 L470 80" stroke="currentColor" stroke-width="2"/>
  <path d="M470 80 l-8 -6 M470 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="470" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="515" y="84" text-anchor="middle" font-size="14">Service</text>
  <path d="M560 80 L620 80" stroke="currentColor" stroke-width="2"/>
  <path d="M620 80 l-8 -6 M620 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="620" y="55" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="675" y="84" text-anchor="middle" font-size="14">Repository</text>
</svg>

O fluxo permanece igual em todos os domínios gerados.

---

## Criando um usuário

<ApiMethod method="POST"/> `/api/users`

```json
{
  "nome": "João",
  "email": "joao@email.com",
  "password": "12345678"
}
```

Resposta:

```json
{
  "type": "success",
  "status": 201,
  "data": {
    "public_id": "01JXYZABCDEF123456789"
  }
}
```

---

## Listando usuários

<ApiMethod method="GET"/> `/api/users`

A paginação já vem pronta.

---

## Atualizando

<ApiMethod method="PUT"/> `/api/users/{public_id}`

Utiliza automaticamente `UpdateRequest` e `UpdateDTO`.

---

## Removendo

<ApiMethod method="DELETE"/> `/api/users/{public_id}`

Quando SoftDeletes estiver ativo, a remoção será lógica.

<Callout type="success">

O CRUD gerado já segue integralmente DDD e Clean Architecture.

</Callout>