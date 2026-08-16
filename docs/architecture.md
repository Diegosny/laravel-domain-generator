# Arquitetura

O **Laravel Domain Generator** foi desenvolvido seguindo os princípios de **Domain Driven Design (DDD)** e **Clean Architecture**, separando responsabilidades entre as camadas da aplicação.

Essa organização reduz acoplamento, facilita testes e torna o código mais previsível à medida que o projeto cresce.

---

## Visão Geral

A comunicação entre as camadas acontece sempre em uma única direção.

<svg viewBox="0 0 900 140" width="100%" role="img" aria-label="Fluxo Controller DTO Service Repository Model">
  <rect x="20" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="180" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="340" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="500" y="40" width="140" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="680" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="80" y="70" text-anchor="middle" font-size="14">Controller</text>
  <text x="240" y="70" text-anchor="middle" font-size="14">DTO</text>
  <text x="400" y="70" text-anchor="middle" font-size="14">Service</text>
  <text x="570" y="70" text-anchor="middle" font-size="14">Repository</text>
  <text x="740" y="70" text-anchor="middle" font-size="14">Model</text>
  <path d="M140 65 H180 M300 65 H340 M460 65 H500 M640 65 H680" stroke="currentColor" fill="none"/>
</svg>

Cada camada possui uma responsabilidade única.

| Camada | Responsabilidade |
|--------|------------------|
| Controller | Entrada HTTP |
| FormRequest | Validação |
| DTO | Transporte de dados |
| Service | Regras da aplicação |
| Repository | Persistência |
| Model | Representação do banco |
| Resource | Transformação da resposta |

---

## Estrutura do projeto

Após executar:

```bash
php artisan make:domain User
```

a estrutura ficará semelhante a:

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

---

## Fluxo completo de uma requisição

Quando um cliente envia uma requisição, o fluxo acontece desta forma:

<svg viewBox="0 0 260 520" width="100%" role="img" aria-label="Fluxo completo da requisição">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">HTTP Request</text>
  <path d="M130 56 V84" stroke="currentColor"/>
  <rect x="50" y="84" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="107" text-anchor="middle" font-size="13">FormRequest</text>
  <path d="M130 120 V148" stroke="currentColor"/>
  <rect x="50" y="148" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="171" text-anchor="middle" font-size="13">DTO</text>
  <path d="M130 184 V212" stroke="currentColor"/>
  <rect x="50" y="212" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="235" text-anchor="middle" font-size="13">Service</text>
  <path d="M130 248 V276" stroke="currentColor"/>
  <rect x="50" y="276" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="299" text-anchor="middle" font-size="13">Repository</text>
  <path d="M130 312 V340" stroke="currentColor"/>
  <rect x="50" y="340" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="363" text-anchor="middle" font-size="13">Model</text>
  <path d="M130 376 V404" stroke="currentColor"/>
  <rect x="50" y="404" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="427" text-anchor="middle" font-size="13">Resource</text>
  <path d="M130 440 V468" stroke="currentColor"/>
  <rect x="50" y="468" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="491" text-anchor="middle" font-size="13">JSON Response</text>
</svg>

---

## Dependências entre camadas

A regra principal é simples.

Uma camada nunca conhece responsabilidades acima dela.

| Camada | Pode conhecer |
|--------|---------------|
| Controller | Service, DTO |
| Service | Repository |
| Repository | Model |
| Model | Ninguém |

Isso mantém o domínio independente da camada HTTP.

---

## O que fica dentro do domínio?

Dentro de `app/Domain` ficam apenas elementos relacionados às regras da aplicação.

```text
Domain/
└── User/
    ├── DTO/
    ├── Service/
    └── Repositories/
```

Não devem existir Controllers, Requests ou Resources dentro do domínio.

---

## Benefícios

- Código organizado por domínio.
- Menor acoplamento.
- Facilidade para testes.
- Reutilização de Services.
- Repositories independentes da camada HTTP.
- Crescimento previsível da aplicação.