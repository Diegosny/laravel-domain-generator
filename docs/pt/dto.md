# DTO

<VersionBadge version="v1.1.0"/>

Os Data Transfer Objects (DTOs) permitem transportar dados validados de forma limpa pela aplicação.

Em vez de passar `Request` diretamente para Services, os domínios gerados utilizam DTOs tipados.

---

## Por que usar DTO?

- Separação da camada HTTP
- Tipagem forte
- Services mais limpos
- Facilidade para testes

---

## DTO Gerado

```php
class UserDTO extends AbstractDTO
{
    public function __construct(
        public readonly string $nome,
        public readonly string $email,
        public readonly bool $ativo
    ) {}
}
```

---

## Criando a partir do Request

```php
$dto = UserDTO::fromRequest($request);
```

Os dados validados são preenchidos automaticamente.

---

## Enviando ao Service

```php
$this->service->create($dto);
```

---

## Convertendo para Array

```php
$dto->toArray();
```

Resultado:

```php
[
    'nome' => 'João',
    'email' => 'joao@email.com',
    'ativo' => true
]
```

<Callout type="success">

Os Services gerados recebem DTOs, nunca Requests diretamente.

</Callout>