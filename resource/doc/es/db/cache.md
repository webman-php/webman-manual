# Caché

En webman, se utiliza por defecto [symfony/cache](https://github.com/symfony/cache) como componente de caché.

> Antes de utilizar `symfony/cache`, es necesario instalar la extensión de redis para `php-cli`.

## Instalación
**php 7.x**
```php
composer require -W illuminate/redis ^8.2.0 symfony/cache ^5.2
```
**php 8.x**
```php
composer require -W illuminate/redis symfony/cache
```

Después de la instalación, es necesario reiniciar (reload no es efectivo).

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
> Es recomendable agregar un prefijo a la clave para evitar conflictos con otros servicios que utilicen redis.

## Uso de otros componentes de caché

El uso del componente [ThinkCache](https://github.com/top-think/think-cache) se puede encontrar en [Otros databases](others.md#ThinkCache).
