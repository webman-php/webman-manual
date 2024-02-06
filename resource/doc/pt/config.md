# Ficheiro de Configuração

## Localização
O ficheiro de configuração do webman está localizado na diretoria `config/`. No projeto, é possível obter a configuração correspondente através da função `config()`.

## Obter Configuração

Obter todas as configurações
```php
config();
```

Obter todas as configurações em `config/app.php`
```php
config('app');
```

Obter a configuração `debug` em `config/app.php`
```php
config('app.debug');
```

Se a configuração for um array, pode-se obter o valor dos elementos internos do array usando `.`, por exemplo
```php
config('file.key1.key2');
```

## Valor Padrão
```php
config($key, $default);
```
Ao passar o segundo parâmetro para a função config, pode-se definir um valor padrão. Se a configuração não existir, o valor padrão será retornado. Se a configuração não existir e nenhum valor padrão foi definido, será retornado `null`.

## Configuração Personalizada
Os desenvolvedores podem adicionar os seus próprios ficheiros de configuração na diretoria `config/`, por exemplo

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Utilização ao obter configuração**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Alterar Configuração
O webman não suporta a alteração dinâmica da configuração. Todas as configurações devem ser modificadas manualmente nos ficheiros de configuração correspondentes e depois recarregadas ou o servidor reiniciado.

> **Nota**
> As configurações do servidor em `config/server.php` e as configurações de processo em `config/process.php` não suportam recarregamento. É necessário reiniciar o servidor para que as alterações entrem em vigor.
