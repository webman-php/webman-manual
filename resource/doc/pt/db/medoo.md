## Medoo

Medoo é um plug-in de operação de banco de dados leve, [site oficial do Medoo](https://medoo.in/).

## Instalação
`composer require webman/medoo`

## Configuração do Banco de Dados
O arquivo de configuração está localizado em `config/plugin/webman/medoo/database.php`.

## Utilização
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Medoo\Medoo;

class Index
{
    public function index(Request $request)
    {
        $user = Medoo::get('user', '*', ['uid' => 1]);
        return json($user);
    }
}
```

> **Dica**
> `Medoo::get('user', '*', ['uid' => 1]);`
> É equivalente a
> `Medoo::instance('default')->get('user', '*', ['uid' => 1]);`

## Configuração de Múltiplos Bancos de Dados

**Configuração**
Adicione uma nova configuração em `config/plugin/webman/medoo/database.php`, com a chave sendo qualquer uma, neste caso está sendo usada `other`.

```php
<?php
return [
    'default' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
    // Aqui foi adicionada uma configuração 'other'
    'other' => [
        'type' => 'mysql',
        'host' => 'localhost',
        'database' => 'database',
        'username' => 'username',
        'password' => 'password',
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'port' => 3306,
        'prefix' => '',
        'logging' => false,
        'error' => PDO::ERRMODE_EXCEPTION,
        'option' => [
            PDO::ATTR_CASE => PDO::CASE_NATURAL
        ],
        'command' => [
            'SET SQL_MODE=ANSI_QUOTES'
        ]
    ],
];
```

**Utilização**
```php
$user = Medoo::instance('other')->get('user', '*', ['uid' => 1]);
```

## Documentação Detalhada
Consulte a [documentação oficial do Medoo](https://medoo.in/api/select).
