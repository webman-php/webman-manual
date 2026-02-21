# Limitador de tasa

Limitador de tasa de webman con soporte de limitación por anotación.
Soporta drivers apcu, redis y memory.

## Repositorio fuente

https://github.com/webman-php/limiter

## Instalación

```
composer require webman/limiter
```

## Uso

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // Por defecto limitación por IP, ventana de tiempo por defecto 1 segundo
        return 'Máximo 10 peticiones por IP por segundo';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID, limitación por ID de usuario, requiere session('user.id') no vacía
        return 'Máximo 100 búsquedas por usuario por 60 segundos';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: 'Solo 1 email por persona por minuto')]
    public function sendMail(): string
    {
        // key: Limit::SID, limitación por session_id
        return 'Email enviado correctamente';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: 'Cupones de hoy agotados, intente mañana')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: 'Cada usuario solo puede reclamar un cupón por día')]
    public function coupon(): string
    {
        // key: 'coupon', clave personalizada para limitación global, máx 100 cupones por día
        // También limitación por ID de usuario, cada usuario un cupón por día
        return 'Cupón enviado correctamente';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: 'Máximo 5 SMS por número por día')]
    public function sendSms2(): string
    {
        // Cuando key es variable: [clase, método_estático], ej. [UserController::class, 'getMobile'] usa el valor de retorno de UserController::getMobile() como clave
        return 'SMS enviado correctamente';
    }

    /**
     * Clave personalizada, obtener número móvil, debe ser método estático
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'Tasa limitada', exception: RuntimeException::class)]
    public function testException(): string
    {
        // Excepción por defecto al exceder: support\limiter\RateLimitException, modificable vía parámetro exception
        return 'ok';
    }

}
```

**Notas**

* Usa algoritmo de ventana fija
* Ventana de tiempo ttl por defecto: 1 segundo
* Configurar ventana vía ttl, ej. `ttl:60` para 60 segundos
* Dimensión de limitación por defecto: IP (por defecto `127.0.0.1` no limitado, ver configuración abajo)
* Integrado: limitación IP, UID (requiere `session('user.id')` no vacía), SID (por `session_id`)
* Con proxy nginx, pasar cabecera `X-Forwarded-For` para limitación IP, ver [proxy nginx](../others/nginx-proxy.md)
* Dispara `support\limiter\RateLimitException` al exceder, clase de excepción personalizada vía `exception:xx`
* Mensaje de error por defecto al exceder: `Too Many Requests`, mensaje personalizado vía `message:xx`
* Mensaje de error por defecto modificable vía [traducción](translation.md), referencia Linux:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

A veces los desarrolladores quieren llamar al limitador directamente en el código, ver el siguiente ejemplo:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // mobile se usa como clave aquí
        Limiter::check($mobile, 5, 24*60*60, 'Máximo 5 SMS por número por día');
        return 'SMS enviado correctamente';
    }
}
```

## Configuración

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // Estas IP no están limitadas (solo efectivo cuando key es Limit::IP)
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: Activar limitación de tasa
* **driver**: Uno de `auto`, `apcu`, `memory`, `redis`; `auto` elige automáticamente entre `apcu` (prioritario) y `memory`
* **stores**: Configuración Redis, `connection` corresponde a la clave en `config/redis.php`
* **ip_whitelist**: Las IP en lista blanca no están limitadas (solo efectivo cuando key es `Limit::IP`)

## Selección de driver

**memory**

* Introducción
  Sin extensiones requeridas, mejor rendimiento.

* Limitaciones
  Limitación solo para el proceso actual, sin compartir datos entre procesos, limitación en cluster no soportada.

* Casos de uso
  Entorno de desarrollo Windows; escenarios sin limitación estricta; defensa contra ataques CC.

**apcu**

* Instalación de extensión
  Extensión apcu requerida, configuración php.ini:

```
apc.enabled=1
apc.enable_cli=1
```

Ubicación de php.ini con `php --ini`

* Introducción
  Excelente rendimiento, soporta compartir datos multi-proceso.

* Limitaciones
  Cluster no soportado

* Casos de uso
  Cualquier entorno de desarrollo; limitación servidor único en producción; cluster sin limitación estricta; defensa contra ataques CC.

**redis**

* Dependencias
  Extensión redis y componente Redis requeridos, instalación:

```
composer require -W webman/redis illuminate/events
```

* Introducción
  Rendimiento inferior a apcu, soporta limitación precisa servidor único y cluster

* Casos de uso
  Entorno de desarrollo; servidor único en producción; entorno cluster
