# make:domain

<VersionBadge version="v2.0"/>

O comando `make:domain` gera uma estrutura completa baseada em Domain Driven Design com um único comando.

## Utilização

```bash
php artisan make:domain User
```

Para sobrescrever arquivos existentes:

```bash
php artisan make:domain User --force
```

<Callout type="success">

O comando utiliza os generators nativos do Laravel sempre que possível, mantendo as convenções do framework.

</Callout>

## Estrutura Gerada

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

## Fluxo de geração

<svg viewBox="0 0 760 120" width="100%" role="img" aria-label="Fluxo make:domain">
  <rect x="20" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="170" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="320" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="470" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <rect x="620" y="35" width="110" height="45" rx="10" fill="none" stroke="currentColor"/>
  <text x="75" y="62" text-anchor="middle" font-size="13">Comando</text>
  <text x="225" y="62" text-anchor="middle" font-size="13">Model</text>
  <text x="375" y="62" text-anchor="middle" font-size="13">Migration</text>
  <text x="525" y="62" text-anchor="middle" font-size="13">Domínio</text>
  <text x="675" y="62" text-anchor="middle" font-size="13">HTTP</text>
  <path d="M130 57 H170 M280 57 H320 M430 57 H470 M580 57 H620" stroke="currentColor" fill="none"/>
</svg>

## O que é criado?

| Componente | Finalidade |
|------------|------------|
| Model | Modelo Eloquent |
| Migration | Banco |
| Controller | HTTP |
| Request | Validação |
| DTO | Transporte |
| Service | Regras |
| Repository | Persistência |