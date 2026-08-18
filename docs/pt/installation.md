# Instalação

<VersionBadge version="v1.1.0"/>

A instalação leva apenas alguns minutos.

---

## Requisitos

| Requisito | Versão |
|-----------|---------|
| PHP | 8.2+ |
| Laravel | 11, 12 ou 13 |
| Composer | Última versão |

---

## Instalação

Execute:

```bash
composer require domain/laravel-domain-generator
```

<Callout type="info">

A biblioteca oferece suporte ao Laravel 11, 12 e 13.

</Callout>

---

## Publicar Configuração

```bash
php artisan vendor:publish --provider="Domain\DomainGenerator\DomainGeneratorServiceProvider"
```

Será criado:

```text
config/domain-generator.php
```

---

## Gerar JWT Secret

```bash
php artisan jwt:secret
```

<Callout type="success">

Sua aplicação já está pronta para gerar domínios e utilizar os endpoints de autenticação.

</Callout>

---

## Gerar o primeiro domínio

```bash
php artisan make:domain User
```

Estrutura gerada:

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

---

## Próximo passo

Continue para **Autenticação JWT**.