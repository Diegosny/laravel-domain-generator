# Exemplo de Autenticação

<VersionBadge version="v1.1.0"/>

Fluxo completo de autenticação JWT.

---

## Fluxo

```text
Login
   │
   ▼
JWT
   │
   ▼
Bearer Token
   │
   ▼
Rotas Protegidas
```

---

## Login

<ApiMethod method="POST"/> `/api/auth/login`

```json
{
  "email": "diego@email.com",
  "password": "12345678"
}
```

Resposta:

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "access_token": "...",
    "token_type": "Bearer"
  }
}
```

---

## Usuário Atual

<ApiMethod method="GET"/> `/api/auth/me`

Header:

```text
Authorization: Bearer TOKEN
```

---

## Refresh

<ApiMethod method="POST"/> `/api/auth/refresh`

Renova o JWT.

---

## Logout

<ApiMethod method="POST"/> `/api/auth/logout`

Invalida o token.

<Callout type="success">

Os endpoints seguem o mesmo padrão de resposta utilizado em toda a biblioteca.

</Callout>