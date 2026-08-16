# AbstractController

O `AbstractController` é a camada responsável por integrar a aplicação HTTP ao domínio.

Ele fornece um CRUD completo, integração automática com DTOs, API Resources, paginação, tratamento de exceções e respostas JSON padronizadas.

---

## Visão Geral

Ao estender essa classe, o Controller já recebe automaticamente:

- CRUD REST completo
- Validação automática via FormRequest
- Conversão automática para DTO
- API Resources automáticos
- Paginação automática
- Tratamento centralizado de exceções
- Respostas JSON padronizadas
- Relacionamentos via `with`

Exemplo mínimo:

```php
class UserController extends AbstractController
{
    protected mixed $service;

    protected ?string $requestValidate = UserRequest::class;

    protected ?string $requestDto = UserDTO::class;

    protected ?string $resource = UserResource::class;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }
}
```

---

## Fluxo interno

<svg viewBox="0 0 260 380" width="100%" role="img" aria-label="Fluxo do AbstractController">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">Request</text>
  <path d="M130 56 V84" stroke="currentColor"/>
  <rect x="50" y="84" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="107" text-anchor="middle" font-size="13">FormRequest</text>
  <path d="M130 120 V148" stroke="currentColor"/>
  <rect x="50" y="148" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="171" text-anchor="middle" font-size="13">DTO</text>
  <path d="M130 184 V212" stroke="currentColor"/>
  <rect x="50" y="212" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="235" text-anchor="middle" font-size="13">Service</text>
  <path d="M130 248 V276" stroke="currentColor"/>
  <rect x="50" y="276" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="299" text-anchor="middle" font-size="13">Resource</text>
  <path d="M130 312 V340" stroke="currentColor"/>
  <rect x="50" y="340" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="363" text-anchor="middle" font-size="13">JSON</text>
</svg>

---

# Propriedades disponíveis

## `$service`

Define qual Service será utilizado.

```php
protected mixed $service;
```

---

## `$requestValidate`

Define o FormRequest utilizado na criação.

```php
protected ?string $requestValidate = UserRequest::class;
```

---

## `$requestValidateUpdate`

Request utilizado para atualização.

```php
protected ?string $requestValidateUpdate = UpdateUserRequest::class;
```

---

## `$requestDto`

DTO utilizado no Store.

```php
protected ?string $requestDto = UserDTO::class;
```

---

## `$requestDtoUpdate`

DTO utilizado no Update.

```php
protected ?string $requestDtoUpdate = UserUpdateDTO::class;
```

---

## `$resource`

Resource utilizado nas respostas.

```php
protected ?string $resource = UserResource::class;
```

---

## `$with`

Relacionamentos carregados automaticamente.

```php
protected array $with = [
    'roles',
    'permissions',
];
```

Também é possível utilizar:

```http
GET /api/users?with=roles,permissions
```

---

# Métodos disponíveis

## index()

Lista registros.

```php
public function index(Request $request)
```

Retorna paginação automaticamente.

Exemplo:

```http
GET /api/users
```

---

## show()

Busca um registro.

```php
public function show(Request $request, mixed $id)
```

O identificador pode ser:

- ID
- Hash (ULID/UUID)

Exemplo:

```http
GET /api/users/01J5...
```

---

## store()

Cria um registro.

```php
public function store(Request $request)
```

Fluxo:

```text
Request
 ↓
FormRequest
 ↓
DTO
 ↓
Service
```

---

## update()

Atualiza um registro.

```php
public function update(Request $request, mixed $id)
```

---

## destroy()

Remove um registro.

```php
public function destroy(mixed $id)
```

Retorno:

```json
{
    "type":"success",
    "status":200,
    "data":{
        "message":"Registro removido com sucesso."
    }
}
```

---

# Recursos automáticos

## DTO automático

Quando configurado:

```php
protected ?string $requestDto = UserDTO::class;
```

o Controller executa automaticamente:

```php
UserDTO::fromArray($validated);
```

---

## Resources automáticos

Se existir:

```php
protected ?string $resource = UserResource::class;
```

o Controller transforma automaticamente:

| Retorno | Transformação |
|----------|---------------|
| Model | Resource |
| Collection | Resource::collection |
| Paginator | Resource::collection |

Sem precisar escrever:

```php
return new UserResource(...);
```

---

## Paginação

O Controller preserva automaticamente:

- links
- meta
- current_page
- last_page

---

## Tratamento de exceções

Exceções são convertidas automaticamente.

| Exceção | HTTP |
|---------|------|
| ValidationException | 422 |
| AuthorizationException | 403 |
| Throwable | 500 |

---

## Resposta de sucesso

```json
{
    "type":"success",
    "status":200,
    "data":{}
}
```

---

## Resposta de erro

```json
{
    "type":"error",
    "status":500,
    "message":"Erro interno."
}
```

---

## Permissões

Caso o Model utilize Spatie Permission:

```php
$this->hasPermissionTo('users.create');
```

Uma `AuthorizationException` será lançada automaticamente.