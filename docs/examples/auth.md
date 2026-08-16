# Autenticação

A biblioteca fornece autenticação JWT pronta para APIs.

---

## Fluxo

<svg viewBox="0 0 260 360" width="100%" role="img" aria-label="Fluxo da autenticação JWT">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">Login</text>
  <path d="M130 56 V92" stroke="currentColor"/>
  <rect x="50" y="92" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="115" text-anchor="middle" font-size="13">JWT</text>
  <path d="M130 128 V164" stroke="currentColor"/>
  <rect x="50" y="164" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="187" text-anchor="middle" font-size="13">Bearer Token</text>
  <path d="M130 200 V236" stroke="currentColor"/>
  <rect x="50" y="236" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="259" text-anchor="middle" font-size="13">auth:api</text>
  <path d="M130 272 V308" stroke="currentColor"/>
  <rect x="50" y="308" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="331" text-anchor="middle" font-size="13">Usuário</text>
</svg>

---

## Login

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

## Utilizando o token

```http
Authorization: Bearer {token}
```

Agora todas as rotas protegidas passam a funcionar.