# Aplicaciones de plugins
Cada aplicación de plugin es una aplicación completa, cuyo código fuente se encuentra en el directorio `{proyecto principal}/plugin`.

> **Consejo**
> Utilizando el comando `php webman app-plugin:create {nombre del plugin}` (requiere webman/console>=1.2.16) puedes crear un plugin de aplicación localmente,
> por ejemplo, `php webman app-plugin:create cms` creará la siguiente estructura de directorios

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

Podemos observar que un plugin de aplicación tiene una estructura de directorio y archivos de configuración similar a la de webman. De hecho, la experiencia de desarrollar un plugin de aplicación es básicamente la misma que desarrollar un proyecto webman, solo necesitas prestar atención a los siguientes aspectos.

## Espacios de nombres
El directorio y el nombre del plugin siguen la especificación PSR4. Dado que los plugins se almacenan en el directorio de plugins, todos los espacios de nombres comienzan con "plugin", por ejemplo `plugin\cms\app\controller\UserController`, donde "cms" es el directorio principal del código fuente.

## Acceso a URL
Las URLs de acceso a los plugins de aplicación comienzan con `/app`, por ejemplo, la URL del controlador `plugin\cms\app\controller\UserController` es `http://127.0.0.1:8787/app/cms/user`.

## Archivos estáticos
Los archivos estáticos se encuentran en `plugin/{nombre del plugin}/public`, por ejemplo, al acceder a `http://127.0.0.1:8787/app/cms/avatar.png`, en realidad se está obteniendo el archivo `plugin/cms/public/avatar.png`.

## Archivos de configuración
La configuración de los plugins es similar a la de un proyecto webman normal, pero generalmente solo afecta al plugin actual y no al proyecto principal.
Por ejemplo, el valor de `plugin.cms.app.controller_suffix` solo afecta el sufijo del controlador del plugin, y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.cms.app.controller_reuse` solo afecta si el controlador del plugin se reutiliza, y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.cms.middleware` solo afecta al middleware del plugin, y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.cms.view` solo afecta a la vista utilizada por el plugin, y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.cms.container` solo afecta al contenedor utilizado por el plugin, y no afecta al proyecto principal.
Por ejemplo, el valor de `plugin.cms.exception` solo afecta a la clase de manejo de excepciones del plugin, y no afecta al proyecto principal.

Sin embargo, dado que las rutas son globales, la configuración de las rutas del plugin también tiene un impacto global.

## Obtener configuración
Para obtener la configuración de un plugin específico, utiliza el método `config('plugin.{nombre del plugin}.{configuración específica}');`, por ejemplo, para obtener todas las configuraciones de `plugin/cms/config/app.php` se utiliza `config('plugin.cms.app')`.
De la misma manera, el proyecto principal u otros plugins pueden utilizar `config('plugin.cms.xxx')` para obtener la configuración del plugin "cms".

## Configuraciones no admitidas
Los plugins de aplicación no admiten las configuraciones `server.php`, `session.php`, `app.request_class`, `app.public_path` y `app.runtime_path`.

## Base de datos
Los plugins pueden configurar su propia base de datos. Por ejemplo, el contenido de `plugin/cms/config/database.php` es el siguiente
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql es el nombre de la conexión
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base de datos',
            'username'    => 'nombre de usuario',
            'password'    => 'contraseña',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin es el nombre de la conexión
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base de datos',
            'username'    => 'nombre de usuario',
            'password'    => 'contraseña',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
Se accede de la siguiente manera `Db::connection('plugin.{nombre del plugin}.{nombre de la conexión}');`, por ejemplo
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Si deseas utilizar la base de datos del proyecto principal, simplemente úsala, por ejemplo
```php
use support\Db;
Db::table('user')->first();
// Suponiendo que el proyecto principal también tenga una conexión "admin"
Db::connection('admin')->table('admin')->first();
```

> **Consejo**
> El uso de thinkorm es similar.

## Redis
El uso de Redis es similar al de la base de datos. Por ejemplo, en `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
Se utiliza de la siguiente manera
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Del mismo modo, si se quiere reutilizar la configuración de Redis del proyecto principal
```php
use support\Redis;
Redis::get('key');
// Suponiendo que el proyecto principal también tenga una conexión "cache"
Redis::connection('cache')->get('key');
```

## Registros
El uso de la clase de registros es similar al de la base de datos
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Si se desea reutilizar la configuración de registros del proyecto principal, simplemente utilízala
```php
use support\Log;
Log::info('Contenido del registro');
// Suponiendo que el proyecto principal tenga una configuración de registro "test"
Log::channel('test')->info('Contenido del registro');
```

# Instalación y desinstalación de aplicaciones de plugins
Para instalar una aplicación de plugin, simplemente copia el directorio del plugin en el directorio `{proyecto principal}/plugin`, luego necesitas recargar o reiniciar para que surta efecto.
Para desinstalar, simplemente elimina el directorio del plugin correspondiente en `{proyecto principal}/plugin`.
