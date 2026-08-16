# Configuração

Todas as opções da biblioteca ficam em:

```text
config/domain-generator.php
```

---

# Arquivo completo

```php
return [

    'auth'=>[

        'guard'=>env(
            'DOMAIN_GENERATOR_GUARD',
            'api'
        ),

        'login_field'=>env(
            'DOMAIN_GENERATOR_LOGIN_FIELD',
            'email'
        ),

    ],

    'identifier'=>[

        'column'=>'hash',

        'strategy'=>env(
            'DOMAIN_GENERATOR_IDENTIFIER',
            'ulid'
        ),

    ],

    'domain_folder'=>env(
        'APP_DOMAIN_FOLDER',
        'Domain'
    ),

];
```

---

# Variáveis de ambiente

## Pasta dos domínios

```env
APP_DOMAIN_FOLDER=Domain
```

Resultado:

```text
app/Domain
```

Ou:

```env
APP_DOMAIN_FOLDER=Modules
```

Resultado:

```text
app/Modules
```

---

## Estratégia do identificador

```env
DOMAIN_GENERATOR_IDENTIFIER=ulid
```

Valores:

- ulid
- uuid
- uuid32

---

## Coluna pública

```env
DOMAIN_GENERATOR_IDENTIFIER_COLUMN=hash
```

---

## Guard JWT

```env
DOMAIN_GENERATOR_GUARD=api
```

---

## Campo de login

```env
DOMAIN_GENERATOR_LOGIN_FIELD=email
```

Também suporta:

- cpf
- username

---

# Publicando configuração

```bash
php artisan vendor:publish --tag=domain-generator-config
```

---

# Recomendações

Para novos projetos recomenda-se:

```env
APP_DOMAIN_FOLDER=Domain

DOMAIN_GENERATOR_IDENTIFIER=ulid

DOMAIN_GENERATOR_GUARD=api

DOMAIN_GENERATOR_LOGIN_FIELD=email
```

Essa configuração oferece melhor compatibilidade com o restante da biblioteca.