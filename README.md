# Laravel Domain Generator

[![Latest Stable Version](https://img.shields.io/badge/version-v1.0.14-blue.svg)](https://github.com/SEU_USUARIO/laravel-domain-generator)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

O **Laravel Domain Generator** é um pacote para Laravel desenvolvido para automatizar e padronizar a criação de estruturas baseadas em **Domain Driven Design (DDD)** e **Clean Architecture**.

Com um único comando Artisan, o pacote gera a estrutura inicial de um domínio, incluindo **Controller, FormRequest, DTO, Service, Repository, Model e Migration**, já integrados às classes abstratas fornecidas pelo pacote.

A biblioteca também fornece suporte para **API Resources**, paginação, relacionamentos, respostas JSON padronizadas, autenticação JWT e hooks de ciclo de vida no Service.

---

## 💡 Funcionalidades

### 🚀 Geração de Domínio

Crie a estrutura inicial de um domínio utilizando um único comando:

```bash
php artisan make:domain User
```

O comando pode gerar:

- Model
- Migration
- Controller
- FormRequest
- DTO
- Service
- Repository

---

### 🧱 Arquitetura baseada em DDD / Clean Architecture

A estrutura gerada separa as principais responsabilidades da aplicação:

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
   Application
       │
     Service
       │
       ▼
  Repository
       │
       ▼
     Model
```

Essa separação permite manter a camada HTTP desacoplada das regras de persistência e facilita a evolução da aplicação.

---

### 📦 DTOs

O pacote possui um `AbstractDTO` para criação de Data Transfer Objects.

Exemplo:

```php
<?php

namespace App\Domain\User\DTO;

use Domain\DomainGenerator\Abstracts\AbstractDTO;

final class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone = null,
    ) {
    }
}
```

O DTO pode ser configurado diretamente no Controller:

```php
protected ?string $requestDto = UserDTO::class;
```

O fluxo de criação passa a ser:

```text
Request
   ↓
validated()
   ↓
UserDTO
   ↓
UserService
   ↓
UserRepository
```

O pacote mantém compatibilidade com Controllers que não utilizam DTOs.

---

### 🔐 Autenticação JWT

O pacote possui suporte integrado à autenticação JWT utilizando:

`php-open-source-saver/jwt-auth`

São disponibilizados endpoints para:

- Login
- Logout
- Refresh
- Usuário autenticado (`me`)

---

### 🏗️ Classes Abstratas

O pacote fornece classes base para padronizar a arquitetura.

#### AbstractController

Responsável por:

- Respostas JSON padronizadas
- `ok()`
- `success()`
- `error()`
- Integração com FormRequests
- Integração com DTOs
- Integração com API Resources
- Paginação
- Tratamento de exceções
- Controle de permissões
- Relacionamentos através de `with`

#### AbstractService

Fornece operações comuns de aplicação:

- `getAll()`
- `find()`
- `save()`
- `saveDto()`
- `update()`
- `updateDto()`
- `delete()`
- `create()`
- `createDto()`
- `updateOrCreate()`
- `updateOrCreateDto()`
- `preRequisite()`
- `toSelect()`

Também disponibiliza hooks:

```text
beforeSave
afterSave

beforeUpdate
afterUpdate

beforeDelete
afterDelete
```

#### AbstractRepository

Fornece uma abstração sobre o Eloquent com suporte a:

- Paginação
- Filtros
- Relacionamentos
- Busca por ID
- Busca por campo alternativo
- `findOrFail`
- `where`
- `findOneWhere`
- `create`
- `update`
- `delete`
- `deleteWhere`
- `updateOrCreate`
- `pluck`

---

## 📦 Instalação

Instale o pacote via Composer:

```bash
composer require domain/laravel-domain-generator
```

O pacote instalará automaticamente as dependências necessárias do JWT:

```text
php-open-source-saver/jwt-auth
```

---

# 🏗️ Criando um domínio

Para criar um novo domínio:

```bash
php artisan make:domain User
```

Para forçar a recriação de arquivos existentes:

```bash
php artisan make:domain User --force
```

A estrutura gerada será semelhante a:

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       │   └── UserDTO.php
│       │
│       ├── Repositories/
│       │   └── UserRepository.php
│       │
│       └── Service/
│           └── UserService.php
│
├── Http/
│   ├── Controllers/
│   │   └── UserController.php
│   │
│   └── Requests/
│       └── UserRequest.php
│
└── Models/
    └── User.php
```

> API Resources são criados manualmente em `app/Http/Resources`, pois pertencem à camada HTTP da aplicação e não à camada de domínio.

---

# 🔄 Fluxo de uma requisição

Com DTO configurado, uma requisição de criação seguirá aproximadamente este fluxo:

```text
HTTP Request
     │
     ▼
UserRequest
     │
     │ validated()
     ▼
UserDTO
     │
     ▼
UserController
     │
     ▼
UserService
     │
     ▼
UserRepository
     │
     ▼
User Model
     │
     ▼
UserResource
     │
     ▼
JSON Response
```

---

# 📝 FormRequest

O Request gerado pode ser utilizado normalmente para validação:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],
        ];
    }
}
```

---

# 📦 DTO

Depois de validar os dados, o Controller pode convertê-los automaticamente para um DTO.

```php
<?php

namespace App\Domain\User\DTO;

use Domain\DomainGenerator\Abstracts\AbstractDTO;

final class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone = null,
    ) {
    }
}
```

No Controller:

```php
protected ?string $requestValidate = UserRequest::class;

protected ?string $requestDto = UserDTO::class;
```

O `AbstractController` fará automaticamente:

```text
$validated
    ↓
UserDTO::fromArray()
```

O Service recebe o DTO através de:

```php
$this->service->saveDto($dto);
```

Internamente, o DTO é convertido para array antes de chegar ao Repository.

---

# 🧩 API Resources

Os API Resources devem ficar na camada HTTP:

```text
app/
└── Http/
    └── Resources/
        └── UserResource.php
```

Para criar um Resource:

```bash
php artisan make:resource UserResource
```

Exemplo:

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ];
    }
}
```

No Controller:

```php
use App\Http\Resources\UserResource;

class UserController extends AbstractController
{
    protected ?string $resource = UserResource::class;
}
```

A partir desse momento, o `AbstractController` utilizará automaticamente o Resource nas respostas.

---

## 📄 Resources com Collection

Para respostas de listas, o `AbstractController` também suporta Resources:

```php
protected ?string $resource = UserResource::class;
```

O resultado será transformado automaticamente através de:

```php
UserResource::collection($items)
```

---

## 📑 Resources com Paginação

Paginação também é suportada:

```php
public function index(Request $request): JsonResponse
{
    return $this->handle(
        fn () => $this->service->getAll(
            $request->all(),
            $this->resolveWith($request)
        )
    );
}
```

Quando o Service retornar um `LengthAwarePaginator`, o `AbstractController` utilizará o Resource configurado e preservará os metadados de paginação.

---

# 🔗 Relacionamentos

É possível solicitar relacionamentos através do parâmetro `with`.

Por exemplo:

```http
GET /api/users?with=roles,permissions
```

Ou definir relacionamentos padrão no Controller:

```php
protected array $with = [
    'roles',
    'permissions',
];
```

O `AbstractRepository` normaliza automaticamente os relacionamentos recebidos.

---

# 🔎 Filtros

Os filtros podem ser enviados diretamente na requisição:

```http
GET /api/users?name=John&status=active
```

O `AbstractRepository` remove automaticamente parâmetros técnicos como:

```text
page
per_page
with
sort
order
search
```

antes de aplicar os filtros à consulta.

---

# 🔑 Configuração Rápida da Autenticação JWT

Para utilizar os endpoints de autenticação prontos, faça as seguintes etapas.

## 1. Gere a chave secreta

```bash
php artisan jwt:secret
```

---

## 2. Configure o Guard da API

No arquivo:

```text
config/auth.php
```

configure o guard:

```php
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],

    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],
],
```

---

## 3. Atualize o Model User

Adicione `JWTSubject` e `HasJwtAuth`:

```php
<?php

namespace App\Models;

use Domain\DomainGenerator\Traits\HasJwtAuth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasJwtAuth;

    // ...
}
```

---

# 🌐 Endpoints de Autenticação

O pacote disponibiliza rotas de autenticação sob o prefixo:

```text
/api/auth
```

| Método | Endpoint | Protegido | Descrição |
|---|---|:---:|---|
| POST | `/api/auth/login` | ❌ | Realiza login e retorna o token JWT |
| GET | `/api/auth/me` | ✅ | Retorna o usuário autenticado |
| POST | `/api/auth/refresh` | ✅ | Renova o token |
| POST | `/api/auth/logout` | ✅ | Invalida o token atual |

---

# 🔐 Permissões

O `AbstractController` possui suporte à verificação de permissões:

```php
$this->hasPermissionTo('users.create');
```

Caso o usuário não possua a permissão necessária, será lançada uma exceção de autorização.

---

# ⚙️ Configuração do diretório de Domínios

Por padrão, os domínios são criados dentro de:

```text
app/Domain
```

É possível alterar esse comportamento através da variável:

```env
APP_DOMAIN_FOLDER=Domain
```

Por exemplo:

```env
APP_DOMAIN_FOLDER=Modules
```

Nesse caso:

```text
app/Modules/User/
```

será utilizado como diretório do domínio.

---

# 🧪 Exemplo completo

Uma implementação básica pode ficar assim:

### Controller

```php
<?php

namespace App\Http\Controllers;

use App\Domain\User\DTO\UserDTO;
use App\Domain\User\Service\UserService;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use Domain\DomainGenerator\Abstracts\AbstractController;

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

### DTO

```php
<?php

namespace App\Domain\User\DTO;

use Domain\DomainGenerator\Abstracts\AbstractDTO;

final class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
    ) {
    }
}
```

### Service

```php
<?php

namespace App\Domain\User\Service;

use App\Domain\User\Repositories\UserRepository;
use Domain\DomainGenerator\Abstracts\AbstractService;

class UserService extends AbstractService
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
}
```

### Repository

```php
<?php

namespace App\Domain\User\Repositories;

use App\Models\User;
use Domain\DomainGenerator\Abstracts\AbstractRepository;

class UserRepository extends AbstractRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
```

### Resource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
```

---

# 🧠 Princípios da arquitetura

A biblioteca busca manter as responsabilidades separadas:

| Camada | Responsabilidade |
|---|---|
| Controller | Entrada HTTP e resposta |
| FormRequest | Validação da entrada |
| DTO | Transporte dos dados |
| Service | Regras/processos da aplicação |
| Repository | Persistência e consultas |
| Model | Representação Eloquent |
| Resource | Transformação da resposta HTTP |

Uma regra importante é evitar colocar dependências HTTP dentro do domínio.

Por isso:

```text
DTO
Service
Repository
```

ficam dentro do domínio, enquanto:

```text
Controller
Request
Resource
```

ficam na camada HTTP.

---

# 🚀 Roadmap

Possíveis evoluções futuras:

- [ ] Geração automática de API Resources
- [ ] Geração de DTOs baseada no `$fillable` do Model
- [ ] DTOs específicos para Store e Update
- [ ] Conversão automática de tipos nos DTOs
- [ ] Value Objects
- [ ] Domain Events
- [ ] Repository Interfaces
- [ ] Use Cases / Application Services
- [ ] Policies
- [ ] Testes automatizados gerados pelo comando
- [ ] Suporte a diferentes estratégias de persistência

---

# 📄 Licença

Este projeto está licenciado sob a licença MIT.