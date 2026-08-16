# DTO (Data Transfer Object)

Os DTOs são responsáveis por transportar dados entre o Controller e o Service.

Eles eliminam dependência direta do Request dentro do domínio.

## Criando um DTO

```php
final class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $phone = null,
    ) {}
}
```

## Configurando no Controller

```php
protected ?string $requestDto = UserDTO::class;
```

## Fluxo

<svg viewBox="0 0 260 220" width="100%" role="img" aria-label="Fluxo do DTO">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">FormRequest</text>
  <path d="M130 56 V84" stroke="currentColor"/>
  <rect x="50" y="84" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="107" text-anchor="middle" font-size="13">DTO</text>
  <path d="M130 120 V148" stroke="currentColor"/>
  <rect x="50" y="148" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="171" text-anchor="middle" font-size="13">Service</text>
</svg>

## Salvando

```php
$this->service->saveDto($dto);
```

Internamente:

```text
DTO
 ↓
toArray()
 ↓
Repository
```

## Benefícios

- Tipagem forte.
- Código mais previsível.
- Independência da camada HTTP.
- Melhor autocomplete.
- Facilita testes.