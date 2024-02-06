# Base de Dados
Os plugins podem configurar seu próprio banco de dados, por exemplo, o conteúdo de `plugin/foo/config/database.php` é o seguinte:
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'database',
            'username'    => 'username',
            'password'    => 'password',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
A forma de referenciar é `Db::connection('plugin.{plugin}.{nome da conexão}');`, por exemplo:
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Se quiser usar o banco de dados do projeto principal, simplesmente use como no exemplo abaixo:
```php
use support\Db;
Db::table('user')->first();
// Supondo que o projeto principal também configurou uma conexão chamada admin
Db::connection('admin')->table('admin')->first();
```

## Configurar o Banco de Dados para o Model

Podemos criar uma classe Base para o Model, onde a classe Base utiliza a propriedade `$connection` para especificar a conexão do banco de dados do próprio plugin, por exemplo:

```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.foo.mysql';

}
```

Dessa forma, todos os Models do plugin herdam da classe Base e automaticamente utilizam o banco de dados do próprio plugin.

## Reutilização da Configuração do Banco de Dados
Claro que podemos reutilizar a configuração do banco de dados do projeto principal. Se estiver usando o [webman-admin](https://www.workerman.net/plugin/82), também é possível reutilizar a configuração do banco de dados do [webman-admin](https://www.workerman.net/plugin/82), por exemplo:
```php
<?php

namespace plugin\foo\app\model;

use DateTimeInterface;
use support\Model;

class Base extends Model
{
    /**
     * @var string
     */
    protected $connection = 'plugin.admin.mysql';

}
```
