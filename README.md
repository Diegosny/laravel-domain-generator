# Laravel Domain Generator

[![Latest Stable Version](https://img.shields.io/badge/version-v1.1.0-blue.svg)](https://github.com/SEU_USUARIO/laravel-domain-generator)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4.svg?logo=php)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20.svg?logo=laravel)](https://laravel.com)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Automatize a criação de domínios seguindo **Domain Driven Design (DDD)** e **Clean Architecture**.

Com um único comando Artisan, o pacote gera **Controller, Request, DTO, Service, Repository, Model e Migration**, já integrados às classes abstratas da biblioteca.

---

# Índice

- Visão Geral
- Funcionalidades
- Instalação
- Criando um domínio
- Estrutura gerada
- Fluxo da arquitetura
- Classes abstratas
- DTOs
- API Resources
- Relacionamentos
- Filtros
- Identificadores Públicos (Hash)
- JWT
- Permissões
- Configuração
- Exemplo completo
- Roadmap
- Licença

---

# Visão Geral

O pacote padroniza projetos Laravel utilizando:

- DDD
- Clean Architecture
- DTOs
- Repository Pattern
- API Resources
- JWT
- Identificadores públicos (Hash)

## Fluxo da aplicação

```text
HTTP
 │
 ├── Controller
 ├── FormRequest
 └── Resource
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
```

---

# Funcionalidades

## Geração de Domínio

```bash
php artisan make:domain User
```

Gera:

- Model
- Migration
- Controller
- FormRequest
- DTO
- Service
- Repository

## Classes Abstratas

### AbstractController

- Respostas JSON padronizadas
- DTO automático
- API Resources
- Paginação
- Tratamento de exceções
- Relacionamentos (`with`)
- Permissões

### AbstractService

- `getAll()`
- `find()`
- `save()`
- `saveDto()`
- `update()`
- `updateDto()`
- `delete()`
- `create()`
- Hooks (`beforeSave`, `afterSave`, etc.)

### AbstractRepository

- Paginação
- Filtros
- Relacionamentos
- Busca por ID
- Busca por Hash
- `findOrFail()`
- `updateOrCreate()`
- `deleteWhere()`

---

# Instalação

```bash
composer require domain/laravel-domain-generator
```

O pacote instala automaticamente:

```text
php-open-source-saver/jwt-auth
```

---

# Criando um domínio

```bash
php artisan make:domain User
```

Forçar recriação:

```bash
php artisan make:domain User --force
```

## Estrutura gerada

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       │   └── UserDTO.php
│       ├── Repositories/
│       │   └── UserRepository.php
│       └── Service/
│           └── UserService.php
├── Http/
│   ├── Controllers/
│   │   └── UserController.php
│   └── Requests/
│       └── UserRequest.php
└── Models/
    └── User.php
```

---

# DTOs

```php
final class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {}
}
```

Controller:

```php
protected ?string $requestDto = UserDTO::class;
```

Fluxo:

```text
Request
 ↓
validated()
 ↓
UserDTO
 ↓
Service
 ↓
Repository
```

---

# API Resources

Criar:

```bash
php artisan make:resource UserResource
```

Exemplo:

```php
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'hash' => $this->hash,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
```

Controller:

```php
protected ?string $resource = UserResource::class;
```

Paginação preserva automaticamente os metadados.

---

# Relacionamentos

Buscar:

```http
GET /api/users?with=roles,permissions
```

Relacionamentos padrão:

```php
protected array $with = [
    'roles',
    'permissions',
];
```

O Repository valida automaticamente relacionamentos existentes.

---

# Filtros

```http
GET /api/users?name=John&status=active
```

Parâmetros ignorados:

- page
- per_page
- with
- sort
- order
- search

---

# Identificadores Públicos (Hash)

## HasHash

```php
use Domain\DomainGenerator\Traits\HasHash;

class User extends Authenticatable
{
    use HasHash;
}
```

### Recursos

- UUID automático
- `find($id)` e `find($hash)`
- Route Model Binding
- `getPublicIdentifier()`

### Migration

```php
$table->uuid('hash')->unique();
```

### Buscar

```php
$this->repository->find(1);

$this->repository->find('c9d47f58-20dd-41b3-b65e-f6ec5faef8d');
```

Também funciona para:

- `findOrFail()`
- `delete()`
- `update()`

### Rotas

```http
GET /api/users/c9d47f58-20dd-41b3-b65e-f6ec5faef8d
```

---

# JWT

Gerar chave:

```bash
php artisan jwt:secret
```

`config/auth.php`:

```php
'guards' => [
    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

Model:

```php
class User extends Authenticatable implements JWTSubject
{
    use HasJwtAuth;
    use HasHash;
}
```

## Endpoints

| Método | Endpoint |
|---------|----------|
| POST | `/api/auth/login` |
| GET | `/api/auth/me` |
| POST | `/api/auth/refresh` |
| POST | `/api/auth/logout` |

---

# Permissões

```php
$this->hasPermissionTo('users.create');
```

---

# Configuração

Padrão:

```env
APP_DOMAIN_FOLDER=Domain
```

Personalizado:

```env
APP_DOMAIN_FOLDER=Modules
```

---

# Exemplo Completo

## Controller

```php
class UserController extends AbstractController
{
    protected mixed $service;

    protected ?string $requestValidate = UserRequest::class;

    protected ?string $requestDto = UserDTO::class;

    protected ?string $resource = UserResource::class;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
}
```

## Repository

```php
class UserRepository extends AbstractRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
```

## Model

```php
class User extends Authenticatable implements JWTSubject
{
    use HasJwtAuth;
    use HasHash;

    protected $fillable = [
        'hash',
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'id',
        'password',
        'remember_token',
    ];
}
```

---

# Boas práticas

- Mantenha regras de negócio no Service.
- Use DTOs para transporte de dados.
- Utilize Repository para acesso ao banco.
- Exponha `hash` em APIs públicas.

---

# Roadmap

- [ ] Geração automática de API Resources
- [ ] HasHash automático no Model gerado
- [ ] Hash automático na Migration
- [ ] StoreDTO e UpdateDTO
- [ ] StoreRequest e UpdateRequest
- [ ] Policies
- [ ] Domain Events
- [ ] Testes gerados automaticamente

---

# Licença

MIT.