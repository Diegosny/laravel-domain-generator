# AbstractService

O `AbstractService` centraliza as regras da aplicação.

Toda lógica de negócio deve ficar aqui.

---

## Responsabilidades

- regras de negócio
- validações de domínio
- hooks
- comunicação com Repository

---

## Fluxo

<svg viewBox="0 0 260 260" width="100%" role="img" aria-label="Fluxo do AbstractService">
  <rect x="50" y="20" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="43" text-anchor="middle" font-size="13">Controller</text>
  <path d="M130 56 V92" stroke="currentColor"/>
  <rect x="50" y="92" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="115" text-anchor="middle" font-size="13">Service</text>
  <path d="M130 128 V164" stroke="currentColor"/>
  <rect x="50" y="164" width="160" height="36" rx="10" fill="none" stroke="currentColor"/>
  <text x="130" y="187" text-anchor="middle" font-size="13">Repository</text>
</svg>

---

# Métodos

## getAll()

```php
$this->service->getAll();
```

---

## find()

Aceita:

- ID
- Hash

```php
$this->service->find($hash);
```

---

## save()

```php
$this->service->save($data);
```

---

## saveDto()

```php
$this->service->saveDto($dto);
```

---

## update()

```php
$this->service->update($id, $data);
```

---

## updateDto()

```php
$this->service->updateDto($id, $dto);
```

---

## delete()

```php
$this->service->delete($id);
```

---

## create()

Criação direta.

---

## updateOrCreate()

```php
$this->service->updateOrCreate(
    ['email'=>$email],
    $data
);
```

---

# Hooks

Os hooks permitem executar código antes e depois das operações.

| Hook | Momento |
|-------|---------|
| beforeSave | Antes de salvar |
| afterSave | Após salvar |
| beforeUpdate | Antes de atualizar |
| afterUpdate | Após atualizar |
| beforeDelete | Antes de excluir |
| afterDelete | Após excluir |

Exemplo:

```php
public function beforeSave(array $data): array
{
    $data['empresa_id'] = auth()->id();

    return $data;
}
```

---

# Usuário autenticado

O Service fornece:

```php
$this->getUserAuth();
```

Sem precisar acessar `Auth` diretamente.