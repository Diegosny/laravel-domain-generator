# AbstractController

<VersionBadge version="v1.1.0"/>

O `AbstractController` é a base de todos os Controllers gerados.

Ele padroniza respostas JSON e elimina código repetitivo.

---

## Recursos

- Respostas JSON padronizadas
- Helper de sucesso
- Helper de erro
- Suporte a HTTP Status
- Estrutura consistente

---

## Resposta de Sucesso

```php
return $this->success($user);
```

Resposta:

```json
{
  "type": "success",
  "status": 200,
  "data": {
    "name": "João"
  }
}
```

---

## Resposta de Erro

```php
return $this->error('Usuário não encontrado.', 404);
```

Resposta:

```json
{
  "type": "error",
  "status": 404,
  "message": "Usuário não encontrado."
}
```

---

## Por que utilizar?

Todos os Controllers seguem exatamente o mesmo padrão de resposta, facilitando integração com frontend e documentação.

<Callout type="success">

Todos os Controllers gerados estendem `AbstractController`.

</Callout>