# Exemplo Completo: Produto

<VersionBadge version="v1.1.0"/>

Este guia demonstra o fluxo completo de utilização do Laravel Domain Generator utilizando um domínio chamado **Product**.

Ao final você entenderá exatamente como uma requisição percorre todas as camadas geradas, desde o endpoint da API até o Repository.

<Callout type="info">

Este é o exemplo mais completo da documentação e representa o fluxo recomendado para novos projetos.

</Callout>

---

# Resultado Final

Com um único comando Artisan é possível gerar uma arquitetura completa pronta para produção.

```bash
php artisan make:domain Product
```

Estrutura gerada.

```text
app/
├── Domain/
│   └── Product/
│       ├── DTO/
│       │   ├── ProductDTO.php
│       │   └── ProductUpdateDTO.php
│       ├── Repositories/
│       │   └── ProductRepository.php
│       └── Services/
│           └── ProductService.php
├── Http/
│   ├── Controllers/
│   │   └── ProductController.php
│   ├── Requests/
│   │   ├── ProductRequest.php
│   │   └── ProductUpdateRequest.php
│   └── Resources/
│       └── ProductResource.php
├── Models/
│   └── Product.php
└── database/
    └── migrations/
```

Cada arquivo possui uma única responsabilidade.

---

# Fluxo da Requisição

Toda requisição segue sempre o mesmo pipeline.

<svg viewBox="0 0 760 180" width="100%" role="img" aria-label="Fluxo da requisição Produto">
  <rect x="20" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="65" y="84" text-anchor="middle" font-size="14">Request</text>

  <path d="M110 80 L170 80" stroke="currentColor" stroke-width="2"/>
  <path d="M170 80 l-8 -6 M170 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="170" y="55" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="225" y="84" text-anchor="middle" font-size="14">FormRequest</text>

  <path d="M280 80 L340 80" stroke="currentColor" stroke-width="2"/>
  <path d="M340 80 l-8 -6 M340 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="340" y="55" width="70" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="375" y="84" text-anchor="middle" font-size="14">DTO</text>

  <path d="M410 80 L470 80" stroke="currentColor" stroke-width="2"/>
  <path d="M470 80 l-8 -6 M470 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="470" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="515" y="84" text-anchor="middle" font-size="14">Service</text>

  <path d="M560 80 L620 80" stroke="currentColor" stroke-width="2"/>
  <path d="M620 80 l-8 -6 M620 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="620" y="55" width="110" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="675" y="84" text-anchor="middle" font-size="14">Repository</text>
</svg>

O Controller nunca conversa diretamente com o banco de dados.

---

# Migration

A migration gerada cria automaticamente a estrutura da tabela.

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('public_id')->unique();
    $table->string('nome');
    $table->text('descricao')->nullable();
    $table->decimal('preco',10,2);
    $table->integer('estoque')->default(0);
    $table->boolean('ativo')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

Execute normalmente.

```bash
php artisan migrate
```

---

# Model

O Model permanece extremamente simples.

```php
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasHash;

    protected $fillable = [
        'nome',
        'descricao',
        'preco',
        'estoque',
        'ativo',
    ];
}
```

Observe que toda lógica permanece fora do Model.

---

# Controller

O Controller apenas conecta as camadas geradas.

```php
class ProductController extends AbstractController
{
    protected mixed $service;

    protected ?string $requestValidate = ProductRequest::class;
    protected ?string $requestValidateUpdate = ProductUpdateRequest::class;
    protected ?string $requestDto = ProductDTO::class;
    protected ?string $requestDtoUpdate = ProductUpdateDTO::class;
    protected ?string $resource = ProductResource::class;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }
}
```

Nenhuma lógica CRUD precisa ser escrita manualmente.

---

# ProductRequest

A validação acontece antes de chegar ao Service.

```php
class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => ['required','string','max:255'],
            'descricao' => ['nullable','string'],
            'preco' => ['required','numeric'],
            'estoque' => ['required','integer'],
            'ativo' => ['boolean'],
        ];
    }
}
```

Erros de validação retornam automaticamente **HTTP 422**.

---

# ProductUpdateRequest

A atualização possui regras independentes.

```php
class ProductUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nome' => ['sometimes','string'],
            'descricao' => ['nullable','string'],
            'preco' => ['sometimes','numeric'],
            'estoque' => ['sometimes','integer'],
            'ativo' => ['boolean'],
        ];
    }
}
```

Isso permite regras diferentes entre criação e edição.

---

# ProductDTO

Após a validação, o Request é convertido para um objeto tipado.

```php
final class ProductDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly ?string $descricao,
        public readonly float $preco,
        public readonly int $estoque,
        public readonly bool $ativo
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            nome: $request->nome,
            descricao: $request->descricao,
            preco: (float) $request->preco,
            estoque: (int) $request->estoque,
            ativo: (bool) $request->ativo,
        );
    }
}
```

DTOs eliminam a dependência direta do Request dentro do domínio.

---

# ProductUpdateDTO

A atualização possui seu próprio DTO.

```php
final class ProductUpdateDTO
{
    public function __construct(
        public readonly ?string $nome,
        public readonly ?string $descricao,
        public readonly ?float $preco,
        public readonly ?int $estoque,
        public readonly ?bool $ativo
    ) {}
}
```

Isso mantém a tipagem consistente.

---

# ProductService

As regras de negócio pertencem ao Service.

O Service recebe o Repository através de injeção de dependência.

```php
class ProductService extends AbstractService
{
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(ProductDTO $dto)
    {
        if ($dto->preco <= 0) {
            throw new DomainException(
                'Preço inválido.'
            );
        }

        return $this->repository->create(
            $dto->toArray()
        );
    }
}
```

O Service permanece totalmente desacoplado da camada HTTP.

---

# ProductRepository

O Repository é responsável pela persistência.

```php
class ProductRepository extends AbstractRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
```

Diversos métodos já estão disponíveis automaticamente.

### Criar

```php
$this->repository->create(
    $dto->toArray()
);
```

### Buscar

```php
$this->repository->findByPublicId(
    $publicId
);
```

### Paginar

```php
$this->repository
    ->where('ativo', true)
    ->orderBy('nome')
    ->paginate();
```

Sem necessidade de escrever consultas repetitivas.

---

# ProductResource

O Resource controla exatamente o que será retornado pela API.

```php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'public_id' => $this->public_id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'preco' => $this->preco,
            'estoque' => $this->estoque,
            'ativo' => $this->ativo,
        ];
    }
}
```

Isso evita expor atributos internos.

---

# Rotas Geradas

As rotas seguem o padrão REST.

| Método | Endpoint |
|--------|----------|
| GET | `/api/products` |
| GET | `/api/products/{public_id}` |
| POST | `/api/products` |
| PUT | `/api/products/{public_id}` |
| DELETE | `/api/products/{public_id}` |

---

# Criando um Produto

**POST** `/api/products`

Request.

```json
{
  "nome": "Notebook Gamer",
  "descricao": "RTX 4070",
  "preco": 7999.90,
  "estoque": 12,
  "ativo": true
}
```

Resposta (**201 Created**).

```json
{
  "type": "success",
  "status": 201,
  "data": {
    "public_id": "01JXYZABCDEF123456",
    "nome": "Notebook Gamer",
    "preco": 7999.90,
    "estoque": 12,
    "ativo": true
  }
}
```

---

# Listando Produtos

**GET** `/api/products`

Resposta.

```json
{
  "data": [
    {
      "public_id": "01JXYZABCDEF123456",
      "nome": "Notebook Gamer"
    }
  ],
  "links": {},
  "meta": {}
}
```

A paginação já vem pronta.

---

# Buscando um Produto

**GET** `/api/products/{public_id}`

Exemplo.

```text
GET /api/products/01JXYZABCDEF123456
```

O Repository resolve automaticamente o identificador público.

---

# Atualizando um Produto

**PUT** `/api/products/{public_id}`

Request.

```json
{
  "preco": 7499.90
}
```

Resposta.

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "public_id": "01JXYZABCDEF123456",
    "preco": 7499.90
  }
}
```

O fluxo utiliza automaticamente `ProductUpdateRequest` e `ProductUpdateDTO`.

---

# Removendo um Produto

**DELETE** `/api/products/{public_id}`

Resposta.

```json
{
  "type": "success",
  "status": 200,
  "message": "Produto removido com sucesso."
}
```

Quando `SoftDeletes` estiver habilitado, o registro será arquivado automaticamente.

---

# Fluxo Completo da Execução

```text
POST /api/products
        │
        ▼
 ProductRequest
        │
        ▼
 ProductDTO
        │
        ▼
 ProductService
        │
        ▼
 ProductRepository
        │
        ▼
 Product Model
        │
        ▼
 ProductResource
        │
        ▼
 JSON Response
```

Cada camada executa apenas sua responsabilidade.

---

# Boas Práticas

- mantenha Controllers enxutos;
- coloque regras de negócio no Service;
- utilize DTOs;
- retorne Resources;
- trabalhe com identificadores públicos;
- deixe o Repository responsável apenas pela persistência.

<Callout type="success">

Este exemplo demonstra exatamente como um desenvolvedor utilizaria o Laravel Domain Generator em um projeto real.

</Callout>