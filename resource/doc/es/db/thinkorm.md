## ThinkORM

### Instalación de ThinkORM

`composer require -W webman/think-orm`

Después de la instalación, es necesario reiniciar (reload no es válido)

> **Nota**
> Si la instalación falla, puede ser debido al uso de un proxy de composer. Intente ejecutar `composer config -g --unset repos.packagist` para cancelar el proxy de composer.

> [webman/think-orm](https://www.workerman.net/plugin/14) en realidad es un complemento de instalación automatizada para `toptink/think-orm`. Si tu versión de webman es inferior a `1.2` y no puedes usar el complemento, consulta el artículo [Instalación y configuración manual de think-orm](https://www.workerman.net/a/1289).

### Archivo de configuración
Modifica el archivo de configuración según sea necesario `config/thinkorm.php`

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

Los modelos de ThinkOrm heredan de `think\Model`, similar a lo siguiente
```php
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
```bash
php webman make:model nombre_de_tabla
```

> **Nota**
> Este comando requiere la instalación de `webman/console`, el comando de instalación es `composer require webman/console ^1.2.13`

> **Atención**
> Si `make:model` detecta que el proyecto principal está utilizando `illuminate/database`, creará archivos de modelo basados en `illuminate/database` en lugar de thinkorm. En este caso, puedes generar un modelo basado en think-orm forzando con un parámetro adicional `tp`, el comando es similar a `php webman make:model nombre_de_tabla tp` (si no funciona, actualiza `webman/console`)
