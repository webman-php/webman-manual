# Arquivo de configuração

## Localização
O arquivo de configuração do webman está localizado no diretório `config/` e pode ser acessado no projeto utilizando a função `config()`.

## Obtenção de configuração

Obter todas as configurações
```php
config();
```

Obter todas as configurações de `config/app.php`
```php
config('app');
```

Obter a configuração `debug` de `config/app.php`
```php
config('app.debug');
```

Se a configuração for um array, pode-se utilizar `.` para obter os valores internos do array, por exemplo
```php
config('file.key1.key2');
```

## Valor padrão
```php
config($key, $default);
```
A função config passa o valor padrão como segundo parâmetro. Se a configuração não existir, retorna o valor padrão. Se a configuração não existir e nenhum valor padrão for definido, retorna null.

## Configuração personalizada
Os desenvolvedores podem adicionar seus próprios arquivos de configuração no diretório `config/`, por exemplo

**config/payment.php**
```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**Usando ao obter a configuração**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## Alteração de configuração
O webman não suporta a alteração dinâmica de configurações. Todas as configurações devem ser modificadas manualmente nos respectivos arquivos de configuração e depois reload ou restart para que tenham efeito.

> **Nota**
> As configurações do servidor `config/server.php` e as configurações de processo `config/process.php` não suportam reload e precisam de restart para serem efetivas.
