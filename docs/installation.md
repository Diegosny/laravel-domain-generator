# Instalação

## Instalação via Composer

```bash
composer require domain/laravel-domain-generator
```

## Publicando configurações

```bash
php artisan vendor:publish --tag=domain-generator-config
```

Será criado:

```text
config/domain-generator.php
```

## Configurando JWT

### Gerando a chave

```bash
php artisan jwt:secret
```

### auth.php

Configure:

```php
'guards' => [

    'api' => [
        'driver' => 'jwt',
        'provider' => 'users',
    ],

];
```

### User

O Model deve implementar:

```php
use HasJwtAuth;

class User extends Authenticatable implements JWTSubject
{
    use HasJwtAuth;
}
```

## Verificando instalação

Execute:

```bash
php artisan route:list
```

As rotas devem existir:

| Método | Endpoint |
|---------|----------|
| POST | `/api/auth/login` |
| GET | `/api/auth/me` |
| POST | `/api/auth/refresh` |
| POST | `/api/auth/logout` |