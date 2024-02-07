# Começando

O webman usa por padrão o [illuminate/database](https://github.com/illuminate/database), que é o [banco de dados do Laravel](https://learnku.com/docs/laravel/8.x/database/9400), e seu uso é semelhante ao Laravel.

Claro, você pode consultar a seção [Usando outros componentes de banco de dados](others.md) para usar o ThinkPHP ou outro banco de dados.

## Instalação

`composer require -W illuminate/database illuminate/pagination illuminate/events symfony/var-dumper`

Após a instalação, é necessário reiniciar (reload não é eficaz).

> **Dica**
> Se não precisar de paginação, eventos de banco de dados ou impressão de SQL, basta executar
> `composer require -W illuminate/database`

## Configuração do Banco de Dados
`config/database.php`
```php

return [
    // Banco de dados padrão
    'default' => 'mysql',

    // Configurações de vários bancos de dados
    'connections' => [
        'mysql' => [
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'test',
            'username'    => 'root',
            'password'    => '',
            'unix_socket' => '',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'prefix'      => '',
            'strict'      => true,
            'engine'      => null,
            'options' => [
                \PDO::ATTR_TIMEOUT => 3
            ]
        ],
    ],
];
```


## Uso
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function db(Request $request)
    {
        $default_uid = 29;
        $uid = $request->get('uid', $default_uid);
        $name = Db::table('users')->where('uid', $uid)->value('username');
        return response("olá $name");
    }
}
```
