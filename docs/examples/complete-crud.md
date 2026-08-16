# CRUD Completo

Este exemplo demonstra um CRUD completo utilizando o Laravel Domain Generator.

Ao final você terá:

- Model
- Migration
- Controller
- FormRequest
- DTO
- Service
- Repository
- Resource
- Rotas
- JSON final

---

## Criando o domínio

```bash
php artisan make:domain User
```

Estrutura criada:

```text
app/
├── Domain/
│   └── User/
│       ├── DTO/
│       ├── Repositories/
│       └── Service/
│
├── Http/
│   ├── Controllers/
│   └── Requests/
│
└── Models/
```

---

## Migration

```php
Schema::create('users', function (Blueprint $table) {

    $table->id();

    $table->char('hash', 26)->unique();

    $table->string('nome');

    $table->string('email')->unique();

    $table->string('password');

    $table->timestamps();

});
```

---

## Model

```php
class User extends Authenticatable implements JWTSubject
{
    use HasFactory;
    use HasJwtAuth;
    use HasHash;
}
```

---

## DTO

```php
final class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly string $password
    ) {}
}
```

---

## Service

```php
class UserService extends AbstractService
{
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }
}
```

---

## Repository

```php
class UserRepository extends AbstractRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }
}
```

---

## Resource

```php
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'hash'=>$this->hash,
            'nome'=>$this->nome,
            'email'=>$this->email,
        ];
    }
}
```

---

## Controller

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

## Rotas

```php
Route::apiResource('users', UserController::class);
```

---

## Resultado

Criando:

```http
POST /api/users
```

Resposta:

```json
{
  "type":"success",
  "status":201,
  "data":{
    "hash":"01K2JP...",
    "nome":"Diego",
    "email":"diego@email.com"
  }
}
```

---

## Fluxo completo

<svg viewBox="0 0 260 520" width="100%" role="img" aria-label="Fluxo completo do CRUD">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">Request</text>
  <path d="M130 56 V84" stroke="currentColor"/>
  <rect x="50" y="84" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="107" text-anchor="middle" font-size="13">DTO</text>
  <path d="M130 120 V148" stroke="currentColor"/>
  <rect x="50" y="148" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="171" text-anchor="middle" font-size="13">Service</text>
  <path d="M130 184 V212" stroke="currentColor"/>
  <rect x="50" y="212" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="235" text-anchor="middle" font-size="13">Repository</text>
  <path d="M130 248 V276" stroke="currentColor"/>
  <rect x="50" y="276" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="299" text-anchor="middle" font-size="13">Model</text>
  <path d="M130 312 V340" stroke="currentColor"/>
  <rect x="50" y="340" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="363" text-anchor="middle" font-size="13">Resource</text>
  <path d="M130 376 V404" stroke="currentColor"/>
  <rect x="50" y="404" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="427" text-anchor="middle" font-size="13">JSON</text>
</svg>