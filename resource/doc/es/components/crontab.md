# Componente de tareas programadas crontab

## workerman/crontab

### Descripción

`workerman/crontab` es similar a crontab en Linux, pero la diferencia es que `workerman/crontab` admite tareas programadas a nivel de segundos.

Explicación del tiempo:

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ día de la semana (0 - 6) (Domingo=0)
|   |   |   |   +------ mes (1 - 12)
|   |   |   +-------- día del mes (1 - 31)
|   |   +---------- hora (0 - 23)
|   +------------ minutos (0 - 59)
+-------------- segundos (0-59) [Opcional, si no está presente, la unidad de tiempo mínima es el minuto]
```

### Dirección del proyecto

https://github.com/walkor/crontab

### Instalación

```php
composer require workerman/crontab
```

### Uso

**Paso 1: Crear un archivo de proceso `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Ejecutar cada segundo
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Ejecutar cada 5 segundos
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Ejecutar cada minuto
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Ejecutar cada 5 minutos
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Ejecutar en el primer segundo de cada minuto
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Ejecutar a las 7:50 todos los días, tenga en cuenta que se omitió el campo de segundos aquí
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Paso 2: Configurar el archivo de proceso para iniciar con webman**

Abrir el archivo de configuración `config/process.php` y agregar la siguiente configuración

```php
return [
    ....otras configuraciones, se omiten aquí....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Paso 3: Reiniciar webman**

> Nota: Las tareas programadas no se ejecutarán de inmediato; comenzarán a ejecutarse en el próximo minuto.

### Explicación

crontab no es asíncrono, por ejemplo, si un proceso de tarea programa dos temporizadores, A y B, ambos se ejecutan cada segundo, pero la tarea A toma 10 segundos, entonces B debe esperar a que A termine antes de ser ejecutado, lo que causa un retraso en la ejecución de B. Si la aplicación es sensible al intervalo de tiempo, es necesario ejecutar las tareas programadas sensibles al tiempo en un proceso separado para evitar que otras tareas programadas las afecten. Por ejemplo, en `config/process.php` se hace la siguiente configuración:

```php
return [
    ....otras configuraciones, se omiten aquí....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Se pueden colocar las tareas programadas sensibles al tiempo en `process/Task1.php` y las demás tareas programadas en `process/Task2.php`.

### Más información
Para obtener más información sobre la configuración en `config/process.php`, consulte [Proceso personalizado](../process.md)
