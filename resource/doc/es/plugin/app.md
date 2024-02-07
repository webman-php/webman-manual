# Plugins de aplicación
Cada plugin de aplicación es una aplicación completa, cuyo código fuente se encuentra en el directorio `{proyecto principal}/plugin`.

> **Consejo**
> Utilizando el comando `php webman app-plugin:create {nombre del plugin}` (se requiere webman/console>=1.2.16) puedes crear un plugin de aplicación local. Por ejemplo, `php webman app-plugin:create cms` creará la siguiente estructura de directorios:

```plaintext
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

Observamos que un plugin de aplicación tiene la misma estructura de directorios y archivos de configuración que webman. De hecho, el desarrollo de un plugin de aplicación es casi idéntico al desarrollo de un proyecto webman, con unas pocas consideraciones.

## Espacio de nombres
El directorio y la nomenclatura de los plugins siguen la especificación PSR4. Dado que los plugins se encuentran en el directorio de plugins, todos los espacios de nombres comienzan con "plugin", por ejemplo `plugin\cms\app\controller\UserController`. Aquí, "cms" es el directorio principal del código fuente del plugin.

## Acceso a URL
Las direcciones URL de los plugins de aplicación comienzan con `/app`, por ejemplo, la dirección URL de `plugin\cms\app\controller\UserController` es `http://127.0.0.1:8787/app/cms/user`.

## Archivos estáticos
Los archivos estáticos se encuentran en `plugin/{plugin}/public`. Por ejemplo, al acceder a `http://127.0.0.1:8787/app/cms/avatar.png`, en realidad se está obteniendo el archivo `plugin/cms/public/avatar.png`.

## Archivos de configuración
La configuración de un plugin es igual a la de un proyecto webman normal. Sin embargo, la configuración de un plugin generalmente solo afecta a ese plugin en particular, sin impacto en el proyecto principal.
 
Por ejemplo, el valor de `plugin.cms.app.controller_suffix` solo afecta al sufijo del controlador del plugin, sin influir en el proyecto principal.
 
De forma similar, el valor de `plugin.cms.app.controller_reuse` solo afecta a la reutilización de controladores en el plugin, sin influencia en el proyecto principal.
 
Asimismo, `plugin.cms.middleware` solo afecta a los middleware del plugin, sin impacto en el proyecto principal.
 
Del mismo modo, `plugin.cms.view` solo afecta a las vistas utilizadas por el plugin, sin influencia en el proyecto principal.
 
Igualmente, `plugin.cms.container` solo afecta a los contenedores utilizados por el plugin, sin impacto en el proyecto principal.
 
Finalmente, `plugin.cms.exception` solo afecta a la clase de manejo de excepciones del plugin, sin influencia en el proyecto principal.
 
Sin embargo, dado que las rutas son globales, la configuración de rutas del plugin también afecta globalmente.

## Obtención de configuraciones
La obtención de la configuración de un plugin se realiza mediante `config('plugin.{plugin}.{configuración específica}');`, por ejemplo, para obtener todas las configuraciones de `plugin/cms/config/app.php` se utiliza `config('plugin.cms.app')`. Del mismo modo, otros plugins o el proyecto principal pueden usar `config('plugin.cms.xxx')` para obtener la configuración del plugin cms.

## Configuraciones no admitidas
Los plugins de aplicación no admiten las configuraciones `server.php` y `session.php`, ni las configuraciones `app.request_class`, `app.public_path`, `app.runtime_path`.

## Base de datos
Un plugin puede configurar su propia base de datos, por ejemplo, el contenido de `plugin/cms/config/database.php` es el siguiente:

```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql es el nombre de la conexión
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base_de_datos',
            'username'    => 'usuario',
            'password'    => 'contraseña',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin es el nombre de la conexión
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => 'base_de_datos',
            'username'    => 'usuario',
            'password'    => 'contraseña',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
La forma de referencia es `Db::connection('plugin.{plugin}.{nombre_de_conexión}');`, por ejemplo:
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

Si se desea utilizar la base de datos del proyecto principal, simplemente se utiliza de la siguiente manera:
```php
use support\Db;
Db::table('user')->first();
// Suponiendo que el proyecto principal también tenga una conexión admin
Db::connection('admin')->table('admin')->first();
```

> **Consejo**
> El uso de thinkorm es similar.

## Redis
El uso de Redis es similar al de la base de datos. Por ejemplo, en `plugin/cms/config/redis.php`:
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
El uso es el siguiente:
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

Del mismo modo, si se desea reutilizar la configuración de Redis del proyecto principal:
```php
use support\Redis;
Redis::get('key');
// Suponiendo que el proyecto principal también tenga una conexión cache
Redis::connection('cache')->get('key');
```

## Registro
El uso del registro es similar al de la base de datos:
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

Si se desea reutilizar la configuración de registro del proyecto principal, simplemente se utiliza de la siguiente manera:
```php
use support\Log;
Log::info('contenido del registro');
// Suponiendo que el proyecto principal tenga una configuración de registro llamada test
Log::channel('test')->info('contenido del registro');
```

# Instalación y desinstalación de plugins de aplicación
Para instalar un plugin de aplicación, simplemente copia el directorio del plugin en el directorio `{proyecto principal}/plugin` y luego recarga o reinicia para que surta efecto. Para desinstalarlo, simplemente elimina el directorio del plugin correspondiente en `{proyecto principal}/plugin`.
