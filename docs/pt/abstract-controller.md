# AbstractController

<VersionBadge version="v1.1.0"/>

O `AbstractController` é a base de todos os Controllers gerados pela biblioteca.

Em vez de implementar manualmente CRUD, validação, conversão para DTO e respostas padronizadas, os Controllers herdam esse comportamento automaticamente.

<Callout type="info">

Todo Controller criado por `php artisan make:domain` estende essa classe.

</Callout>

---

## Visão Geral

Ao estender essa classe, o Controller recebe automaticamente:

- CRUD REST completo
- Validação automática via FormRequest
- Conversão automática para DTO
- Delegação para o Service
- Serialização via Resource
- Respostas JSON padronizadas
- Paginação automática
- Tratamento centralizado de exceções
- Relacionamentos via eager loading

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

Todo request percorre sempre o mesmo pipeline.

<svg viewBox="0 0 700 160" width="100%" role="img" aria-label="Fluxo interno do AbstractController">
  <rect x="20" y="45" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="65" y="73" text-anchor="middle" font-size="14">Request</text>

  <path d="M110 70 L155 70" stroke="currentColor" stroke-width="2"/>
  <path d="M155 70 l-8 -6 M155 70 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="155" y="45" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="210" y="73" text-anchor="middle" font-size="14">FormRequest</text>

  <path d="M265 70 L320 70" stroke="currentColor" stroke-width="2"/>
  <path d="M320 70 l-8 -6 M320 70 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="320" y="45" width="70" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="355" y="73" text-anchor="middle" font-size="14">DTO</text>

  <path d="M390 70 L450 70" stroke="currentColor" stroke-width="2"/>
  <path d="M450 70 l-8 -6 M450 70 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="450" y="45" width="80" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="490" y="73" text-anchor="middle" font-size="14">Service</text>

  <path d="M530 70 L590 70" stroke="currentColor" stroke-width="2"/>
  <path d="M590 70 l-8 -6 M590 70 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="590" y="45" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="635" y="73" text-anchor="middle" font-size="14">Repository</text>
</svg>

Isso mantém a camada HTTP separada da lógica de negócio.

---

## Propriedades protegidas

### `$service`

```php
protected mixed $service;
```

Armazena a instância do Service injetada no construtor.

O Controller nunca conversa diretamente com o Model.

Fluxo:

```text
Controller
    ↓
 Service
    ↓
Repository
    ↓
 Model
```

---

### `$requestValidate`

```php
protected ?string $requestValidate;
```

Define qual FormRequest valida o método `store()`.

Exemplo:

```php
protected ?string $requestValidate = UserRequest::class;
```

Fluxo:

1. Laravel valida o Request.
2. Erros retornam HTTP 422.
3. O payload validado é convertido em DTO.

---

### `$requestValidateUpdate`

```php
protected ?string $requestValidateUpdate;
```

Define qual FormRequest será utilizado durante o `update()`.

Permite regras diferentes para criação e atualização.

---

### `$requestDto`

```php
protected ?string $requestDto;
```

Define qual DTO será criado automaticamente durante o `store()`.

Internamente:

```php
UserDTO::fromRequest($request);
```

---

### `$requestDtoUpdate`

Utilizado pelo `update()`.

```php
protected ?string $requestDtoUpdate = UserUpdateDTO::class;
```

Mantém campos específicos de atualização isolados.

---

### `$resource`

Define qual Resource será utilizado para serializar a resposta.

Exemplo:

```php
protected ?string $resource = UserResource::class;
```

Em vez de retornar Models diretamente:

```php
return new UserResource($user);
```

---

### `$with`

```php
protected array $with = [];
```

Carrega relacionamentos automaticamente.

Exemplo:

```php
protected array $with = [
    'municipio'
];
```

Equivalente a:

```php
User::with('municipio');
```

---

## Métodos CRUD

### `index()`

Retorna uma coleção paginada.

Fluxo:

```text
Repository
   ↓
paginate()
   ↓
Resource::collection()
```

---

### `show()`

Busca um único registro utilizando identificadores públicos automaticamente.

Exemplo:

```text
GET /api/users/01JXYZABCDEF
```

---

### `store()`

Fluxo completo:

1. Valida o Request.
2. Cria o DTO.
3. Executa o Service.
4. Serializa o Resource.
5. Retorna HTTP 201.

---

### `update()`

Utiliza Request e DTO específicos para atualização.

Fluxo:

```text
Request
   ↓
Update Request
   ↓
Update DTO
   ↓
Service
```

---

### `destroy()`

Remove o registro.

Quando o Model utiliza:

```php
use SoftDeletes;
```

o Controller executa Soft Delete automaticamente.

---

## Conversão automática para DTO

Em vez de utilizar:

```php
$request->validated();
```

o Controller executa:

```php
UserDTO::fromRequest($request);
```

Benefícios:

- tipagem forte;
- payload imutável;
- Services mais limpos.

---

## Resources automáticos

Os Controllers nunca retornam Models diretamente.

Em vez disso:

```php
return new UserResource($user);
```

Vantagens:

- oculta IDs internos;
- padroniza respostas;
- facilita integração com frontend.

---

## Paginação

A paginação é automática.

Exemplo:

```php
$this->service->paginate();
```

A resposta já contém:

- `data`
- `links`
- `meta`

Sem necessidade de código adicional.

---

## Tratamento de exceções

As exceções são normalizadas.

Exemplo:

```json
{
  "type": "error",
  "status": 404,
  "message": "Recurso não encontrado."
}
```

---

## Resposta de sucesso

Todas as operações bem-sucedidas seguem o mesmo padrão.

```json
{
  "type": "success",
  "status": 200,
  "data": {}
}
```

---

## Resposta de erro

Validação:

```json
{
  "type": "error",
  "status": 422
}
```

Autenticação:

```json
{
  "type": "error",
  "status": 401
}
```

Recurso inexistente:

```json
{
  "type": "error",
  "status": 404
}
```

---

## Permissões

A autorização pode ser personalizada sobrescrevendo os métodos gerados.

Exemplo:

```php
public function update(...)
{
    $this->authorize('update', $user);

    return parent::update(...);
}
```

Mantém compatibilidade total com Laravel Policies.

---

## Boas práticas

- Mantenha Controllers enxutos.
- Coloque regras de negócio no Service.
- Receba DTOs em vez de Requests.
- Retorne Resources em vez de Models.
- Utilize identificadores públicos nas APIs.

<Callout type="success">

Os Controllers gerados seguem a arquitetura em camadas do Laravel Domain Generator, separando completamente HTTP da lógica de negócio e eliminando código repetitivo.

</Callout>