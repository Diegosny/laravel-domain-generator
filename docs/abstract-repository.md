# AbstractRepository

O `AbstractRepository` fornece uma abstração reutilizável sobre o Eloquent.

Ele suporta:

- ID
- Hash
- Paginação
- Relacionamentos
- Filtros
- CRUD completo

---

## Busca por ID ou Hash

O método `find()` detecta automaticamente o identificador.

```php
$this->repository->find(1);

$this->repository->find('01J6M...');
```

Não é necessário escrever lógica condicional.

---

## all()

```php
$this->repository->all();
```

Retorna paginação automaticamente.

Também aceita:

```php
$this->repository->all(
    filters: request()->query(),
    with: ['roles']
);
```

---

## allWithoutPaginate()

```php
$this->repository->allWithoutPaginate();
```

---

## find()

```php
$this->repository->find($id);
```

Aceita:

- inteiro
- ULID
- UUID

---

## findOrFail()

Lança `ModelNotFoundException`.

---

## create()

```php
$this->repository->create($data);
```

---

## update()

```php
$this->repository->update($entity, $data);
```

O Model é atualizado e retornado automaticamente.

---

## delete()

```php
$this->repository->delete($id);
```

---

## where()

```php
$this->repository->where([
    'status'=>'ativo'
]);
```

---

## findOneWhere()

```php
$this->repository->findOneWhere([
    'email'=>$email
]);
```

---

## updateOrCreate()

```php
$this->repository->updateOrCreate(
    ['email'=>$email],
    $data
);
```

---

# Relacionamentos seguros

O parâmetro `with` é validado automaticamente.

Exemplo:

```http
GET /api/users?with=roles,permissions
```

Relacionamentos inexistentes são ignorados.

Isso evita:

```
Call to undefined relationship
```

---

# Filtros automáticos

O Repository remove automaticamente parâmetros técnicos.

Ignorados:

- page
- per_page
- with
- search
- sort
- order

Restante:

```http
GET /users?status=ativo
```

gera:

```php
where('status','ativo')
```

---

# Campo público

O Repository utiliza:

```php
protected string $idField='hash';
```

Isso permite que toda a biblioteca utilize identificadores públicos sem alterar a chave primária do Model.

---

# Exemplo completo

```php
$users = $this->repository->all(
    filters: request()->query(),
    with: ['municipio']
);

$user = $this->repository->find($hash);

$this->repository->update(
    $user,
    ['ativo'=>true]
);
```

---

# Métodos disponíveis

| Método | Descrição |
|---------|-----------|
| all | Lista paginada |
| allWithoutPaginate | Lista completa |
| find | Busca por ID ou Hash |
| findOrFail | Busca obrigatória |
| create | Criação |
| update | Atualização |
| delete | Exclusão |
| where | Busca por condições |
| findOneWhere | Primeiro resultado |
| updateOrCreate | Atualiza ou cria |
| list | Select |