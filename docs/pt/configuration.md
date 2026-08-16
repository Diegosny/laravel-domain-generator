# Configuração

<VersionBadge version="v1.1.0"/>

A biblioteca pode ser personalizada através do arquivo de configuração.

---

## Publicar Configuração

```bash
php artisan vendor:publish --provider="Domain\DomainGenerator\DomainGeneratorServiceProvider"
```

---

## Arquivo

```text
config/domain-generator.php
```

---

## Autenticação

```php
'auth' => [
    'guard' => 'api',
    'login_field' => 'email',
],
```

---

## Identificadores Públicos

```php
'hash' => [
    'driver' => 'ulid',
],
```

Valores disponíveis:

- `ulid`
- `uuid`
- `uuid32`

---

## Namespace

As classes geradas respeitam automaticamente o namespace da aplicação.

<Callout type="info">

Na maioria dos projetos a configuração padrão já é suficiente.

</Callout>