# JWT Authentication

<VersionBadge version="v1.1.0"/>

Built-in authentication powered by **php-open-source-saver/jwt-auth**.

---

## Authentication Flow

```text
Login
   │
   ▼
JWT Token
   │
   ▼
Bearer Authorization
   │
   ▼
Protected Routes
```

---

## Available Endpoints

| Method | Endpoint |
|---------|----------|
| <ApiMethod method="POST"/> | `/api/auth/login` |
| <ApiMethod method="GET"/> | `/api/auth/me` |
| <ApiMethod method="POST"/> | `/api/auth/refresh` |
| <ApiMethod method="POST"/> | `/api/auth/logout` |

---

## Login

<ApiMethod method="POST"/> `/api/auth/login`

Request:

```json
{
  "email": "john@example.com",
  "password": "secret"
}
```

Successful response:

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

Use the returned token in the `Authorization: Bearer` header.

</Callout>

---

## Current User

<ApiMethod method="GET"/> `/api/auth/me`

Headers:

```text
Authorization: Bearer YOUR_TOKEN
```

Response:

```json
{
  "name": "John Doe",
  "email": "john@example.com"
}
```

---

## Refresh Token

<ApiMethod method="POST"/> `/api/auth/refresh`

Returns a new JWT without requiring another login.

---

## Logout

<ApiMethod method="POST"/> `/api/auth/logout`

Invalidates the current token.