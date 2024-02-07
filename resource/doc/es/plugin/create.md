# Proceso de generación y publicación de complementos básicos

## Principio
1. Tomemos un complemento de unión cruzada como ejemplo. El complemento consta de tres partes: un archivo de programa de middleware de unión cruzada, un archivo de configuración de middleware llamado middleware.php, y uno generado automáticamente a través del comando Install.php.
2. Utilizamos el comando para empaquetar y publicar los tres archivos en Composer.
3. Cuando los usuarios instalan el complemento de unión cruzada utilizando Composer, Install.php copiará el archivo de programa de middleware de unión cruzada y el archivo de configuración en `{proyecto principal}/config/plugin`, para que webman lo cargue. Esto implementa la configuración automática efectiva de los archivos de middleware de unión cruzada.
4. Cuando los usuarios eliminan el complemento utilizando Composer, Install.php eliminará los archivos correspondientes del programa de middleware de unión cruzada y de configuración, logrando la desinstalación automática del complemento.

## Normativa
1. El nombre del complemento consta de dos partes, el "fabricante" y el "nombre del complemento", por ejemplo, `webman/push`, que corresponde al nombre del paquete de Composer.
2. Los archivos de configuración del complemento se colocan de forma unificada en `config/plugin/fabricante/nombre del complemento/` (el comando de consola creará automáticamente el directorio de configuración). Si el complemento no requiere configuración, se debe eliminar el directorio de configuración creado automáticamente.
3. El directorio de configuración del complemento solo admite los archivos app.php (configuración principal del complemento), bootstrap.php (configuración de inicio de procesos), route.php (configuración de ruta), middleware.php (configuración de middleware), process.php (configuración de procesos personalizados), database.php (configuración de bases de datos), redis.php (configuración de Redis), thinkorm.php (configuración de ThinkORM). Estas configuraciones serán automáticamente reconocidas por webman.
4. Para acceder a la configuración del complemento, se utiliza el siguiente método: `config('plugin.fabricante.nombre del complemento.nombre del archivo de configuración.configuración específica');`, por ejemplo, `config('plugin.webman.push.app.app_key')`.
5. Si el complemento tiene su propia configuración de base de datos, se accede de la siguiente manera: para `illuminate/database` como `Db::connection('plugin.fabricante.nombre del complemento.conexión específica')`, y para `thinkrom` como `Db::connct('plugin.fabricante.nombre del complemento.conexión específica')`.
6. Si un complemento necesita colocar archivos de negocio en el directorio `app/`, se debe garantizar que no entren en conflicto con los proyectos de los usuarios ni con otros complementos.
7. Los complementos deben evitar copiar archivos o directorios al proyecto principal tanto como sea posible. Por ejemplo, aparte del archivo de configuración, el archivo de middleware del complemento de unión cruzada debe ubicarse en `vendor/webman/cros/src` y no necesita copiarse al proyecto principal.
8. Se sugiere que los espacios de nombres de los complementos utilicen mayúsculas, por ejemplo, Webman/Console.

## Ejemplo

**Instalar el comando de línea `webman/console`**

`composer require webman/console`

### Crear un complemento

Supongamos que el complemento creado se llama `foo/admin` (el nombre también será el nombre del proyecto que se publicará a través de Composer, y el nombre debe ser en minúsculas).
Ejecutar el comando
`php webman plugin:create --name=foo/admin`

Después de crear el complemento, se generará el directorio `vendor/foo/admin` para almacenar los archivos relacionados con el complemento y `config/plugin/foo/admin` para almacenar la configuración relacionada con el complemento.

> Nota
> `config/plugin/foo/admin` admite las siguientes configuraciones: app.php (configuración principal del complemento), bootstrap.php (configuración de inicio de procesos), route.php (configuración de ruta), middleware.php (configuración de middleware), process.php (configuración de procesos personalizados), database.php (configuración de bases de datos), redis.php (configuración de Redis), thinkorm.php (configuración de ThinkORM). El formato de la configuración es el mismo que el de webman, y estas configuraciones se reconocerán automáticamente y se fusionarán en la configuración.
Al acceder a ellas, se usa el prefijo `plugin`, por ejemplo, `config('plugin.foo.admin.app')`.

### Exportar el complemento

Una vez que hemos desarrollado el complemento, ejecutamos el siguiente comando para exportarlo
`php webman plugin:export --name=foo/admin`

> Nota
> Después de la exportación, el directorio config/plugin/foo/admin se copiará en vendor/foo/admin/src, y se generará automáticamente un Install.php. Install.php se utiliza para ejecutar ciertas operaciones automáticamente al instalar o desinstalar un complemento.
> La operación predeterminada de la instalación consiste en copiar la configuración de vendor/foo/admin/src al directorio actual del proyecto en config/plugin.
> La operación predeterminada de eliminación consiste en eliminar los archivos de configuración del directorio actual del proyecto en config/plugin.
> Puede modificar Install.php para realizar operaciones personalizadas al instalar o desinstalar el complemento.

### Publicar el complemento
* Supongamos que ya tienes una cuenta en [github](https://github.com) y [packagist](https://packagist.org)
* Crea un proyecto admin en [github](https://github.com) y sube el código. Supongamos que la dirección del proyecto es `https://github.com/tuusuario/admin`
* Ingresa a la dirección `https://github.com/tuusuario/admin/releases/new` para publicar un release, por ejemplo, `v1.0.0`
* Ingresa a [packagist](https://packagist.org) y haz clic en `Submit` en la navegación, luego envía la dirección de tu proyecto en GitHub, `https://github.com/tuusuario/admin`, para completar la publicación del complemento

> **Consejo**
> Si al ingresar un complemento en `packagist` se muestra un conflicto, considera cambiar el nombre del fabricante, por ejemplo, cambia `foo/admin` a `myfoo/admin`

Si el código de tu proyecto de complemento se actualiza, necesitarás sincronizar el código con GitHub, volver a ingresar a la dirección `https://github.com/tuusuario/admin/releases/new` para publicar un nuevo release, y luego en la página `https://packagist.org/packages/foo/admin` haz clic en el botón `Update` para actualizar la versión.

## Agregar comandos al complemento
A veces, los complementos necesitan algunos comandos personalizados que proporcionen funciones de asistencia, por ejemplo, después de instalar el complemento `webman/redis-queue`, el proyecto se expandirá automáticamente con un comando `redis-queue:consumer`, y los usuarios solo necesitan ejecutar `php webman redis-queue:consumer send-mail` para generar rápidamente una clase consumidora llamada SendMail.php en el proyecto, lo que ayuda al desarrollo rápido.

Supongamos que el complemento `foo/admin` necesita agregar el comando `foo-admin:add`. Consulta los siguientes pasos.

### Crear un nuevo comando
**Crea un archivo de comando llamado `vendor/foo/admin/src/FooAdminAddCommand.php`**

```php
<?php

namespace Foo\Admin;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class FooAdminAddCommand extends Command
{
    protected static $defaultName = 'foo-admin:add';
    protected static $defaultDescription = 'Descripción del comando aquí';

    /**
     * @return void
     */
    protected function configure()
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Agregar nombre');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $output->writeln("Admin add $name");
        return self::SUCCESS;
    }

}
```

> **Nota**
> Para evitar conflictos entre comandos de complementos, se recomienda que el formato del comando sea `fabricante-nombre del complemento:comando específico`, por ejemplo, todos los comandos del complemento `foo/admin` deberían tener como prefijo `foo-admin:`, por ejemplo, `foo-admin:add`.

### Agregar configuración
**Crea una configuración llamada `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....puedes agregar múltiples configuraciones...
];
```

> **Consejo**
> `command.php` se utiliza para configurar comandos personalizados del complemento. Cada elemento del array corresponde a un archivo de clase de comando, y cada archivo de clase corresponde a un comando. Cuando un usuario ejecuta un comando en la línea de comandos, `webman/console` cargará automáticamente los comandos personalizados establecidos en `command.php` de cada complemento. Para obtener más información sobre comandos, consulta [Comandos de consola](console.md).

### Ejecutar la exportación
Ejecuta el comando `php webman plugin:export --name=foo/admin` para exportar el complemento y subirlo a `packagist`. De esta manera, al instalar el complemento `foo/admin`, se agregará el comando `foo-admin:add`. Al ejecutar `php webman foo-admin:add jerry`, se imprimirá `Admin add jerry`.
