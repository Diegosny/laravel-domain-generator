# Getting Started

<VersionBadge version="v2.0"/>

Welcome to **Laravel Domain Generator**.

This package helps you build Laravel applications using **Domain Driven Design (DDD)** and **Clean Architecture** by generating a complete domain structure with a single Artisan command.

<Callout type="info">

Supports **Laravel 11, 12 and 13**, PHP **8.2+** and JWT authentication out of the box.

</Callout>

## What you'll build

Instead of manually creating Controllers, Services and Repositories, you'll generate this structure automatically.

<svg viewBox="0 0 900 140" width="100%" role="img" aria-label="Controller DTO Service Repository Model flow">
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

## Why use it?

<FeatureCard icon="🧱" title="Organized Architecture">

Keep your application divided into Controllers, DTOs, Services and Repositories from day one.

</FeatureCard>

<FeatureCard icon="⚡" title="Less Boilerplate">

Generate repetitive code automatically and focus on business rules.

</FeatureCard>

## Next step

Continue to the Installation guide.