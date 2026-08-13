# Laravel Domain Generator

<p align="center">
    <img src="https://img.shields.io/badge/Laravel-Domain%20Generator-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel Domain Generator">
</p>

<p align="center">
    <strong>Automatize a criação de estruturas DDD e Clean Architecture no Laravel.</strong>
</p>

<p align="center">
    Gere Controllers, Services e Repositories completos com um único comando Artisan.
</p>

<p align="center">
    <img src="https://img.shields.io/badge/version-v1.0.14-blue.svg" alt="Version">
    <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4.svg?logo=php&logoColor=white" alt="PHP">
    <img src="https://img.shields.io/badge/Laravel-10%2B-FF2D20.svg?logo=laravel&logoColor=white" alt="Laravel">
    <img src="https://img.shields.io/badge/License-MIT-yellow.svg" alt="License">
</p>

---

## 📸 Visão Geral

O **Laravel Domain Generator** foi criado para reduzir o trabalho repetitivo na criação de novos domínios dentro de aplicações Laravel.

Com um único comando:

```bash
php artisan make:domain User
```

o pacote gera automaticamente a estrutura necessária para o domínio, incluindo:

```text
Controller
    ↓
Service
    ↓
Repository
    ↓
Model / Database
```

A ideia é manter cada camada com uma responsabilidade clara, utilizando injeção de dependência e uma estrutura consistente entre os diferentes domínios da aplicação.

---

## 🚀 Por que utilizar?

Em projetos maiores, criar manualmente Controllers, Services, Repositories e suas dependências pode se tornar repetitivo.

O Laravel Domain Generator automatiza esse processo.

### Sem o pacote

```text
Criar Controller
       ↓
Criar Service
       ↓
Criar Repository
       ↓
Configurar namespaces
       ↓
Configurar imports
       ↓
Configurar Dependency Injection
       ↓
Configurar herança das classes base
       ↓
Repetir tudo para o próximo domínio...
```

### Com o pacote

```bash
php artisan make:domain User
```

E pronto.

```text
Domain/
└── User/
    ├── Controllers/
    │   └── UserController.php
    │
    ├── Services/
    │   └── UserService.php
    │
    └── Repositories/
        └── UserRepository.php
```

---

# ✨ Funcionalidades

## 🚀 Geração automática de Domínios

Crie uma estrutura completa de domínio utilizando apenas um comando Artisan.

```bash
php artisan make:domain User
```

O objetivo é eliminar código repetitivo e manter um padrão consistente entre os módulos da aplicação.

---

## 🏗️ Arquitetura baseada em camadas

O pacote organiza o fluxo da aplicação utilizando uma separação clara de responsabilidades:

```text
┌───────────────────────┐
│       Controller      │
│                       │
│ HTTP / Request        │
│ Response              │
└───────────┬───────────┘
            │
            ▼
┌───────────────────────┐
│        Service        │
│                       │
│ Business Logic        │
│ Rules / Hooks         │
└───────────┬───────────┘
            │
            ▼
┌───────────────────────┐
│      Repository       │
│                       │
│ Data Access           │
│ Queries / Relations   │
└───────────┬───────────┘
            │
            ▼
┌───────────────────────┐
│    Model / Database   │
└───────────────────────┘
```

Essa abordagem facilita a manutenção e ajuda a manter Controllers mais enxutos, enquanto a lógica de negócio permanece concentrada na camada apropriada.

A arquitetura também aproveita recursos nativos do Laravel, como seu Service Container e injeção de dependência.

---

## 🎨 API Resources

O pacote possui suporte opcional para `JsonResource`.

Você pode definir um Resource diretamente no Controller:

```php
protected ?string $resource = UserResource::class;
```

Isso permite personalizar a estrutura das respostas da API sem precisar alterar a lógica principal do CRUD.

---

## 🔐 Autenticação JWT

O pacote possui suporte integrado para autenticação utilizando JWT.

São disponibilizados endpoints para:

* Login
* Logout
* Refresh Token
* Usuário autenticado

Sem precisar implementar toda a lógica de autenticação novamente em cada projeto.

---

## 🧩 Classes Abstratas

O pacote fornece classes base para padronizar o comportamento das diferentes camadas.

### `AbstractController`

Responsável pela padronização da camada HTTP.

Possui suporte para:

* Respostas JSON padronizadas.
* `ok`.
* `success`.
* `error`.
* API Resources.
* Form Requests.
* Tratamento de exceções.
* Integração com Services.

---

### `AbstractService`

Responsável pela camada de negócio.

Possui métodos CRUD e permite utilizar hooks para personalizar o comportamento das operações.

Exemplos:

```text
beforeSave
afterSave

beforeUpdate
afterUpdate

beforeDelete
afterDelete
```

Isso permite adicionar regras específicas sem precisar modificar a estrutura principal do CRUD.

---

### `AbstractRepository`

Responsável pela abstração da camada de dados.

Possui suporte para:

* CRUD.
* Paginação.
* Relacionamentos.
* Consultas.
* Acesso aos Models.
* Abstração da persistência.

---

# 📦 Instalação

Instale o pacote utilizando o Composer:

```bash
composer require domain/laravel-domain-generator
```

O pacote instalará automaticamente as dependências necessárias para autenticação JWT.

```text
php-open-source-saver/jwt-auth
```

---

# ⚡ Quick Start

Depois da instalação, basta executar:

```bash
php artisan make:domain User
```

O pacote criará a estrutura do domínio.

```text
Domain/
└── User/
    ├── Controllers/
    │   └── UserController.php
    ├── Services/
    │   └── UserService.php
    └── Repositories/
        └── UserRepository.php
```

A partir daí, você pode começar a implementar as regras específicas do domínio.

---

# 🔑 Configuração JWT

## 1. Gere a chave secreta

Execute:

```bash
php artisan jwt:secret
```

---

## 2. Configure o Guard

No arquivo:

```text
config/auth.php
```

Configure o guard `api`:

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

## 3. Configure o Model User

No seu:

```text
app/Models/User.php
```

Adicione `JWTSubject` e `HasJwtAuth`:

```php
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

O pacote disponibiliza automaticamente as seguintes rotas:

| Método | Endpoint            | Protegido | Descrição                           |
| ------ | ------------------- | :-------: | ----------------------------------- |
| `POST` | `/api/auth/login`   |     ❌     | Realiza login e retorna o token JWT |
| `GET`  | `/api/auth/me`      |     ✅     | Retorna o usuário autenticado       |
| `POST` | `/api/auth/refresh` |     ✅     | Renova o token de acesso            |
| `POST` | `/api/auth/logout`  |     ✅     | Invalida o token atual              |

---

# 🎯 API Resources

Caso você queira controlar exatamente como os dados serão retornados pela API, utilize um `JsonResource`.

## 1. Crie o Resource

```bash
php artisan make:resource UserResource
```

Será criado:

```text
app/Http/Resources/UserResource.php
```

---

## 2. Configure o Controller

```php
namespace Domain\User\Controllers;

use App\Http\Resources\UserResource;
use Domain\DomainGenerator\Abstracts\AbstractController;
use Domain\User\Services\UserService;

class UserController extends AbstractController
{
    protected ?string $resource = UserResource::class;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
}
```

Agora as respostas do CRUD poderão utilizar automaticamente o Resource configurado.

---

## 💡 Resource opcional

O uso de Resources não é obrigatório.

Caso você não defina um Resource:

```php
protected ?string $resource = null;
```

o Controller continuará utilizando a estrutura padrão de resposta fornecida pelo pacote.

---

# 🔄 Hooks

Uma das principais funcionalidades do `AbstractService` são os hooks.

Eles permitem executar comportamentos antes e depois das operações.

### Exemplo

```php
protected function beforeSave(array $data): array
{
    // Manipular os dados antes de salvar.

    return $data;
}
```

Outro exemplo:

```php
protected function afterSave($model)
{
    // Executar alguma ação após salvar.

    return $model;
}
```

Isso permite personalizar o comportamento dos Services sem precisar reimplementar toda a lógica CRUD.

---

# 🧱 Dependency Injection

Os componentes gerados utilizam injeção de dependência através do construtor.

Exemplo:

```php
public function __construct(UserService $service)
{
    $this->service = $service;
}
```

Isso facilita:

* Testes automatizados.
* Substituição de implementações.
* Manutenção.
* Organização das dependências.
* Baixo acoplamento.

A injeção de dependência é um recurso central do Service Container do Laravel.

---

# 📁 Estrutura do Projeto

Uma aplicação utilizando o pacote pode ficar organizada desta forma:

```text
app/
├── Http/
│   └── Resources/
│       └── UserResource.php
│
└── Models/
    └── User.php

Domain/
├── DomainGenerator/
│   ├── Abstracts/
│   │   ├── AbstractController.php
│   │   ├── AbstractService.php
│   │   └── AbstractRepository.php
│   │
│   └── Traits/
│       └── HasJwtAuth.php
│
└── User/
    ├── Controllers/
    │   └── UserController.php
    │
    ├── Services/
    │   └── UserService.php
    │
    └── Repositories/
        └── UserRepository.php
```

---

# 🧠 Como pensar na arquitetura

Uma forma simples de entender a responsabilidade de cada camada:

```text
┌─────────────────────────────────────────────┐
│                  CLIENT                     │
│                                             │
│       Web / Mobile / Postman / API          │
└──────────────────────┬──────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────┐
│                CONTROLLER                   │
│                                             │
│ Request → Validation → Response             │
└──────────────────────┬──────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────┐
│                  SERVICE                    │
│                                             │
│ Business Rules → Hooks → Operations        │
└──────────────────────┬──────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────┐
│                REPOSITORY                   │
│                                             │
│ Queries → Pagination → Relationships       │
└──────────────────────┬──────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────┐
│              DATABASE / MODEL               │
└─────────────────────────────────────────────┘
```

---

# 🆚 Antes vs. Depois

### ❌ Sem o Domain Generator

```text
Novo domínio
    ↓
Criar Controller
    ↓
Criar Service
    ↓
Criar Repository
    ↓
Configurar imports
    ↓
Configurar namespaces
    ↓
Configurar constructor
    ↓
Configurar herança
    ↓
Repetir...
```

### ✅ Com o Domain Generator

```bash
php artisan make:domain Order
```

```text
OrderController
OrderService
OrderRepository
```

Tudo criado e estruturado automaticamente.

---

# 🛠️ Exemplo Prático

Imagine que você esteja iniciando um domínio de pedidos.

Execute:

```bash
php artisan make:domain Order
```

O resultado será semelhante a:

```text
Domain/
└── Order/
    ├── Controllers/
    │   └── OrderController.php
    │
    ├── Services/
    │   └── OrderService.php
    │
    └── Repositories/
        └── OrderRepository.php
```

O Controller recebe a requisição:

```text
HTTP Request
     ↓
OrderController
```

O Service executa as regras:

```text
OrderController
     ↓
OrderService
```

O Repository cuida do acesso aos dados:

```text
OrderService
     ↓
OrderRepository
```

E finalmente:

```text
OrderRepository
     ↓
Order Model
     ↓
Database
```

---

# 📋 Requisitos

Antes de instalar o pacote, certifique-se de que sua aplicação atende às versões suportadas pelo pacote.

Recomenda-se verificar o `composer.json` para consultar as versões exatas de:

* PHP
* Laravel
* Composer

---

# 🧪 Filosofia do Projeto

O Laravel Domain Generator não tem como objetivo substituir o Laravel.

A proposta é **automatizar padrões repetitivos** e fornecer uma base consistente para projetos que desejam trabalhar com:

* Domain-Driven Design.
* Clean Architecture.
* Service Layer.
* Repository Pattern.
* Dependency Injection.
* API Resources.
* JWT Authentication.
* CRUD padronizado.

A estrutura também busca preservar a flexibilidade do Laravel, permitindo que cada domínio evolua conforme as necessidades do projeto.

---

# ⭐ Benefícios

| Benefício            | Descrição                           |
| -------------------- | ----------------------------------- |
| 🚀 Produtividade     | Menos código repetitivo             |
| 🧱 Organização       | Domínios separados e estruturados   |
| 🧠 Manutenibilidade  | Responsabilidades bem definidas     |
| 🔌 Baixo acoplamento | Uso de Dependency Injection         |
| 🔐 JWT               | Autenticação pronta                 |
| 🎨 Resources         | Respostas personalizáveis           |
| ♻️ Reutilização      | Classes abstratas compartilhadas    |
| 🧪 Testabilidade     | Estrutura preparada para testes     |
| ⚡ Artisan            | Geração através de comandos simples |

---

# 📚 Referências

* [Laravel Documentation](https://laravel.com/docs)
* [Laravel Controllers](https://laravel.com/docs/13.x/controllers)
* [Laravel Service Container](https://laravel.com/docs/13.x/container)
* [Laravel API Resources](https://laravel.com/docs/13.x/eloquent-resources)

---

# 🤝 Contribuindo

Contribuições são muito bem-vindas!

Para contribuir:

1. Faça um fork do projeto.
2. Crie uma branch para sua alteração.
3. Implemente suas modificações.
4. Execute os testes.
5. Faça um commit.
6. Abra um Pull Request.

---

# 🐛 Reportando problemas

Encontrou um bug?

Abra uma **Issue** descrevendo:

* O comportamento esperado.
* O comportamento atual.
* Versão do PHP.
* Versão do Laravel.
* Versão do pacote.
* Passos para reproduzir o problema.

Quanto mais informações forem fornecidas, mais fácil será investigar o problema.

---

# ⭐ Apoie o projeto

Se o **Laravel Domain Generator** ajudou no seu projeto, considere deixar uma ⭐ no GitHub.

Isso ajuda o projeto a ganhar visibilidade e incentiva novas melhorias.

---

# 📄 Licença

Este projeto está licenciado sob a licença **MIT**.

Consulte o arquivo `LICENSE` para obter mais informações.

---

# 👨‍💻 Autor

Desenvolvido para facilitar a implementação de **DDD**, **Clean Architecture**, **Service Layer** e **Repository Pattern** em aplicações Laravel.

<p align="center">
    <strong>Laravel Domain Generator</strong>
    <br>
    <sub>Generate. Organize. Scale.</sub>
</p>
