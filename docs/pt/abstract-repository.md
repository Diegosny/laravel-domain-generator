# AbstractRepository

<VersionBadge version="v1.1.0"/>

O `AbstractRepository` representa a camada de persistência do Laravel Domain Generator.

Em vez de espalhar consultas pelo Service ou Controller, toda comunicação com o banco é centralizada no Repository.

<Callout type="info">

Todo Repository criado pelo `php artisan make:domain` estende automaticamente essa classe.

</Callout>

---

## Visão Geral

Os Repositories gerados fornecem uma API reutilizável e previsível para operações de banco de dados.

Recursos disponíveis:

- CRUD completo
- Paginação
- Busca por identificadores públicos
- Eager loading
- Delegação para Query Builder
- Compatibilidade com SoftDeletes
- Filtros dinâmicos
- Ordenação
- Métodos reutilizáveis

Exemplo mínimo:

```php
class UserRepository extends AbstractRepository
{
    public function model(): string
    {
        return User::class;
    }
}
```

---

## Ciclo do Repository

Toda operação segue o mesmo pipeline.

<svg viewBox="0 0 760 180" width="100%" role="img" aria-label="Ciclo do AbstractRepository">
  <rect x="20" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="65" y="84" text-anchor="middle" font-size="14">Service</text>

  <path d="M110 80 L200 80" stroke="currentColor" stroke-width="2"/>
  <path d="M200 80 l-8 -6 M200 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="200" y="55" width="120" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="260" y="84" text-anchor="middle" font-size="14">Repository</text>

  <path d="M320 80 L430 80" stroke="currentColor" stroke-width="2"/>
  <path d="M430 80 l-8 -6 M430 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="430" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="475" y="84" text-anchor="middle" font-size="14">Model</text>

  <path d="M520 80 L630 80" stroke="currentColor" stroke-width="2"/>
  <path d="M630 80 l-8 -6 M630 80 l-8 6" stroke="currentColor" stroke-width="2" fill="none"/>

  <rect x="630" y="55" width="90" height="50" rx="10" fill="none" stroke="currentColor"/>
  <text x="675" y="84" text-anchor="middle" font-size="14">Banco</text>
</svg>

O Repository é responsável exclusivamente pela persistência, enquanto o Service permanece responsável pelas regras de negócio.

---

## Propriedades protegidas

### `$model`

```php
protected Model $model;
```

Armazena automaticamente a instância do Model resolvida pelo método `model()`.

---

## Método obrigatório

### `model()`

```php
public function model(): string
```

Todo Repository deve informar qual Model administra.

Exemplo:

```php
public function model(): string
{
    return User::class;
}
```

Normalmente, esse é o único método obrigatório da implementação.

---

## Métodos CRUD

### `create()`

Cria um novo registro.

Exemplo:

```php
$repository->create([
    'nome' => 'João',
    'email' => 'joao@email.com'
]);
```

Fluxo:

```text
Array
 ↓
Repository
 ↓
Model::create()
 ↓
Banco
```

Retorna o Model criado.

---

### `update()`

Atualiza um registro existente.

Exemplo:

```php
$repository->update($user, [
    'nome' => 'João Atualizado'
]);
```

Fluxo:

1. Resolve a entidade.
2. Preenche atributos.
3. Salva alterações.

---

### `delete()`

Remove um registro.

Quando SoftDeletes está habilitado, a exclusão passa a ser lógica automaticamente.

---

### `restore()`

Restaura registros removidos logicamente.

Exemplo:

```php
$repository->restore($publicId);
```

Muito útil para áreas administrativas.

---

## Métodos de Busca

### `find()`

Recupera uma entidade.

Exemplo:

```php
$user = $repository->find($publicId);
```

Retorna `null` quando não encontra.

---

### `findOrFail()`

Lança exceção quando o registro não existe.

Exemplo:

```php
$user = $repository->findOrFail($publicId);
```

Mantém o comportamento familiar do Laravel, mas com suporte a identificadores públicos.

---

### `findByPublicId()`

Um dos métodos mais importantes da biblioteca.

Em vez de expor IDs internos:

```text
/api/users/15
```

utiliza:

```text
/api/users/01JXYZABCDEF123456
```

Compatível com:

- ULID
- UUID
- UUID32
- hashes personalizados

---

## Métodos de Coleção

### `all()`

Retorna todos os registros.

Exemplo:

```php
$repository->all();
```

Recomendado apenas para conjuntos pequenos.

---

### `paginate()`

Retorna resultados paginados.

Exemplo:

```php
$repository->paginate(15);
```

A resposta já inclui:

- `data`
- `links`
- `meta`

---

## Métodos do Query Builder

O Repository oferece construção fluente de consultas.

---

### `with()`

Carrega relacionamentos antecipadamente.

Exemplo:

```php
$repository->with([
    'municipio'
]);
```

Equivalente a:

```php
User::with('municipio');
```

Benefícios:

- menos consultas;
- respostas previsíveis.

---

### `where()`

Adiciona filtros simples.

Exemplo:

```php
$repository->where('ativo', true);
```

---

### `whereLike()`

Realiza buscas parciais.

Exemplo:

```php
$repository->whereLike('nome', 'João');
```

Equivalente a:

```sql
WHERE nome LIKE '%João%'
```

Ideal para pesquisas.

---

### `whereIn()`

Filtra múltiplos valores.

Exemplo:

```php
$repository->whereIn('perfil', [
    'admin',
    'manager'
]);
```

---

### `orderBy()`

Ordena resultados.

Exemplo:

```php
$repository->orderBy('nome');
```

Ordem decrescente:

```php
$repository->orderBy('created_at', 'desc');
```

---

## Encadeamento de Consultas

Um dos maiores benefícios do Repository é o encadeamento fluente.

Exemplo:

```php
$repository
    ->with(['municipio'])
    ->where('ativo', true)
    ->orderBy('nome')
    ->paginate();
```

Legível, reutilizável e expressivo.

---

## Carregamento de Relacionamentos

Os relacionamentos ficam centralizados.

Exemplo:

```php
$repository->with([
    'municipio',
    'envios'
]);
```

Sem necessidade de repetir consultas nos Services.

---

## Fluxo da Paginação

A paginação segue sempre o mesmo pipeline.

```text
Service
 ↓
Repository
 ↓
Query Builder
 ↓
Paginator
 ↓
Resource Collection
```

Os Controllers recebem uma estrutura padronizada automaticamente.

---

## SoftDeletes

Os Repositories gerados possuem suporte completo ao SoftDeletes.

Exemplo:

```php
use SoftDeletes;
```

Operações disponíveis:

- `delete()`
- `restore()`
- consultas em registros removidos
- `forceDelete()` (quando implementado)

---

## Considerações de Performance

Prefira:

- `with()`
- `paginate()`
- filtros específicos

Evite:

- carregar relacionamentos desnecessários;
- utilizar `all()` em tabelas muito grandes.

---

## Boas Práticas

Mantenha o Repository responsável apenas pela persistência.

Recomendado:

- construir consultas;
- carregar relacionamentos;
- paginar;
- recuperar entidades.

Evite colocar regras de negócio aqui.

Elas pertencem ao Service.

<Callout type="success">

Os Repositories gerados oferecem uma API consistente para persistência enquanto mantêm a lógica de negócio completamente separada do acesso ao banco de dados.

</Callout>