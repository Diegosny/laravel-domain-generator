# Primeiros Passos

Bem-vindo ao **Laravel Domain Generator**.

Este guia apresenta a forma mais rápida de começar a utilizar a biblioteca.

## Requisitos

| Requisito | Versão |
|------------|---------|
| PHP | 8.2+ |
| Laravel | 11, 12 ou 13 |
| Composer | 2+ |

## Instalação

Execute:

```bash
composer require domain/laravel-domain-generator
```

## Gerando a chave JWT

```bash
php artisan jwt:secret
```

## Criando o primeiro domínio

```bash
php artisan make:domain User
```

O comando criará automaticamente:

- Model
- Migration
- Controller
- FormRequest
- DTO
- Service
- Repository

## Estrutura gerada

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       ├── Repositories/
│       └── Service/
│
├── Http/
│   ├── Controllers/
│   └── Requests/
│
└── Models/
```

## Próximo passo

Continue para a página de instalação para configurar JWT, Hash e a estrutura completa da aplicação.