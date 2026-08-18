# Complete Product Example

<VersionBadge version="v1.1.0"/>

This guide demonstrates the complete workflow of Laravel Domain Generator using a **Product** domain.

By the end of this guide you'll understand how an HTTP request travels through every generated layer, from the API endpoint to the Repository.

<Callout type="info">

This is the most complete example in the documentation and represents the recommended workflow for new projects.

</Callout>

---

# Final Result

Running a single Artisan command generates an entire production-ready architecture.

```bash
php artisan make:domain Product
```

Generated structure.

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

Each file has a single responsibility.

---

# Request Lifecycle

Every request follows the same execution pipeline.

<svg viewBox="0 0 760 180" width="100%" role="img" aria-label="Product request lifecycle">
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

The Controller never communicates directly with the database.

---

# Migration

```php
Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('public_id')->unique();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price',10,2);
    $table->integer('stock')->default(0);
    $table->boolean('active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

Run:

```bash
php artisan migrate
```

---

# Product Model

```php
class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasHash;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'active',
    ];
}
```

Notice that business rules remain outside the Model.

---

# Product Controller

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

No CRUD logic needs to be written manually.

---

# ProductRequest

```php
class ProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'description' => ['nullable','string'],
            'price' => ['required','numeric'],
            'stock' => ['required','integer'],
            'active' => ['boolean'],
        ];
    }
}
```

Validation errors automatically return **HTTP 422**.

---

# ProductUpdateRequest

```php
class ProductUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes','string'],
            'description' => ['nullable','string'],
            'price' => ['sometimes','numeric'],
            'stock' => ['sometimes','integer'],
            'active' => ['boolean'],
        ];
    }
}
```

---

# ProductDTO

```php
final class ProductDTO
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly float $price,
        public readonly int $stock,
        public readonly bool $active
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->name,
            description: $request->description,
            price: (float) $request->price,
            stock: (int) $request->stock,
            active: (bool) $request->active,
        );
    }
}
```

DTOs remove the dependency on raw HTTP Requests.

---

# ProductUpdateDTO

```php
final class ProductUpdateDTO
{
    public function __construct(
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?float $price,
        public readonly ?int $stock,
        public readonly ?bool $active
    ) {}
}
```

---

# ProductService

Business rules belong here.

The Service receives the Repository through dependency injection.

```php
class ProductService extends AbstractService
{
    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function create(ProductDTO $dto)
    {
        if ($dto->price <= 0) {
            throw new DomainException(
                'Invalid product price.'
            );
        }

        return $this->repository->create(
            $dto->toArray()
        );
    }
}
```

The Service remains completely independent from HTTP.

---

# ProductRepository

The Repository owns persistence.

```php
class ProductRepository extends AbstractRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }
}
```

Several methods are already inherited.

### Create

```php
$this->repository->create(
    $dto->toArray()
);
```

### Find

```php
$this->repository->findByPublicId(
    $publicId
);
```

### Paginate

```php
$this->repository
    ->where('active', true)
    ->orderBy('name')
    ->paginate();
```

No repetitive query code is required.

---

# ProductResource

```php
class ProductResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'public_id' => $this->public_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'active' => $this->active,
        ];
    }
}
```

Resources prevent exposing internal attributes.

---

# Generated Routes

| Method | Endpoint |
|--------|----------|
| GET | `/api/products` |
| GET | `/api/products/{public_id}` |
| POST | `/api/products` |
| PUT | `/api/products/{public_id}` |
| DELETE | `/api/products/{public_id}` |

---

# Create a Product

**POST** `/api/products`

Request.

```json
{
  "name": "Gaming Laptop",
  "description": "RTX 4070",
  "price": 7999.90,
  "stock": 12,
  "active": true
}
```

Response (**201 Created**).

```json
{
  "type": "success",
  "status": 201,
  "data": {
    "public_id": "01JXYZABCDEF123456",
    "name": "Gaming Laptop",
    "price": 7999.90,
    "stock": 12,
    "active": true
  }
}
```

---

# List Products

**GET** `/api/products`

Response.

```json
{
  "data": [
    {
      "public_id": "01JXYZABCDEF123456",
      "name": "Gaming Laptop"
    }
  ],
  "links": {},
  "meta": {}
}
```

Pagination is generated automatically.

---

# Get a Product

**GET** `/api/products/{public_id}`

Example.

```text
GET /api/products/01JXYZABCDEF123456
```

The Repository automatically resolves the public identifier.

---

# Update a Product

**PUT** `/api/products/{public_id}`

Request.

```json
{
  "price": 7499.90
}
```

Response.

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "public_id": "01JXYZABCDEF123456",
    "price": 7499.90
  }
}
```

The update pipeline automatically uses `ProductUpdateRequest` and `ProductUpdateDTO`.

---

# Delete a Product

**DELETE** `/api/products/{public_id}`

Response.

```json
{
  "type": "success",
  "status": 200,
  "message": "Product removed successfully."
}
```

When `SoftDeletes` is enabled, the record is automatically archived.

---

# Complete Execution Flow

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

Every layer has a single responsibility.

---

# Best Practices

- Keep Controllers thin.
- Place business rules inside Services.
- Use DTOs instead of raw Requests.
- Return Resources.
- Use public identifiers.
- Keep Repositories focused only on persistence.

<Callout type="success">

This example demonstrates exactly how a developer would use Laravel Domain Generator in a real production project.

</Callout>