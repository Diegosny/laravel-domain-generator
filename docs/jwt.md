# Autenticação JWT

O pacote possui integração nativa com **php-open-source-saver/jwt-auth**, oferecendo autenticação pronta para APIs.

Os endpoints ficam disponíveis automaticamente.

```
/api/auth
```

---

# Configuração

## Gerando a chave

```bash
php artisan jwt:secret
```

---

## auth.php

Configure:

```php
'guards'=>[
    'api'=>[
        'driver'=>'jwt',
        'provider'=>'users'
    ]
];
```

---

## User

O Model deve estender:

```php
Authenticatable
```

e implementar:

```php
JWTSubject
```

Exemplo:

```php
class User extends Authenticatable implements JWTSubject
{
    use HasJwtAuth;
}
```

---

# Endpoints

| Método | Endpoint |
|---------|----------|
| POST | `/api/auth/login` |
| GET | `/api/auth/me` |
| POST | `/api/auth/refresh` |
| POST | `/api/auth/logout` |

---

# Login

Requisição:

```http
POST /api/auth/login
```

```json
{
  "email":"user@email.com",
  "password":"123456"
}
```

Resposta:

```json
{
  "type":"success",
  "status":200,
  "data":{
    "access_token":"...",
    "token_type":"Bearer",
    "expires_in":3600
  }
}
```

---

# Usuário autenticado

```http
GET /api/auth/me
```

Resposta:

```json
{
  "type":"success",
  "status":200,
  "data":{
    "hash":"01K...",
    "nome":"Diego"
  }
}
```

---

# Refresh

```http
POST /api/auth/refresh
```

Novo token será retornado.

---

# Logout

```http
POST /api/auth/logout
```

O token atual será invalidado.

---

# Fluxo da autenticação

<svg viewBox="0 0 260 300" width="100%" role="img" aria-label="Fluxo JWT">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">Login</text>
  <path d="M130 56 V92" stroke="currentColor"/>
  <rect x="50" y="92" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="115" text-anchor="middle" font-size="13">JWT</text>
  <path d="M130 128 V164" stroke="currentColor"/>
  <rect x="50" y="164" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="187" text-anchor="middle" font-size="13">auth:api</text>
  <path d="M130 200 V236" stroke="currentColor"/>
  <rect x="50" y="236" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="259" text-anchor="middle" font-size="13">Usuário</text>
</svg>

---

# Personalizando o campo de login

No `.env`:

```env
DOMAIN_GENERATOR_LOGIN_FIELD=email
```

Também funciona para:

- cpf
- username

---

# Troubleshooting

## Credenciais inválidas

Verifique:

- senha
- campo de login

## JWT 401

Execute:

```bash
php artisan jwt:secret
```

## User precisa estender

```php
Authenticatable
```