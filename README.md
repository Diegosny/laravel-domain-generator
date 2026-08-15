# Laravel Domain Generator

[![Latest Stable Version](https://img.shields.io/badge/version-v1.1.0-blue.svg)](https://github.com/SEU_USUARIO/laravel-domain-generator)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

O **Laravel Domain Generator** é um pacote para Laravel desenvolvido para automatizar e padronizar estruturas baseadas em **DDD** e **Clean Architecture**.

> Esta documentação corresponde à versão **v1.1.0**.

## Funcionalidades

- Geração automática de Domain (Controller, Request, DTO, Service, Repository, Model e Migration)
- DTOs integrados ao AbstractController
- API Resources
- Paginação
- Relacionamentos (`with`)
- Respostas JSON padronizadas
- Autenticação JWT
- Suporte nativo a identificadores públicos (`hash`)
- Busca por ID ou Hash

## Instalação

```bash
composer require domain/laravel-domain-generator
```

## Criando um domínio

```bash
php artisan make:domain User
```

## Estrutura gerada

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       ├── Repositories/
│       └── Service/
├── Http/
│   ├── Controllers/
│   └── Requests/
└── Models/
```

## Identificadores Públicos (Hash)

Utilize o trait:

```php
use Domain\DomainGenerator\Traits\HasHash;

class User extends Authenticatable
{
    use HasHash;
}
```

O trait fornece:

- UUID automático
- `find($id)` e `find($hash)`
- Route Model Binding por `hash`
- `getPublicIdentifier()`

### Migration

```php
$table->uuid('hash')->unique();
```

### Busca por hash

```php
$this->repository->find('c9d47f58-20dd-41b3-b65e-f6ec5faef8d');
```

## Relacionamentos

```http
GET /api/users?with=roles,permissions
```

Relacionamentos inexistentes são ignorados automaticamente.

## JWT

```bash
php artisan jwt:secret
```

## Roadmap

- [ ] API Resources automáticos
- [ ] HasHash automático no Model
- [ ] Coluna hash automática na Migration
- [ ] StoreDTO e UpdateDTO
- [ ] StoreRequest e UpdateRequest
- [ ] Policies
- [ ] Domain Events

## Licença

MIT.