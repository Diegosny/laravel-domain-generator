# CRUD Completo

<VersionBadge version="v1.1.0"/>

Crie um CRUD completo utilizando Laravel Domain Generator.

<Callout type="info">

Este exemplo demonstra todo o fluxo, desde a geração até uma API funcionando.

</Callout>

---

## O que será gerado?

- Model
- Migration
- Controller
- FormRequest
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

---

## Controller

```php
public function index()
{
    return $this->success(
        UserResource::collection(
            $this->service->paginate()
        )
    );
}
```

---

## Service

```php
public function create(UserDTO $dto)
{
    return $this->repository->create(
        $dto->toArray()
    );
}
```

---

## Repository

```php
public function model(): string
{
    return User::class;
}
```

---

## Criando um usuário

<ApiMethod method="POST"/> `/api/users`

Requisição:

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
    "public_id": "01JXYZABCDEF123456789",
    "nome": "João",
    "email": "joao@email.com"
  }
}
```

<Callout type="success">

O CRUD gerado já segue a arquitetura DDD completa.

</Callout>

---

## Fluxo

```text
Request
   │
   ▼
FormRequest
   │
   ▼
DTO
   │
   ▼
Service
   │
   ▼
Repository
   │
   ▼
Model
   │
   ▼
Resource
   │
   ▼
JSON
```