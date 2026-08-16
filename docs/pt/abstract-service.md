# AbstractService

<VersionBadge version="v1.1.0"/>

O `AbstractService` concentra toda a regra de negócio da aplicação.

Controllers ficam responsáveis apenas por receber a requisição e devolver a resposta.

---

## Responsabilidades

- Regras de negócio
- Orquestração
- Comunicação com Repository
- Transações
- Métodos reutilizáveis

---

## Exemplo

```php
public function create(UserDTO $dto): User
{
    return $this->repository->create($dto->toArray());
}
```

Controller:

```php
public function store(StoreUserRequest $request)
{
    return $this->success(
        $this->service->create(
            UserDTO::fromRequest($request)
        )
    );
}
```

---

## Benefícios

- Controllers enxutos
- Fácil de testar
- Regra de negócio centralizada
- Maior manutenção

<Callout type="info">

Services conversam com Repositories, nunca diretamente com o Model.

</Callout>