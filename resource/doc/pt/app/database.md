# Base de dados
Os plugins podem configurar seu próprio banco de dados, por exemplo, o conteúdo de `plugin/foo/config/database.php` é o seguinte:
```php
return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'banco_de_dados',
            'username'    => 'nome_de_usuário',
            'password'    => 'senha',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin é o nome da conexão
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'banco_de_dados',
            'username'    => 'nome_de_usuário',
            'password'    => 'senha',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
A forma de referenciar é `Db::connection('plugin.{plugin}.{nome_da_conexão}');`, por exemplo
```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Se deseja usar o banco de dados do projeto principal, simplesmente use, por exemplo
```php
use support\Db;
Db::table('user')->first();
// Supondo que o projeto principal também tenha configurado uma conexão admin
Db::connection('admin')->table('admin')->first();
```

## Configurar banco de dados para o Model

Podemos criar uma classe Base para o Model, a classe Base usa `$connection` para especificar a conexão do banco de dados do plugin, por exemplo
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
Dessa forma, todos os Model dentro do plugin herdarão da Base e automaticamente utilizarão o banco de dados do plugin.

## Reutilizar configuração do banco de dados
Claro, podemos reutilizar a configuração do banco de dados do projeto principal. Se estiver usando o [webman-admin](https://www.workerman.net/plugin/82), também pode reutilizar a configuração do banco de dados do [webman-admin](https://www.workerman.net/plugin/82), por exemplo
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
