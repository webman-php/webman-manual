# Inicialización del negocio

A veces necesitamos realizar una inicialización del negocio después de que el proceso se inicia, esta inicialización solo se ejecuta una vez durante el ciclo de vida del proceso, como configurar un temporizador después del inicio del proceso o inicializar la conexión con la base de datos, entre otros. A continuación, explicaremos esto.

## Principio
Según la explicación en **[Proceso de Ejecución](process.md)**, después de que webman inicia el proceso, cargará las clases configuradas en `config/bootstrap.php` (incluyendo `config/plugin/*/*/bootstrap.php`) y ejecutará el método start de las clases. Podemos agregar código del negocio en el método start para completar la inicialización del negocio después del inicio del proceso.

## Proceso
Supongamos que queremos crear un temporizador para reportar el uso de memoria actual del proceso a intervalos regulares, y llamaremos a esta clase `MemReport`.

#### Comando de ejecución

Ejecute el comando `php webman make:bootstrap MemReport` para generar el archivo de inicialización `app/bootstrap/MemReport.php`

> **Consejo**
> Si su entorno webman no tiene instalado `webman/console`, ejecute el comando `composer require webman/console` para instalarlo.

#### Edite el archivo de inicialización
Edite `app/bootstrap/MemReport.php`, el contenido será similar al siguiente:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // ¿Es un entorno de línea de comandos?
        $is_console = !$worker;
        if ($is_console) {
            // Si no quieres que esta inicialización se ejecute en un entorno de línea de comandos, puedes regresar directamente aquí.
            return;
        }
        
        // Ejecutar cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Para simplificar la demostración, aquí usamos la salida en lugar del proceso de reporte
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Consejo**
> Cuando se utiliza la línea de comandos, el framework también ejecutará el método start configurado en `config/bootstrap.php`. Podemos determinar si es un entorno de línea de comandos o no, según si `$worker` es nulo, y así decidir si ejecutar el código de inicialización del negocio.

#### Configuración para iniciar con el proceso
Abra `config/bootstrap.php` y agregue la clase `MemReport` a los elementos de inicio.
```php
return [
    // ...Otras configuraciones omitidas...

    app\bootstrap\MemReport::class,
];
```

De esta forma, hemos completado el proceso de inicialización del negocio.

## Consideraciones adicionales
Después del inicio de un [proceso personalizado](../process.md), también se ejecutará el método `start` configurado en `config/bootstrap.php`. Podemos determinar qué proceso está en ejecución según `$worker->name` y decidir si ejecutar su código de inicialización del negocio. Por ejemplo, si no necesitamos monitorear el proceso de monitor, el contenido de `MemReport.php` será similar a lo siguiente:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // ¿Es un entorno de línea de comandos?
        $is_console = !$worker;
        if ($is_console) {
            // Si no quieres que esta inicialización se ejecute en un entorno de línea de comandos, puedes regresar directamente aquí.
            return;
        }
        
        // El proceso de monitoreo no ejecuta el temporizador
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Ejecutar cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Para simplificar la demostración, aquí usamos la salida en lugar del proceso de reporte
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
