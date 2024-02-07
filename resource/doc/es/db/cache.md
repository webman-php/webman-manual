# Caché

En webman, el componente de caché por defecto utiliza [symfony/cache](https://github.com/symfony/cache).

> Antes de usar `symfony/cache`, es necesario instalar la extensión de redis para `php-cli`.

## Instalación
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Después de la instalación, se requiere reiniciar (reload no es efectivo).

## Configuración de Redis
El archivo de configuración de redis se encuentra en `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ]
];
```

## Ejemplo
```php
<?php
namespace app\controller;

use support\Request;
use support\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

> **Nota**
> Es recomendable añadir un prefijo a la clave para evitar conflictos con otros usos de redis.

## Uso de Otros Componentes de Caché
Para utilizar el componente [ThinkCache](https://github.com/top-think/think-cache), consulta [Other Databases](others.md#ThinkCache).
