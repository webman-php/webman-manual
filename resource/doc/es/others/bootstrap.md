# Inicialización del negocio

A veces necesitamos realizar una inicialización del negocio después de que el proceso se inicie, esta inicialización se ejecuta solo una vez durante el ciclo de vida del proceso, por ejemplo, configurar un temporizador después del inicio del proceso o establecer una conexión de base de datos. A continuación, explicaremos esto.

## Principio
Según la explicación en **[Flujo de ejecución](process.md)**, webman cargará las clases configuradas en `config/bootstrap.php` (incluidas en `config/plugin/*/*/bootstrap.php`) después de que el proceso se inicie, y ejecutará el método start de las clases. Podemos agregar código de negocio en el método start para completar la inicialización del negocio después del inicio del proceso.

## Proceso
Supongamos que queremos crear un temporizador para informar periódicamente el uso de memoria actual del proceso. Esta clase se llamará `MemReport`.

#### Ejecutar comando
Ejecutar el comando `php webman make:bootstrap MemReport` para generar el archivo de inicialización `app/bootstrap/MemReport.php`.

> **Consejo**
> Si webman no tiene instalado `webman/console`, ejecuta el comando `composer require webman/console` para instalarlo.

#### Editar archivo de inicialización
Edite `app/bootstrap/MemReport.php` con un contenido similar al siguiente:

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
            // Si no quieres que esta inicialización se ejecute en un entorno de línea de comandos, simplemente devuelve aquí.
            return;
        }
        
        // Ejecutar cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Para propósitos de demostración, aquí utilizamos la salida en lugar de un informe real.
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Consejo**
> Cuando se usa la línea de comandos, el marco también ejecutará el método start configurado en `config/bootstrap.php`. Podemos determinar si es un entorno de línea de comandos o no a través de la variable `$worker`, y decidir si ejecutar el código de inicialización del negocio.

#### Configurar para el inicio del proceso
Abra `config/bootstrap.php` y agregue la clase `MemReport` a la lista de inicio.

```php
return [
    // ... Se omiten otras configuraciones...
    
    app\bootstrap\MemReport::class,
];
```

De esta manera, completamos el proceso de inicialización del negocio.

## Nota adicional
Después de que se inicia un [proceso personalizado](../process.md), también se ejecutará el método start configurado en `config/bootstrap.php`. Podemos utilizar `$worker->name` para determinar qué tipo de proceso es y luego decidir si ejecutar su código de inicialización del negocio. Por ejemplo, si no necesitamos monitorear el proceso "monitor", el contenido de `MemReport.php` será similar al siguiente:

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
            // Si no quieres que esta inicialización se ejecute en un entorno de línea de comandos, simplemente devuelve aquí.
            return;
        }
        
        // El proceso monitor no ejecuta un temporizador
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Ejecutar cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Para propósitos de demostración, aquí utilizamos la salida en lugar de un informe real.
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
