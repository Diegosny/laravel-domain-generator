---
layout: home

hero:
  name: Laravel Domain Generator
  text: Construa domínios Laravel com DDD e Clean Architecture
  tagline: Gere Controllers, DTOs, Services, Repositories, APIs JWT e Identificadores Públicos com um único comando Artisan.

  image:
    src: /logo.svg
    alt: Laravel Domain Generator

  actions:
    - theme: brand
      text: Primeiros Passos
      link: /pt/getting-started

    - theme: alt
      text: GitHub
      link: https://github.com/Diegosny/laravel-domain-generator

features:
  - title: Arquitetura DDD
    details: Organize Controllers, Services, Repositories e DTOs desde o primeiro dia.

  - title: Um Único Comando
    details: Gere um domínio Laravel completo com um único comando Artisan.

  - title: JWT Integrado
    details: Endpoints de autenticação prontos para uso.

  - title: Identificadores Públicos
    details: Suporte nativo para ULID, UUID e UUID32.

  - title: Repository Pattern
    details: Repositórios genéricos com paginação, filtros e relacionamentos.

  - title: CI/CD Profissional
    details: GitHub Actions, Releases, PHPStan, Laravel Pint e deploy automático da documentação.
---

<HeroDashboard/>

> Construído para desenvolvedores que valorizam arquitetura, manutenção e automação.

## Quick Start

Instale a biblioteca.

```bash
composer require domain/laravel-domain-generator
```

Gere seu primeiro domínio.

```bash
php artisan make:domain User
```

<Callout type="success">

Seu primeiro domínio Laravel completo é gerado em segundos.

</Callout>

---

## Por que Laravel Domain Generator?

Em vez de criar manualmente Controllers, DTOs, Services, Repositories e toda a estrutura de autenticação, a biblioteca gera automaticamente uma arquitetura escalável baseada em Domain Driven Design.

Ideal para projetos que utilizam:

- Domain Driven Design (DDD)
- Clean Architecture
- Repository Pattern
- APIs REST
- Autenticação JWT
- Identificadores Públicos

---

## Fluxo Profissional de Desenvolvimento

A biblioteca segue uma pipeline de qualidade inspirada em projetos como Laravel, Filament e Spatie.

| Recurso | Descrição |
|---------|-----------|
| 🚀 Releases Automáticas | Versionamento semântico com GitHub Releases |
| 📖 Documentação Online | Deploy automático com VitePress |
| 🔒 Segurança | Composer Audit e CodeQL |
| ⚙️ Qualidade de Código | Laravel Pint e PHPStan |
| 📦 Validação do Pacote | Composer validate a cada push |
| 🧪 Pipeline CI | Lint, Build e Deploy da documentação |

---

## Arquitetura Gerada

A biblioteca gera automaticamente uma estrutura semelhante a esta.

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

Cada arquivo segue as convenções do Laravel mantendo a lógica de negócio isolada na camada de domínio.

---

## Por que desenvolvedores escolhem esta biblioteca


| Recurso | Benefício |
|---------|-----------|
| 🧱 **DDD desde o início** | Estrutura orientada a domínio desde o primeiro dia. |
| 🔐 **JWT Integrado** | Endpoints de autenticação prontos para uso. |
| 🆔 **Identificadores Públicos** | Suporte para ULID, UUID e UUID32. |
| 📦 **Repository Genérico** | Paginação, filtros e relacionamentos integrados. |
| 🏗️ **Clean Architecture** | Regras de negócio separadas da camada HTTP. |
| 🚀 **CI/CD Profissional** | GitHub Actions, Releases e documentação automática. |

---

## Próximo Passo

Continue para o guia de Instalação e gere seu primeiro domínio Laravel pronto para produção.

**→ Próximo:** [Instalação](/pt/installation)