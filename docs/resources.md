# API Resources

O `AbstractController` possui integração automática com Resources.

Isso elimina repetição de código.

---

# Criando um Resource

```bash
php artisan make:resource UserResource
```

---

# Exemplo

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

# Configurando

No Controller:

```php
protected ?string $resource=UserResource::class;
```

Pronto.

---

# Transformações automáticas

| Retorno | Resultado |
|----------|-----------|
| Model | Resource |
| Collection | Resource::collection |
| Paginator | Resource::collection |

---

# Model

Service:

```php
return $user;
```

Resposta:

```json
{
  "data":{
    "hash":"01K...",
    "nome":"Diego"
  }
}
```

---

# Collection

Service:

```php
return User::all();
```

Resposta:

```json
{
  "data":[]
}
```

---

# Paginação

Service:

```php
return User::paginate();
```

O Resource preserva automaticamente:

- links
- meta
- current_page
- last_page

---

# Fluxo

<svg viewBox="0 0 260 260" width="100%" role="img" aria-label="Fluxo dos Resources">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">Model</text>
  <path d="M130 56 V92" stroke="currentColor"/>
  <rect x="50" y="92" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="115" text-anchor="middle" font-size="13">Resource</text>
  <path d="M130 128 V164" stroke="currentColor"/>
  <rect x="50" y="164" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="187" text-anchor="middle" font-size="13">JSON</text>
</svg>

---

# Boas práticas

- Nunca retorne arrays manualmente.
- Centralize transformação no Resource.
- Utilize Resources mesmo em listas.