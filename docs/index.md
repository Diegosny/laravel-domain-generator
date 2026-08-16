---
layout: home

hero:
  name: Laravel Domain Generator
  text: DDD + Clean Architecture para Laravel
  tagline: Gere Controllers, DTOs, Services, Repositories, Resources e Migrations em segundos.
  image:
    src: /logo.svg
    alt: Laravel Domain Generator

  actions:
    - theme: brand
      text: Começar →
      link: /getting-started

    - theme: alt
      text: GitHub
      link: https://github.com/Diegosny/laravel-domain-generator

features:
  - icon: 🏛️
    title: Domain Driven Design
    details: Estruture sua aplicação por domínio desde o primeiro commit.

  - icon: 🚀
    title: CRUD Automatizado
    details: Gere Controllers, Services, DTOs, Repositories, Requests e Migrations.

  - icon: 🔐
    title: JWT Ready
    details: Login, Logout, Refresh e Me prontos para uso.

  - icon: 🔑
    title: Identificadores Públicos
    details: ULID, UUID e UUID32 configuráveis.

  - icon: 📦
    title: Repository Pattern
    details: Abstrações reutilizáveis sobre o Eloquent.

  - icon: 🎯
    title: Resources Automáticos
    details: Collections, paginação e Models transformados automaticamente.
---

# Laravel Domain Generator

Uma biblioteca para Laravel desenvolvida para automatizar e padronizar aplicações seguindo **DDD** e **Clean Architecture**.

## Instalação rápida

```bash
composer require domain/laravel-domain-generator
php artisan jwt:secret
php artisan make:domain User
```

## Fluxo da aplicação

```text
HTTP Request
      │
      ▼
 FormRequest
      │
      ▼
     DTO
      │
      ▼
 Controller
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
 JSON Response
```

## Principais recursos

- Estrutura baseada em DDD
- DTO automático
- Repository Pattern
- Resources automáticos
- JWT integrado
- Hash público (ULID/UUID)
- Paginação automática
- Relacionamentos seguros
- GitHub Actions
- Compatível com Laravel 11, 12 e 13