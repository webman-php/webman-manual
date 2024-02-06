# Proceso de generación y publicación de complementos básicos

## Principio
1. Tomando como ejemplo un complemento de origen cruzado, el complemento se divide en tres partes: un archivo de programa de middleware de origen cruzado, un archivo de configuración de middleware.php y uno generado mediante comando Install.php.
2. Utilizamos un comando para empaquetar y publicar los tres archivos en Composer.
3. Cuando un usuario instala un complemento de origen cruzado utilizando Composer, Install.php del complemento copiará el archivo de programa de middleware de origen cruzado y el archivo de configuración a `{directorio principal del proyecto}/config/plugin`, para que webman los cargue. Esto logra que el archivo de programa de middleware de origen cruzado se configure automáticamente y entre en efecto.
4. Cuando un usuario elimina el complemento mediante Composer, Install.php eliminará el archivo de programa de middleware de origen cruzado correspondiente y el archivo de configuración, logrando la desinstalación automática del complemento.

## Normas
1. El nombre del complemento consta de dos partes, `fabricante` y `nombre del complemento`. Por ejemplo, `webman/push`, que corresponde al nombre del paquete de Composer.
2. Los archivos de configuración del complemento se almacenan de forma unificada en `config/plugin/fabricante/nombre del complemento/` (el comando de consola creará automáticamente el directorio de configuración). Si el complemento no requiere configuraciones, se deben eliminar los directorios de configuración creados automáticamente.
3. El directorio de configuración del complemento solo admite los archivos: app.php (configuración principal del complemento), bootstrap.php (configuración de inicio de proceso), route.php (configuración de ruta), middleware.php (configuración de middleware), process.php (configuración de proceso personalizado), database.php (configuración de base de datos), redis.php (configuración de Redis) y thinkorm.php (configuración de ThinkORM). Estas configuraciones serán reconocidas automáticamente por webman.
4. El complemento utiliza el siguiente método para acceder a la configuración`config('plugin.fabricante.nombre del complemento.archivo de configuración.item específico');`, por ejemplo `config('plugin.webman.push.app.app_key')`.
5. Si el complemento tiene su propia configuración de base de datos, se accede de la siguiente manera: `illuminate/database` como `Db::connection('plugin.fabricante.nombre del complemento.conexión específica')` y `thinkrom` como `Db::connct('plugin.fabricante.nombre del complemento.conexión específica')`.
6. Si un complemento necesita colocar archivos de negocios en el directorio `app/`, asegúrese de que no entren en conflicto con el proyecto del usuario ni con otros complementos.
7. Los complementos deben evitar copiar archivos o directorios al proyecto principal en la medida de lo posible. Por ejemplo, el complemento de origen cruzado, aparte de los archivos de configuración que deben copiarse al proyecto principal, el archivo de programa de middleware debe colocarse en `vendor/webman/cros/src` y no necesita copiarse al proyecto principal.
8. Se recomienda utilizar letras mayúsculas para el espacio de nombres del complemento, por ejemplo, Webman/Console.

## Ejemplo

**Instalación de la línea de comandos `webman/console`**

`composer require webman/console`

#### Crear un complemento

Supongamos que el nombre del complemento creado es `foo/admin` (el nombre también es el nombre del proyecto que se publicará posteriormente en Composer, el nombre debe estar en minúsculas). Ejecute el siguiente comando
`php webman plugin:create --name=foo/admin`

Después de crear el complemento, se generará el directorio `vendor/foo/admin` para almacenar los archivos relacionados con el complemento y `config/plugin/foo/admin` para almacenar las configuraciones relacionadas con el complemento.

> Nota
> `config/plugin/foo/admin` admite las siguientes configuraciones: app.php (configuración principal del complemento), bootstrap.php (configuración de inicio de proceso), route.php (configuración de ruta), middleware.php (configuración de middleware), process.php (configuración de proceso personalizado), database.php (configuración de base de datos), redis.php (configuración de Redis) y thinkorm.php (configuración de ThinkORM). El formato de configuración es el mismo que el de webman, y estas configuraciones se reconocerán automáticamente por webman y se fusionarán en la configuración.
Cuando se utilice, acceda con el prefijo `plugin`, por ejemplo `config('plugin.foo.admin.app')`.


#### Exportar complemento

Una vez que hayamos desarrollado el complemento, ejecutamos el siguiente comando para exportar el complemento.
`php webman plugin:export --name=foo/admin`

> Explicación
Al exportar, el directorio config/plugin/foo/admin se copiará a src dentro de vendor/foo/admin, y se generará automáticamente un archivo Install.php. Install.php se utiliza para realizar operaciones automáticas al instalar y desinstalar el complemento.
La operación predeterminada al instalar es copiar las configuraciones de src dentro de vendor/foo/admin al directorio config/plugin del proyecto actual.
La operación predeterminada al eliminar es eliminar los archivos de configuración del directorio config/plugin del proyecto actual.
Puede modificar Install.php para realizar operaciones personalizadas al instalar y desinstalar el complemento.

#### Publicar complemento
* Supongamos que ya tienes una cuenta en [github](https://github.com) y en [packagist](https://packagist.org)
* Crea un proyecto admin en [github](https://github.com) y sube el código. Supongamos que la dirección del proyecto es `https://github.com/yourusername/admin`.
* Ingresa a la dirección `https://github.com/yourusername/admin/releases/new` para publicar un lanzamiento, por ejemplo, `v1.0.0`.
* Ingresa a [packagist](https://packagist.org), haz clic en `Submit` en la navegación, y sube la dirección de tu proyecto de github `https://github.com/yourusername/admin`. Así es como se publica un complemento.

> **Consejo**
> Si al enviar el complemento a `packagist` aparece un mensaje de conflicto, puedes utilizar un nombre de fabricante diferente, por ejemplo, cambiar `foo/admin` a `myfoo/admin`.

Cuando actualices el código del proyecto del complemento en el futuro, asegúrate de sincronizar el código con github, volver a ingresar a `https://github.com/yourusername/admin/releases/new` para publicar un nuevo lanzamiento, y luego en la página de `https://packagist.org/packages/foo/admin` haz clic en `Update` para actualizar la versión.

## Agregar comandos al complemento
A veces, los complementos necesitan comandos personalizados para ofrecer funciones de asistencia, por ejemplo, al instalar el complemento `webman/redis-queue`, el proyecto agregará automáticamente un comando `redis-queue:consumer`, para que el usuario simplemente ejecute `php webman redis-queue:consumer send-mail` y se generará una clase consumidora SendMail.php en el proyecto, lo que ayuda al desarrollo rápido.

Supongamos que el complemento `foo/admin` necesita agregar el comando `foo-admin:add`, sigue los pasos a continuación.

#### Crear un nuevo comando

**Crea un nuevo archivo de comando `vendor/foo/admin/src/FooAdminAddCommand.php`**

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
    protected static $defaultDescription = 'Esta es la descripción del comando';

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
> Para evitar conflictos entre comandos de complementos, se recomienda que el formato del nombre del comando sea `fabricante-nombre del complemento:comando específico`, por ejemplo, todos los comandos del complemento `foo/admin` deben tener `foo-admin:` como prefijo, por ejemplo `foo-admin:add`.

#### Agregar configuración
**Crea un nuevo archivo de configuración `config/plugin/foo/admin/command.php`**
```php
<?php

use Foo\Admin\FooAdminAddCommand;

return [
    FooAdminAddCommand::class,
    // ....se pueden agregar múltiples configuraciones...
];
```

> **Consejo**
> `command.php` se utiliza para configurar comandos personalizados del complemento. Cada elemento del array corresponde a una clase de comando de consola, y cada archivo de clase corresponde a un comando. Cuando un usuario ejecuta un comando de consola, `webman/console` cargará automáticamente todos los comandos personalizados configurados en `command.php` de cada complemento. Para obtener más información sobre comandos de consola, consulta [Command Line](console.md).

#### Ejecutar exportación
Ejecuta el comando `php webman plugin:export --name=foo/admin` para exportar el complemento y enviarlo a `packagist`. De esta forma, al instalar el complemento `foo/admin`, se agregará el comando  `foo-admin:add`. Al ejecutar `php webman foo-admin:add jerry`, se imprimirá `Admin add jerry`.
