# Laravel Domain Generator

[![Latest Stable Version](https://img.shields.io/badge/version-v1.0.14-blue.svg)](https://github.com/SEU_USUARIO/laravel-domain-generator)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

O **Laravel Domain Generator** é um pacote para Laravel desenvolvido para automatizar e padronizar a criação de estruturas de **Domain Driven Design (DDD)** / **Clean Architecture**.

Com um único comando Artisan, o pacote gera os arquivos de **Controller**, **Service** e **Repository** totalmente configurados com herança automática das classes base do pacote (`AbstractController`, `AbstractService` e `AbstractRepository`).

---

## 💡 Funcionalidades

- 🚀 **Geração de Domínio Completa:** Cria estrutura modular para seu novo domínio de forma instantânea.
- 🏗️ **Classes Abstratas Robustas:**
    - `AbstractController`: Padronização de respostas JSON (`ok`, `success`, `error`), validação automática com FormRequests e manipulação global de exceções.
    - `AbstractService`: Métodos nativos de CRUD com *hooks* (`beforeSave`, `afterSave`, `beforeUpdate`, `afterUpdate`, etc.).
    - `AbstractRepository`: Abstração de camada de dados com suporte a paginação e relacionamentos.
- 🧱 **Boas Práticas & Clean Code:** Injeção de dependência via construtor gerada automaticamente em todas as camadas.

---

## 📦 Instalação

Adicione o repositório ao seu `composer.json` (ou instale via Composer se estiver publicado no Packagist):

```bash
composer require domain/laravel-domain-generator
````

## Depois rode comando

Ex:
```bash
php artisan make:domain User
```