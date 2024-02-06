# Script personalizado

A veces necesitamos escribir algunos scripts temporales, en los cuales podemos llamar a cualquier clase o interfaz de la misma manera que en webman, para realizar operaciones como importación de datos, actualización de datos, estadísticas, etc. Esto es muy fácil de hacer en webman, por ejemplo:

**Crear `scripts/update.php`** (si el directorio no existe, créelo usted mismo)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

Por supuesto, también podemos usar el `webman/console` para personalizar comandos y realizar este tipo de operaciones, consulte [Línea de comandos](../plugin/console.md) para más información.
