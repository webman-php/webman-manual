# Componente de tareas programadas crontab

## workerman/crontab

### Descripción

`workerman/crontab` es similar al crontab de Linux, la diferencia es que `workerman/crontab` admite programación a nivel de segundos.

Explicación del tiempo:

```plaintext
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ día de la semana (0 - 6) (Domingo=0)
|   |   |   |   +------ mes (1 - 12)
|   |   |   +-------- día del mes (1 - 31)
|   |   +---------- hora (0 - 23)
|   +------------ minuto (0 - 59)
+-------------- segundo (0-59)[Puede omitirse, si no hay 0, la granularidad de tiempo mínima es el minuto]
```

### Dirección del proyecto

https://github.com/walkor/crontab

### Instalación

```php
composer require workerman/crontab
```

### Uso

**Paso 1: Crear el archivo de proceso `process/Task.php`**

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
      
        // Ejecutar a las 7:50 todos los días, aquí se omite la posición de los segundos
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Paso 2: Configurar el archivo de proceso para iniciar junto con webman**

Abrir el archivo de configuración `config/process.php` y agregar la siguiente configuración

```php
return [
    ....otras configuraciones, omitidas por brevedad....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Paso 3: Reiniciar webman**

> Nota: Las tareas programadas no se ejecutarán de inmediato; comenzarán a ejecutarse en el próximo minuto.

### Notas
El crontab no es asíncrono. Por ejemplo, si un proceso de tarea configura dos temporizadores, A y B, para ejecutarse cada segundo, pero la tarea A tarda 10 segundos en ejecutarse, entonces B tendrá que esperar a que A se complete antes de ejecutarse, lo que ocasionará un retraso en la ejecución de B.
Si el negocio es muy sensible al intervalo de tiempo, es necesario ejecutar las tareas programadas sensibles al tiempo en un proceso separado para evitar que sean afectadas por otras tareas programadas. Por ejemplo, en `config/process.php` se puede hacer la siguiente configuración

```php
return [
    ....otras configuraciones, omitidas por brevedad....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```

Colocar las tareas programadas sensibles al tiempo en `process/Task1.php` y las demás tareas programadas en `process/Task2.php`.

### Más
Para obtener más información sobre la configuración de `config/process.php`, consulte [Proceso personalizado](../process.md)
