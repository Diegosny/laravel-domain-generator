# Exemplo: Município

Este exemplo demonstra como trabalhar com relacionamentos utilizando o parâmetro `with`.

---

## Model

```php
class Municipio extends Model
{
    use HasHash;

    public function usuarios()
    {
        return $this->hasMany(User::class);
    }
}
```

---

## Buscando usuários

```http
GET /api/municipios/01K2...?with=usuarios
```

Resposta:

```json
{
  "data":{
    "hash":"01K2...",
    "nome":"Florianópolis",
    "usuarios":[]
  }
}
```

---

## Relacionamentos seguros

Caso seja enviado:

```http
GET /api/municipios?with=teste
```

o Repository ignora automaticamente relacionamentos inexistentes, evitando erros 500.