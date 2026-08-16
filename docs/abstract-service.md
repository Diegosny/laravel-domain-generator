# AbstractService

<VersionBadge version="v1.1.0"/>

The `AbstractService` centralizes business logic while keeping Controllers lightweight.

---

## Responsibilities

- Business rules
- Validation orchestration
- Repository interaction
- Transactions
- Reusable methods

---

## Example

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

## Benefits

- Thin Controllers
- Testable services
- Centralized business logic
- Better maintainability

<Callout type="info">

Services communicate with Repositories instead of directly accessing models.

</Callout>