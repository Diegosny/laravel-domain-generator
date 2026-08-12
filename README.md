# Laravel Domain Generator

[![Latest Stable Version](https://img.shields.io/badge/version-v1.0.14-blue.svg)](https://github.com/SEU_USUARIO/laravel-domain-generator)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

O **Laravel Domain Generator** é um pacote para Laravel desenvolvido para automatizar e padronizar a criação de estruturas de **Domain Driven Design (DDD)** / **Clean Architecture**.

Com um único comando Artisan, o pacote gera os arquivos de **Controller**, **Service** e **Repository** totalmente configurados com herança automática das classes base do pacote (`AbstractController`, `AbstractService` e `AbstractRepository`).

---

## 💡 Funcionalidades

- 🚀 **Geração de Domínio Completa:** Cria estrutura modular para seu novo domínio de forma instantânea.
- 🔐 **Autenticação JWT Pronta:** Suporte nativo a endpoints de login, logout, refresh e me sem necessidade de recriar a lógica de autenticação.
- 🏗️ **Classes Abstratas Robustas:**
  - `AbstractController`: Padronização de respostas JSON (`ok`, `success`, `error`), validação automática com FormRequests e manipulação global de exceções.
  - `AbstractService`: Métodos nativos de CRUD com *hooks* (`beforeSave`, `afterSave`, `beforeUpdate`, `afterUpdate`, etc.).
  - `AbstractRepository`: Abstração de camada de dados com suporte a paginação e relacionamentos.
- 🧱 **Boas Práticas & Clean Code:** Injeção de dependência via construtor gerada automaticamente em todas as camadas.

---

## 📦 Instalação

Instale o pacote via Composer:

```bash
composer require domain/laravel-domain-generator
```

O pacote instalará automaticamente as dependências do JWT (`php-open-source-saver/jwt-auth`).

---

## 🔑 Configuração Rápida da Autenticação JWT

Para utilizar os endpoints de autenticação prontos, faça as seguintes etapas no seu projeto:

### 1. Gere a Chave Secreta do JWT

No terminal da aplicação, execute:

```bash
php artisan jwt:secret
```

### 2. Configure o Guard da API

No arquivo `config/auth.php`, defina o driver do guard `api` para `jwt`:

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

### 3. Atualize o Model `User`

Adicione a interface `JWTSubject` e a Trait `HasJwtAuth` ao seu modelo de usuário (`App\Models\User.php`):

```php
namespace App\Models;

use Domain\DomainGenerator\Traits\HasJwtAuth;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasJwtAuth;

    // ... restante do seu model
}
```

---

## 🌐 Endpoints de Autenticação Disponíveis

O pacote disponibiliza automaticamente as seguintes rotas prontas sob o prefixo `/api/auth`:

| Método | Endpoint          | Protegido | Descrição                             |
| :---   | :---              | :---:     | :---                                  |
| `POST` | `/api/auth/login`   | ❌        | Realiza login e retorna o token JWT   |
| `GET`  | `/api/auth/me`      |  Sim      | Retorna os dados do usuário autenticado |
| `POST` | `/api/auth/refresh` |  Sim      | Renova o token de acesso              |
| `POST` | `/api/auth/logout`  |  Sim      | Invalida o token atual                |

---

## 🛠️ Utilização

Para gerar um novo domínio completo (Controller, Service e Repository):

```bash
php artisan make:domain User
```