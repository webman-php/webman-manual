# Línea de comandos

Componente de línea de comandos de Webman

## Instalación
```
composer require webman/console
```

## Tabla de contenidos

### Generación de código
- [make:controller](#make-controller) - Generar clase de controlador
- [make:model](#make-model) - Generar clase de modelo desde tabla de base de datos
- [make:crud](#make-crud) - Generar CRUD completo (modelo + controlador + validador)
- [make:middleware](#make-middleware) - Generar clase de middleware
- [make:command](#make-command) - Generar clase de comando de consola
- [make:bootstrap](#make-bootstrap) - Generar clase de inicialización bootstrap
- [make:process](#make-process) - Generar clase de proceso personalizado

### Compilación y despliegue
- [build:phar](#build-phar) - Empaquetar proyecto como archivo PHAR
- [build:bin](#build-bin) - Empaquetar proyecto como binario independiente
- [install](#install) - Ejecutar script de instalación de Webman

### Comandos de utilidad
- [version](#version) - Mostrar versión del framework Webman
- [fix-disable-functions](#fix-disable-functions) - Corregir funciones deshabilitadas en php.ini
- [route:list](#route-list) - Mostrar todas las rutas registradas

### Gestión de plugins de aplicación (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - Crear nuevo plugin de aplicación
- [app-plugin:install](#app-plugin-install) - Instalar plugin de aplicación
- [app-plugin:uninstall](#app-plugin-uninstall) - Desinstalar plugin de aplicación
- [app-plugin:update](#app-plugin-update) - Actualizar plugin de aplicación
- [app-plugin:zip](#app-plugin-zip) - Empaquetar plugin de aplicación como ZIP

### Gestión de plugins (plugin:*)
- [plugin:create](#plugin-create) - Crear nuevo plugin de Webman
- [plugin:install](#plugin-install) - Instalar plugin de Webman
- [plugin:uninstall](#plugin-uninstall) - Desinstalar plugin de Webman
- [plugin:enable](#plugin-enable) - Habilitar plugin de Webman
- [plugin:disable](#plugin-disable) - Deshabilitar plugin de Webman
- [plugin:export](#plugin-export) - Exportar código fuente del plugin

### Gestión de servicios
- [start](#start) - Iniciar procesos worker de Webman
- [stop](#stop) - Detener procesos worker de Webman
- [restart](#restart) - Reiniciar procesos worker de Webman
- [reload](#reload) - Recargar código sin tiempo de inactividad
- [status](#status) - Ver estado de procesos worker
- [connections](#connections) - Obtener información de conexión de procesos worker

## Generación de código

<a name="make-controller"></a>
### make:controller

Generar clase de controlador.

**Uso:**
```bash
php webman make:controller <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del controlador (sin sufijo) |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--plugin` | `-p` | Generar controlador en directorio del plugin especificado |
| `--path` | `-P` | Ruta personalizada del controlador |
| `--force` | `-f` | Sobrescribir si el archivo existe |
| `--no-suffix` | | No añadir sufijo "Controller" |

**Ejemplos:**
```bash
# Crear UserController en app/controller
php webman make:controller User

# Crear en plugin
php webman make:controller AdminUser -p admin

# Ruta personalizada
php webman make:controller User -P app/api/controller

# Sobrescribir archivo existente
php webman make:controller User -f

# Crear sin sufijo "Controller"
php webman make:controller UserHandler --no-suffix
```

**Estructura del archivo generado:**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**Notas:**
- Los controladores se colocan en `app/controller/` por defecto
- El sufijo del controlador de la configuración se añade automáticamente
- Solicita confirmación de sobrescritura si el archivo existe (igual que otros comandos)

<a name="make-model"></a>
### make:model

Generar clase de modelo desde tabla de base de datos. Soporta Laravel ORM y ThinkORM.

**Uso:**
```bash
php webman make:model [nombre]
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | No | Nombre de la clase del modelo, puede omitirse en modo interactivo |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--plugin` | `-p` | Generar modelo en directorio del plugin especificado |
| `--path` | `-P` | Directorio de destino (relativo a la raíz del proyecto) |
| `--table` | `-t` | Especificar nombre de tabla; recomendado cuando no sigue la convención |
| `--orm` | `-o` | Elegir ORM: `laravel` o `thinkorm` |
| `--database` | `-d` | Especificar nombre de conexión de base de datos |
| `--force` | `-f` | Sobrescribir archivo existente |

**Notas de rutas:**
- Por defecto: `app/model/` (aplicación principal) o `plugin/<plugin>/app/model/` (plugin)
- `--path` es relativo a la raíz del proyecto, ej. `plugin/admin/app/model`
- Al usar `--plugin` y `--path` juntos, deben apuntar al mismo directorio

**Ejemplos:**
```bash
# Crear modelo User en app/model
php webman make:model User

# Especificar nombre de tabla y ORM
php webman make:model User -t wa_users -o laravel

# Crear en plugin
php webman make:model AdminUser -p admin

# Ruta personalizada
php webman make:model User -P plugin/admin/app/model
```

**Modo interactivo:** Cuando se omite el nombre, entra en flujo interactivo: seleccionar tabla → introducir nombre del modelo → introducir ruta. Soporta: Enter para ver más, `0` para crear modelo vacío, `/palabra_clave` para filtrar tablas.

**Estructura del archivo generado:**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (primary key)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

Las anotaciones `@property` se generan automáticamente desde la estructura de la tabla. Soporta MySQL y PostgreSQL.

<a name="make-crud"></a>
### make:crud

Generar modelo, controlador y validador desde tabla de base de datos de una vez, formando capacidad CRUD completa.

**Uso:**
```bash
php webman make:crud
```

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--table` | `-t` | Especificar nombre de tabla |
| `--model` | `-m` | Nombre de clase del modelo |
| `--model-path` | `-M` | Directorio del modelo (relativo a la raíz del proyecto) |
| `--controller` | `-c` | Nombre de clase del controlador |
| `--controller-path` | `-C` | Directorio del controlador |
| `--validator` | | Nombre de clase del validador (requiere `webman/validation`) |
| `--validator-path` | | Directorio del validador (requiere `webman/validation`) |
| `--plugin` | `-p` | Generar archivos en directorio del plugin especificado |
| `--orm` | `-o` | ORM: `laravel` o `thinkorm` |
| `--database` | `-d` | Nombre de conexión de base de datos |
| `--force` | `-f` | Sobrescribir archivos existentes |
| `--no-validator` | | No generar validador |
| `--no-interaction` | `-n` | Modo no interactivo, usar valores por defecto |

**Flujo de ejecución:** Cuando no se especifica `--table`, entra en selección interactiva de tabla; el nombre del modelo por defecto se infiere del nombre de la tabla; el nombre del controlador por defecto es nombre del modelo + sufijo de controlador; el nombre del validador por defecto es nombre del controlador sin sufijo + `Validator`. Rutas por defecto: modelo `app/model/`, controlador `app/controller/`, validador `app/validation/`; para plugins: subdirectorios correspondientes bajo `plugin/<plugin>/app/`.

**Ejemplos:**
```bash
# Generación interactiva (confirmación paso a paso tras seleccionar tabla)
php webman make:crud

# Especificar nombre de tabla
php webman make:crud --table=users

# Especificar nombre de tabla y plugin
php webman make:crud --table=users --plugin=admin

# Especificar rutas
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# No generar validador
php webman make:crud --table=users --no-validator

# No interactivo + sobrescribir
php webman make:crud --table=users --no-interaction --force
```

**Estructura de archivos generados:**

Modelo (`app/model/User.php`):
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

Controlador (`app/controller/UserController.php`):
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

Validador (`app/validation/UserValidator.php`):
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => 'Clave primaria',
        'username' => 'Nombre de usuario'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**Notas:**
- La generación del validador se omite si `webman/validation` no está instalado o habilitado (instalar con `composer require webman/validation`)
- Los `attributes` del validador se generan automáticamente desde los comentarios de los campos de la base de datos; sin comentarios no hay `attributes`
- Los mensajes de error del validador soportan i18n; el idioma se selecciona de `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

Generar clase de middleware y registrarla automáticamente en `config/middleware.php` (o `plugin/<plugin>/config/middleware.php` para plugins).

**Uso:**
```bash
php webman make:middleware <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del middleware |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--plugin` | `-p` | Generar middleware en directorio del plugin especificado |
| `--path` | `-P` | Directorio de destino (relativo a la raíz del proyecto) |
| `--force` | `-f` | Sobrescribir archivo existente |

**Ejemplos:**
```bash
# Crear middleware Auth en app/middleware
php webman make:middleware Auth

# Crear en plugin
php webman make:middleware Auth -p admin

# Ruta personalizada
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**Estructura del archivo generado:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**Notas:**
- Se coloca en `app/middleware/` por defecto
- El nombre de la clase se añade automáticamente al archivo de configuración de middleware para activación

<a name="make-command"></a>
### make:command

Generar clase de comando de consola.

**Uso:**
```bash
php webman make:command <nombre-comando>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre-comando` | Sí | Nombre del comando en formato `grupo:accion` (ej. `user:list`) |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--plugin` | `-p` | Generar comando en directorio del plugin especificado |
| `--path` | `-P` | Directorio de destino (relativo a la raíz del proyecto) |
| `--force` | `-f` | Sobrescribir archivo existente |

**Ejemplos:**
```bash
# Crear comando user:list en app/command
php webman make:command user:list

# Crear en plugin
php webman make:command user:list -p admin

# Ruta personalizada
php webman make:command user:list -P plugin/admin/app/command

# Sobrescribir archivo existente
php webman make:command user:list -f
```

**Estructura del archivo generado:**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**Notas:**
- Se coloca en `app/command/` por defecto

<a name="make-bootstrap"></a>
### make:bootstrap

Generar clase de inicialización bootstrap. El método `start` se llama automáticamente cuando el proceso inicia, típicamente para inicialización global.

**Uso:**
```bash
php webman make:bootstrap <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre de la clase Bootstrap |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--plugin` | `-p` | Generar en directorio del plugin especificado |
| `--path` | `-P` | Directorio de destino (relativo a la raíz del proyecto) |
| `--force` | `-f` | Sobrescribir archivo existente |

**Ejemplos:**
```bash
# Crear MyBootstrap en app/bootstrap
php webman make:bootstrap MyBootstrap

# Crear sin habilitar automáticamente
php webman make:bootstrap MyBootstrap no

# Crear en plugin
php webman make:bootstrap MyBootstrap -p admin

# Ruta personalizada
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# Sobrescribir archivo existente
php webman make:bootstrap MyBootstrap -f
```

**Estructura del archivo generado:**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**Notas:**
- Se coloca en `app/bootstrap/` por defecto
- Al habilitar, la clase se añade a `config/bootstrap.php` (o `plugin/<plugin>/config/bootstrap.php` para plugins)

<a name="make-process"></a>
### make:process

Generar clase de proceso personalizado y escribir en `config/process.php` para arranque automático.

**Uso:**
```bash
php webman make:process <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre de la clase de proceso (ej. MyTcp, MyWebsocket) |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--plugin` | `-p` | Generar en directorio del plugin especificado |
| `--path` | `-P` | Directorio de destino (relativo a la raíz del proyecto) |
| `--force` | `-f` | Sobrescribir archivo existente |

**Ejemplos:**
```bash
# Crear en app/process
php webman make:process MyTcp

# Crear en plugin
php webman make:process MyProcess -p admin

# Ruta personalizada
php webman make:process MyProcess -P plugin/admin/app/process

# Sobrescribir archivo existente
php webman make:process MyProcess -f
```

**Flujo interactivo:** Solicita en orden: ¿escuchar en puerto? → tipo de protocolo (websocket/http/tcp/udp/unixsocket) → dirección de escucha (IP:puerto o ruta de unix socket) → número de procesos. El protocolo HTTP también pregunta por modo integrado o personalizado.

**Estructura del archivo generado:**

Proceso sin escucha (solo `onWorkerStart`):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

Los procesos con escucha TCP/WebSocket generan los templates de callback correspondientes `onConnect`, `onMessage`, `onClose`.

**Notas:**
- Se coloca en `app/process/` por defecto; la configuración del proceso se escribe en `config/process.php`
- La clave de configuración es snake_case del nombre de la clase; falla si ya existe
- El modo HTTP integrado reutiliza el archivo de proceso `app\process\Http`, no genera nuevo archivo
- Protocolos soportados: websocket, http, tcp, udp, unixsocket

## Compilación y despliegue

<a name="build-phar"></a>
### build:phar

Empaquetar proyecto como archivo PHAR para distribución y despliegue.

**Uso:**
```bash
php webman build:phar
```

**Iniciar:**

Navegar al directorio build y ejecutar

```bash
php webman.phar start
```

**Notas:**
* El proyecto empaquetado no soporta reload; usar restart para actualizar código

* Para evitar tamaño de archivo grande y uso excesivo de memoria, configurar exclude_pattern y exclude_files en config/plugin/webman/console/app.php para excluir archivos innecesarios.

* Ejecutar webman.phar crea un directorio runtime en la misma ubicación para logs y archivos temporales.

* Si el proyecto usa archivo .env, colocar .env en el mismo directorio que webman.phar.

* webman.phar no soporta procesos personalizados en Windows

* Nunca almacenar archivos subidos por usuarios dentro del paquete phar; operar sobre subidas de usuarios vía phar:// es peligroso (vulnerabilidad de deserialización phar). Los archivos subidos por usuarios deben almacenarse por separado en disco fuera del phar. Ver más abajo.

* Si el negocio necesita subir archivos al directorio público, extraer el directorio public a la misma ubicación que webman.phar y configurar config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
Usar la función auxiliar public_path($ruta_relativa) para obtener la ruta real del directorio público.


<a name="build-bin"></a>
### build:bin

Empaquetar proyecto como binario independiente con runtime PHP embebido. No requiere instalación de PHP en el entorno de destino.

**Uso:**
```bash
php webman build:bin [versión]
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `versión` | No | Versión de PHP (ej. 8.1, 8.2), por defecto la versión actual de PHP, mínimo 8.1 |

**Ejemplos:**
```bash
# Usar versión actual de PHP
php webman build:bin

# Especificar PHP 8.2
php webman build:bin 8.2
```

**Iniciar:**

Navegar al directorio build y ejecutar

```bash
./webman.bin start
```

**Notas:**
* Muy recomendado: la versión local de PHP debe coincidir con la versión de compilación (ej. PHP 8.1 local → compilar con 8.1) para evitar problemas de compatibilidad
* La compilación descarga el código fuente de PHP 8 pero no lo instala localmente; no afecta el entorno PHP local
* webman.bin actualmente solo funciona en Linux x86_64; no soportado en macOS
* El proyecto empaquetado no soporta reload; usar restart para actualizar código
* .env no se empaqueta por defecto (controlado por exclude_files en config/plugin/webman/console/app.php); colocar .env en el mismo directorio que webman.bin al iniciar
* Se crea un directorio runtime en el directorio de webman.bin para archivos de log
* webman.bin no lee php.ini externo; para configuraciones personalizadas de php.ini, usar custom_ini en config/plugin/webman/console/app.php
* Excluir archivos innecesarios vía config/plugin/webman/console/app.php para evitar tamaño de paquete grande
* La compilación binaria no soporta corrutinas Swoole
* Nunca almacenar archivos subidos por usuarios dentro del paquete binario; operar vía phar:// es peligroso (vulnerabilidad de deserialización phar). Los archivos subidos por usuarios deben almacenarse por separado en disco fuera del paquete.
* Si el negocio necesita subir archivos al directorio público, extraer el directorio public a la misma ubicación que webman.bin y configurar config/app.php como se indica abajo, luego recompilar:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

Ejecutar script de instalación del framework Webman (llama a `\Webman\Install::install()`), para inicialización del proyecto.

**Uso:**
```bash
php webman install
```

## Comandos de utilidad

<a name="version"></a>
### version

Mostrar versión de workerman/webman-framework.

**Uso:**
```bash
php webman version
```

**Notas:** Lee la versión desde `vendor/composer/installed.php`; devuelve fallo si no puede leer.

<a name="fix-disable-functions"></a>
### fix-disable-functions

Corregir `disable_functions` en php.ini, eliminando las funciones requeridas por Webman.

**Uso:**
```bash
php webman fix-disable-functions
```

**Notas:** Elimina las siguientes funciones (y coincidencias por prefijo) de `disable_functions`: `stream_socket_server`, `stream_socket_accept`, `stream_socket_client`, `pcntl_*`, `posix_*`, `proc_*`, `shell_exec`, `exec`. Omite si no se encuentra php.ini o `disable_functions` está vacío. **Modifica directamente el archivo php.ini**; se recomienda hacer copia de seguridad.

<a name="route-list"></a>
### route:list

Listar todas las rutas registradas en formato tabla.

**Uso:**
```bash
php webman route:list
```

**Ejemplo de salida:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**Columnas de salida:** URI, Method, Callback, Middleware, Name. Los callbacks Closure se muestran como "Closure".

## Gestión de plugins de aplicación (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

Crear nuevo plugin de aplicación, generando estructura de directorios completa y archivos base bajo `plugin/<nombre>`.

**Uso:**
```bash
php webman app-plugin:create <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del plugin; debe coincidir con `[a-zA-Z0-9][a-zA-Z0-9_-]*`, no puede contener `/` o `\` |

**Ejemplos:**
```bash
# Crear plugin de aplicación llamado foo
php webman app-plugin:create foo

# Crear plugin con guión
php webman app-plugin:create my-app
```

**Estructura de directorios generada:**
```
plugin/<nombre>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, etc.
├── api/Install.php  # Hooks de instalación/desinstalación/actualización
├── public/
└── install.sql
```

**Notas:**
- El plugin se crea bajo `plugin/<nombre>/`; falla si el directorio ya existe

<a name="app-plugin-install"></a>
### app-plugin:install

Instalar plugin de aplicación, ejecutando `plugin/<nombre>/api/Install::install($version)`.

**Uso:**
```bash
php webman app-plugin:install <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del plugin; debe coincidir con `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**Ejemplos:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

Desinstalar plugin de aplicación, ejecutando `plugin/<nombre>/api/Install::uninstall($version)`.

**Uso:**
```bash
php webman app-plugin:uninstall <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del plugin |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--yes` | `-y` | Omitir confirmación, ejecutar directamente |

**Ejemplos:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

Actualizar plugin de aplicación, ejecutando en orden `Install::beforeUpdate($from, $to)` y `Install::update($from, $to, $context)`.

**Uso:**
```bash
php webman app-plugin:update <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del plugin |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--from` | `-f` | Versión origen, por defecto versión actual |
| `--to` | `-t` | Versión destino, por defecto versión actual |

**Ejemplos:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

Empaquetar plugin de aplicación como archivo ZIP, salida a `plugin/<nombre>.zip`.

**Uso:**
```bash
php webman app-plugin:zip <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del plugin |

**Ejemplos:**
```bash
php webman app-plugin:zip foo
```

**Notas:**
- Excluye automáticamente `node_modules`, `.git`, `.idea`, `.vscode`, `__pycache__`, etc.

## Gestión de plugins (plugin:*)

<a name="plugin-create"></a>
### plugin:create

Crear nuevo plugin de Webman (forma de paquete Composer), generando directorio de configuración `config/plugin/<nombre>` y directorio de código fuente del plugin `vendor/<nombre>`.

**Uso:**
```bash
php webman plugin:create <nombre>
php webman plugin:create --name <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del paquete del plugin en formato `vendor/package` (ej. `foo/my-admin`); debe seguir la nomenclatura de paquetes Composer |

**Ejemplos:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**Estructura generada:**
- `config/plugin/<nombre>/app.php`: Configuración del plugin (incluye interruptor `enable`)
- `vendor/<nombre>/composer.json`: Definición del paquete del plugin
- `vendor/<nombre>/src/`: Directorio de código fuente del plugin
- Añade automáticamente mapeo PSR-4 al composer.json de la raíz del proyecto
- Ejecuta `composer dumpautoload` para refrescar el autoload

**Notas:**
- El nombre debe estar en formato `vendor/package`: letras minúsculas, números, `-`, `_`, `.`, y debe contener un `/`
- Falla si `config/plugin/<nombre>` o `vendor/<nombre>` ya existe
- Error si se proporcionan tanto el argumento como `--name` con valores diferentes

<a name="plugin-install"></a>
### plugin:install

Ejecutar script de instalación del plugin (`Install::install()`), copiando recursos del plugin al directorio del proyecto.

**Uso:**
```bash
php webman plugin:install <nombre>
php webman plugin:install --name <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del paquete del plugin en formato `vendor/package` (ej. `foo/my-admin`) |

**Opciones:**

| Opción | Descripción |
|--------|-------------|
| `--name` | Especificar nombre del plugin como opción; usar esto o el argumento |

**Ejemplos:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

Ejecutar script de desinstalación del plugin (`Install::uninstall()`), eliminando recursos del plugin del proyecto.

**Uso:**
```bash
php webman plugin:uninstall <nombre>
php webman plugin:uninstall --name <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del paquete del plugin en formato `vendor/package` |

**Opciones:**

| Opción | Descripción |
|--------|-------------|
| `--name` | Especificar nombre del plugin como opción; usar esto o el argumento |

**Ejemplos:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

Habilitar plugin, estableciendo `enable` a `true` en `config/plugin/<nombre>/app.php`.

**Uso:**
```bash
php webman plugin:enable <nombre>
php webman plugin:enable --name <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del paquete del plugin en formato `vendor/package` |

**Opciones:**

| Opción | Descripción |
|--------|-------------|
| `--name` | Especificar nombre del plugin como opción; usar esto o el argumento |

**Ejemplos:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

Deshabilitar plugin, estableciendo `enable` a `false` en `config/plugin/<nombre>/app.php`.

**Uso:**
```bash
php webman plugin:disable <nombre>
php webman plugin:disable --name <nombre>
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del paquete del plugin en formato `vendor/package` |

**Opciones:**

| Opción | Descripción |
|--------|-------------|
| `--name` | Especificar nombre del plugin como opción; usar esto o el argumento |

**Ejemplos:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

Exportar configuración del plugin y directorios especificados del proyecto a `vendor/<nombre>/src/`, y generar `Install.php` para empaquetado y publicación.

**Uso:**
```bash
php webman plugin:export <nombre> [--source=ruta]...
php webman plugin:export --name <nombre> [--source=ruta]...
```

**Argumentos:**

| Argumento | Requerido | Descripción |
|----------|----------|-------------|
| `nombre` | Sí | Nombre del paquete del plugin en formato `vendor/package` |

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--name` | | Especificar nombre del plugin como opción; usar esto o el argumento |
| `--source` | `-s` | Ruta a exportar (relativa a la raíz del proyecto); puede especificarse múltiples veces |

**Ejemplos:**
```bash
# Exportar plugin, por defecto incluye config/plugin/<nombre>
php webman plugin:export foo/my-admin

# Exportar además app, config, etc.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**Notas:**
- El nombre del plugin debe seguir la nomenclatura de paquetes Composer (`vendor/package`)
- Si `config/plugin/<nombre>` existe y no está en `--source`, se añade automáticamente a la lista de exportación
- El `Install.php` exportado incluye `pathRelation` para uso por `plugin:install` / `plugin:uninstall`
- `plugin:install` y `plugin:uninstall` requieren que el plugin exista en `vendor/<nombre>`, con clase `Install` y constante `WEBMAN_PLUGIN`

## Gestión de servicios

<a name="start"></a>
### start

Iniciar procesos worker de Webman. Modo DEBUG por defecto (primer plano).

**Uso:**
```bash
php webman start
```

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--daemon` | `-d` | Iniciar en modo DAEMON (segundo plano) |

<a name="stop"></a>
### stop

Detener procesos worker de Webman.

**Uso:**
```bash
php webman stop
```

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--graceful` | `-g` | Detención elegante; esperar a que las peticiones actuales terminen antes de salir |

<a name="restart"></a>
### restart

Reiniciar procesos worker de Webman.

**Uso:**
```bash
php webman restart
```

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--daemon` | `-d` | Ejecutar en modo DAEMON tras reiniciar |
| `--graceful` | `-g` | Detención elegante antes de reiniciar |

<a name="reload"></a>
### reload

Recargar código sin tiempo de inactividad. Para recarga en caliente tras actualizaciones de código.

**Uso:**
```bash
php webman reload
```

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--graceful` | `-g` | Recarga elegante; esperar a que las peticiones actuales terminen antes de recargar |

<a name="status"></a>
### status

Ver estado de ejecución de procesos worker.

**Uso:**
```bash
php webman status
```

**Opciones:**

| Opción | Atajo | Descripción |
|--------|----------|-------------|
| `--live` | `-d` | Mostrar detalles (estado en vivo) |

<a name="connections"></a>
### connections

Obtener información de conexión de procesos worker.

**Uso:**
```bash
php webman connections
```
