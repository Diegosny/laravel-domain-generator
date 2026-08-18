# Arquitetura

O Laravel Domain Generator segue os princípios de **DDD** e **Clean Architecture**.

## Fluxo da Requisição

<svg viewBox="0 0 900 140" width="100%" role="img" aria-label="Fluxo HTTP">
  <rect x="20" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="180" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="340" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="500" y="40" width="140" height="50" rx="10" fill="none" stroke="currentColor"/>
  <rect x="680" y="40" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="80" y="70" text-anchor="middle" font-size="14">Request</text>
  <text x="240" y="70" text-anchor="middle" font-size="14">Controller</text>
  <text x="400" y="70" text-anchor="middle" font-size="14">DTO</text>
  <text x="570" y="70" text-anchor="middle" font-size="14">Service</text>
  <text x="740" y="70" text-anchor="middle" font-size="14">Repository</text>
  <path d="M140 65 H180 M300 65 H340 M460 65 H500 M640 65 H680" stroke="currentColor" fill="none"/>
</svg>

## Responsabilidades

| Camada | Responsabilidade |
|---------|------------------|
| Controller | HTTP |
| DTO | Transporte de Dados |
| Service | Regras de Negócio |
| Repository | Persistência |
| Model | Banco de Dados |

## Por que essa separação?

- Testes mais simples.
- Menor acoplamento.
- Maior manutenção.
- Regras reutilizáveis.