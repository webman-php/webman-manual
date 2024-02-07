# Script personalizado

A veces necesitamos escribir algunos scripts temporales, en los que podemos llamar a cualquier clase o interfaz como lo hacemos en webman, para completar operaciones como importación de datos, actualización de datos y estadísticas, entre otras. En webman, esto es algo muy fácil de hacer, por ejemplo:

**Crear `scripts/update.php`** (create the directory if it doesn't exist)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Por supuesto, también podemos usar el comando personalizado `webman/console` para realizar este tipo de operaciones, consulte [Línea de comandos](../plugin/console.md)
