# Exemplo de Autenticação

<VersionBadge version="v1.1.0"/>

Este guia demonstra todo o fluxo de autenticação JWT gerado automaticamente pela biblioteca.

---

## Fluxo de Autenticação

<svg viewBox="0 0 760 180" width="100%" role="img" aria-label="Fluxo de autenticação JWT">
  <rect x="20" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="65" y="84" text-anchor="middle" font-size="14">Login</text>
  <path d="M110 80 L200 80" stroke="currentColor" stroke-width="2"/>
  <path d="M200 80 l-8 -6 M200 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="200" y="55" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="255" y="84" text-anchor="middle" font-size="14">JWT Guard</text>
  <path d="M310 80 L420 80" stroke="currentColor" stroke-width="2"/>
  <path d="M420 80 l-8 -6 M420 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="420" y="55" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="480" y="84" text-anchor="middle" font-size="14">Access Token</text>
  <path d="M540 80 L650 80" stroke="currentColor" stroke-width="2"/>
  <path d="M650 80 l-8 -6 M650 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>
  <rect x="650" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="695" y="84" text-anchor="middle" font-size="14">API Protegida</text>
</svg>

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
    "token_type": "Bearer",
    "expires_in": 3600
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

Retorna o usuário autenticado.

---

## Refresh

<ApiMethod method="POST"/> `/api/auth/refresh`

Gera um novo token JWT.

---

## Logout

<ApiMethod method="POST"/> `/api/auth/logout`

Invalida o token atual.

---

## Fluxo Interno

```text
Request
    ↓
LoginRequest
    ↓
JWT Guard
    ↓
Token
    ↓
JSON
```

O Controller já trata automaticamente credenciais inválidas e tokens expirados.

---

## Respostas de Erro

Credenciais inválidas:

```json
{
  "type": "error",
  "status": 401
}
```

Token ausente:

```json
{
  "type": "error",
  "status": 401
}
```

Token expirado:

```json
{
  "type": "error",
  "status": 401
}
```

<Callout type="success">

Todos os endpoints de autenticação seguem o mesmo padrão de resposta utilizado em toda a biblioteca.

</Callout>