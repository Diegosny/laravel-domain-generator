# make:domain

O comando `make:domain` é o principal recurso da biblioteca.

Ele automatiza a criação da estrutura inicial de um domínio seguindo DDD.

## Criando um domínio

```bash
php artisan make:domain Municipio
```

## Resultado

Serão criados automaticamente:

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
│   └── Municipio/
│       ├── DTO/
│       │   └── MunicipioDTO.php
│       │
│       ├── Repositories/
│       │   └── MunicipioRepository.php
│       │
│       └── Service/
│           └── MunicipioService.php
│
├── Http/
│   ├── Controllers/
│   │   └── MunicipioController.php
│   │
│   └── Requests/
│       └── MunicipioRequest.php
│
└── Models/
    └── Municipio.php
```

---

## Sobrescrevendo arquivos

Caso já existam arquivos:

```bash
php artisan make:domain Municipio --force
```

---

## Personalizando a pasta dos domínios

Por padrão:

```text
app/Domain
```

Pode ser alterado no `.env`.

```env
APP_DOMAIN_FOLDER=Modules
```

Resultado:

```text
app/Modules/Municipio
```

---

## O que cada arquivo faz

### Controller

Recebe a requisição HTTP.

### Request

Valida os dados.

### DTO

Transporta dados para o Service.

### Service

Executa regras de negócio.

### Repository

Realiza consultas ao banco.

### Model

Representa a tabela.

---

## Fluxo do comando

<svg viewBox="0 0 260 520" width="100%" role="img" aria-label="Fluxo do comando make:domain">
  <rect x="40" y="20" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">make:domain</text>
  <path d="M130 56 V84" stroke="currentColor"/>
  <rect x="40" y="84" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="107" text-anchor="middle" font-size="13">Model</text>
  <path d="M130 120 V148" stroke="currentColor"/>
  <rect x="40" y="148" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="171" text-anchor="middle" font-size="13">Migration</text>
  <path d="M130 184 V212" stroke="currentColor"/>
  <rect x="40" y="212" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="235" text-anchor="middle" font-size="13">Controller</text>
  <path d="M130 248 V276" stroke="currentColor"/>
  <rect x="40" y="276" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="299" text-anchor="middle" font-size="13">Request</text>
  <path d="M130 312 V340" stroke="currentColor"/>
  <rect x="40" y="340" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="363" text-anchor="middle" font-size="13">DTO</text>
  <path d="M130 376 V404" stroke="currentColor"/>
  <rect x="40" y="404" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="427" text-anchor="middle" font-size="13">Service</text>
  <path d="M130 440 V468" stroke="currentColor"/>
  <rect x="40" y="468" width="180" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="491" text-anchor="middle" font-size="13">Repository</text>
</svg>

---

## Boas práticas

- Mantenha regras no Service.
- Evite lógica de negócio no Controller.
- Utilize DTOs sempre que possível.
- Utilize Resources para saída HTTP.