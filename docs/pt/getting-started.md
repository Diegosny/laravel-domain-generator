# Primeiros Passos

<VersionBadge version="v2.0"/>

Bem-vindo ao **Laravel Domain Generator**.

Esta biblioteca ajuda você a construir aplicações Laravel utilizando **DDD** e **Clean Architecture**, gerando uma estrutura completa de domínio com um único comando.

<Callout type="info">

Compatível com **Laravel 11, 12 e 13**, PHP **8.2+** e autenticação JWT integrada.

</Callout>

## O que você irá construir

Em vez de criar Controllers, Services e Repositories manualmente, você gerará automaticamente esta estrutura.

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

## Por que usar?

<FeatureCard icon="🧱" title="Arquitetura Organizada">

Mantenha sua aplicação dividida em Controllers, DTOs, Services e Repositories desde o início.

</FeatureCard>

<FeatureCard icon="⚡" title="Menos Boilerplate">

Gere código repetitivo automaticamente e foque nas regras de negócio.

</FeatureCard>

## Próximo passo

Continue para o guia de Instalação.