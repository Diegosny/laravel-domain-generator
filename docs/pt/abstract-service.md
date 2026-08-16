# AbstractService

<VersionBadge version="v1.1.0"/>

O `AbstractService` representa a camada de negócio do Laravel Domain Generator.

Os Controllers nunca devem conter regras de negócio. Toda operação é delegada ao Service, que coordena DTOs, Repositories, transações e regras do domínio.

<Callout type="info">

Todo Service gerado pelo comando `php artisan make:domain` estende automaticamente essa classe.

</Callout>

---

## Visão Geral

Os Services gerados oferecem um local consistente para concentrar a lógica de negócio, mantendo os Controllers extremamente enxutos.

Responsabilidades:

- regras de negócio
- orquestração do Repository
- processamento de DTOs
- transações
- suporte a identificadores públicos
- paginação
- carregamento de relacionamentos
- operações reutilizáveis do domínio

Exemplo mínimo:

```php
class UserService extends AbstractService
{
    public function __construct(UserRepository $repository)
    {
        parent::__construct($repository);
    }
}
```

---

## Ciclo de Execução

Toda operação segue sempre o mesmo fluxo.

<svg viewBox="0 0 760 180" width="100%" role="img" aria-label="Ciclo do AbstractService">
  <rect x="20" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="65" y="84" text-anchor="middle" font-size="14">Controller</text>

  <path d="M110 80 L165 80" stroke="currentColor" stroke-width="2"/>
  <path d="M165 80 l-8 -6 M165 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="165" y="55" width="70" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="200" y="84" text-anchor="middle" font-size="14">DTO</text>

  <path d="M235 80 L310 80" stroke="currentColor" stroke-width="2"/>
  <path d="M310 80 l-8 -6 M310 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="310" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="355" y="84" text-anchor="middle" font-size="14">Service</text>

  <path d="M400 80 L480 80" stroke="currentColor" stroke-width="2"/>
  <path d="M480 80 l-8 -6 M480 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="480" y="55" width="100" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="530" y="84" text-anchor="middle" font-size="14">Repository</text>

  <path d="M580 80 L650 80" stroke="currentColor" stroke-width="2"/>
  <path d="M650 80 l-8 -6 M650 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="650" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="695" y="84" text-anchor="middle" font-size="14">Model</text>
</svg>

O Service funciona como a camada de orquestração entre HTTP e persistência.

---

## Propriedades protegidas

### `$repository`

```php
protected AbstractRepository $repository;
```

Armazena a instância do Repository injetada no construtor.

Toda operação CRUD chega ao Repository através dessa propriedade.

Exemplo:

```php
public function __construct(UserRepository $repository)
{
    parent::__construct($repository);
}
```

Isso garante tipagem consistente em todos os Services gerados.

---

## Construtor

```php
public function __construct(AbstractRepository $repository)
```

O construtor registra automaticamente o Repository utilizado pelo Service.

Fluxo:

1. Repository é injetado.
2. O construtor da classe pai o armazena.
3. Os métodos CRUD ficam disponíveis imediatamente.

---

## Métodos CRUD

### `create()`

Cria um novo registro utilizando um DTO.

Exemplo:

```php
$user = $service->create($dto);
```

Fluxo:

```text
DTO
 ↓
Service
 ↓
Repository
 ↓
Model::create()
```

Responsabilidades típicas:

- validar regras de negócio;
- executar transações;
- delegar persistência.

O Service nunca deve receber Requests diretamente.

---

### `update()`

Atualiza um registro existente.

Exemplo:

```php
$service->update($publicId, $dto);
```

Fluxo:

1. Resolve o identificador público.
2. Aplica regras de negócio.
3. Persiste as alterações.

---

### `delete()`

Remove um registro.

Quando o Model utiliza:

```php
use SoftDeletes;
```

o comportamento é preservado automaticamente.

---

### `find()`

Recupera uma entidade.

Exemplo:

```php
$user = $service->find($publicId);
```

Sempre que possível, trabalha com identificadores públicos.

---

### `findOrFail()`

Equivalente ao `findOrFail()` do Laravel, mas mantendo a responsabilidade centralizada no Repository.

Se o registro não existir, uma exceção padronizada é lançada.

---

### `paginate()`

Retorna uma coleção paginada.

Exemplo:

```php
return $service->paginate();
```

O Controller permanece desacoplado da implementação do Repository.

A resposta já contém:

- `data`
- `links`
- `meta`

---

## Suporte a Identificadores Públicos

Os Services utilizam identificadores públicos em vez de expor IDs internos.

Suporta:

- ULID
- UUID
- UUID32
- identificadores hash personalizados

Exemplo:

```text
GET /api/users/01JXYZABCDEF123456
```

A resolução é delegada ao Repository.

---

## Transações

Uma das principais responsabilidades do Service é controlar transações.

Exemplo:

```php
DB::transaction(function () use ($dto) {
    $this->repository->create($dto->toArray());
});
```

Benefícios:

- operações atômicas;
- rollback automático;
- maior segurança para regras complexas.

Sempre que houver múltiplas gravações relacionadas, elas devem ficar aqui.

---

## Regras de Negócio

O Service é o lugar correto para implementar regras do domínio.

Exemplo:

```php
if (! $user->ativo) {
    throw new DomainException();
}
```

Evite colocar essas regras em:

- Controllers;
- Repositories;
- Resources.

Assim o domínio permanece reutilizável.

---

## Delegação para o Repository

O Service nunca acessa o banco diretamente.

Fluxo:

```text
Controller
 ↓
Service
 ↓
Repository
 ↓
Banco de Dados
```

Exemplo:

```php
$this->repository->create($dto->toArray());
```

Isso torna a persistência substituível.

---

## Carregamento de Relacionamentos

O Service pode solicitar eager loading através do Repository.

Exemplo:

```php
$this->repository->with([
    'municipio'
]);
```

Vantagens:

- menos consultas;
- respostas previsíveis.

---

## Fluxo da Paginação

A paginação sempre segue o mesmo pipeline.

```text
Controller
 ↓
Service
 ↓
Repository::paginate()
 ↓
Paginator
 ↓
Resource Collection
```

O Controller nunca precisa montar a paginação manualmente.

---

## Tratamento de Erros

As exceções de negócio permanecem dentro da camada Service.

Exemplo:

```php
throw new DomainException(
    'Usuários inativos não podem executar esta operação.'
);
```

Posteriormente, o Controller converte essa exceção para o formato JSON padronizado.

---

## Boas Práticas

Recomendado:

- receber DTOs;
- chamar Repositories;
- executar transações;
- validar regras do domínio.

Evite:

- receber Requests;
- retornar Responses HTTP;
- consultar Models diretamente.

<Callout type="success">

O Service gerado mantém a lógica de negócio isolada da camada HTTP e da persistência, tornando o domínio mais testável e muito mais fácil de manter.

</Callout>