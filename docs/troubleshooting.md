# Troubleshooting

Esta página reúne os erros mais comuns encontrados durante o uso da biblioteca.

---

## JWT retorna 401

### Sintoma

```json
{
  "message":"Unauthenticated."
}
```

### Solução

Execute:

```bash
php artisan jwt:secret
```

Verifique também:

```php
'guards'=>[
    'api'=>[
        'driver'=>'jwt'
    ]
];
```

---

## User precisa estender Authenticatable

### Erro

```
validateCredentials()
```

### Solução

Troque:

```php
class User extends Model
```

por:

```php
class User extends Authenticatable
```

---

## Resource retorna vazio

### Sintoma

```json
{
  "data":[]
}
```

### Causa

O Controller estava aplicando o Resource sobre um Model já transformado.

### Solução

Atualize para a versão mais recente do `AbstractController`.

---

## Call to undefined relationship

### Erro

```
Call to undefined relationship
```

### Causa

Relacionamento inexistente.

### Solução

Utilize apenas relações válidas.

---

## make:domain não encontra stubs

### Linux

O Linux diferencia:

```
Controller.stub
```

de

```
controller.stub
```

Padronize todos os stubs em minúsculo.

---

## Hash não funciona

Verifique:

```php
use HasHash;
```

e a migration:

```php
$table->char('hash',26)->unique();
```

---

## Rotas de autenticação não aparecem

Execute:

```bash
php artisan route:list
```

Confirme:

```
POST /api/auth/login
```

---

## Limpeza de cache

Sempre que alterar Providers ou configurações:

```bash
composer dump-autoload

php artisan optimize:clear
```