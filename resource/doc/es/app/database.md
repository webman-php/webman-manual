# Base de datos
Los complementos pueden configurar su propia base de datos, por ejemplo, el contenido de `plugin/foo/config/database.php` es el siguiente:

```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql es el nombre de la conexión
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base_de_datos',
            'username'    => 'nombre_de_usuario',
            'password'    => 'contraseña',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin es el nombre de la conexión
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base_de_datos',
            'username'    => 'nombre_de_usuario',
            'password'    => 'contraseña',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```

La forma de usar es `Db::connection('plugin.{plugin}.{nombre_de_conexión}');`, por ejemplo:

```php
use support\Db;
Db::connection('plugin.foo.mysql')->table('user')->first();
Db::connection('plugin.foo.admin')->table('admin')->first();
```

Si desea usar la base de datos del proyecto principal, simplemente úsela directamente, por ejemplo:

```php
use support\Db;
Db::table('user')->first();
// Suponiendo que el proyecto principal también tiene una conexión admin configurada
Db::connection('admin')->table('admin')->first();
```

## Configurar la base de datos para el Modelo
Podemos crear una clase Base para el Modelo, donde la clase Base utiliza `$connection` para especificar la conexión a la base de datos del complemento, por ejemplo:

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

De esta manera, todos los Modelos dentro del complemento heredan de la clase Base y automáticamente usan la base de datos del complemento.

## Reutilizar la configuración de la base de datos
Por supuesto, podemos reutilizar la configuración de la base de datos del proyecto principal. Si se ha incorporado [webman-admin](https://www.workerman.net/plugin/82), también podemos reutilizar la configuración de la base de datos de [webman-admin](https://www.workerman.net/plugin/82), por ejemplo:

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
