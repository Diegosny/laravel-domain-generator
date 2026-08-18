# Autenticação JWT

<VersionBadge version="v1.1.0"/>

Autenticação integrada utilizando **php-open-source-saver/jwt-auth**.

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

## Endpoints

| Método | Endpoint |
|---------|----------|
| <ApiMethod method="POST"/> | `/api/auth/login` |
| <ApiMethod method="GET"/> | `/api/auth/me` |
| <ApiMethod method="POST"/> | `/api/auth/refresh` |
| <ApiMethod method="POST"/> | `/api/auth/logout` |

---

## Login

<ApiMethod method="POST"/> `/api/auth/login`

Requisição:

```json
{
  "email": "diego@email.com",
  "password": "123456"
}
```

Resposta:

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "access_token": "...",
    "token_type": "Bearer",
    "expires_in": 3600
  }
}
```

<Callout type="success">

Utilize o token retornado no header `Authorization: Bearer`.

</Callout>

---

## Usuário autenticado

<ApiMethod method="GET"/> `/api/auth/me`

Header:

```text
Authorization: Bearer TOKEN
```

---

## Refresh

<ApiMethod method="POST"/> `/api/auth/refresh`

Renova o token JWT.

---

## Logout

<ApiMethod method="POST"/> `/api/auth/logout`

Invalida o token atual.