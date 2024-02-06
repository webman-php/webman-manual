## ThinkORM

### Instalación de ThinkORM

`composer require -W webman/think-orm`

Después de la instalación, es necesario reiniciar (reload no es válido)

> **Consejo**
> Si la instalación falla, podría ser porque estás usando un proxy de composer, intenta ejecutar `composer config -g --unset repos.packagist` para desactivar el proxy de composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) es en realidad un complemento para la instalación automática de `toptink/think-orm`, si tu versión de webman es inferior a `1.2` y no puedes usar el complemento, consulta el artículo [Instalación y configuración manual de think-orm](https://www.workerman.net/a/1289).

### Archivo de configuración
Modifica el archivo de configuración según sea necesario `config/thinkorm.php`.

### Uso

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### Crear un modelo

El modelo ThinkOrm hereda de `think\Model`, similar a lo siguiente
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * La clave primaria asociada con la tabla.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

También puedes usar el siguiente comando para crear un modelo basado en thinkorm
```
php webman make:model nombre_de_tabla
```

> **Consejo**
> Este comando requiere la instalación de `webman/console`, el comando de instalación es `composer require webman/console ^1.2.13`.

> **Nota**
> Si el comando make:model detecta que el proyecto principal está utilizando `illuminate/database`, creará archivos de modelo basados en `illuminate/database` en lugar de thinkorm, en este caso puedes forzar la generación de modelos think-orm pasando un parámetro adicional tp, el comando es similar a `php webman make:model nombre_de_tabla tp` (si no funciona, actualiza `webman/console`).
