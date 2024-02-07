# Manejo de eventos

`webman/event` proporciona un mecanismo de eventos ingenioso que puede ejecutar la lógica empresarial sin invadir el código, logrando el desacoplamiento entre los módulos de negocios. Un escenario típico es cuando un nuevo usuario se registra con éxito, simplemente publicando un evento personalizado como `user.register`, todos los módulos pueden recibir ese evento y ejecutar la lógica empresarial correspondiente.

## Instalación
`composer require webman/event`

## Suscribirse a eventos
La suscripción a eventos se configura de forma unificada a través del archivo `config/event.php`.
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...otras funciones de manejo de eventos...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...otras funciones de manejo de eventos...
    ]
];
```
**Nota:**
- `user.register`, `user.logout`, etc., son nombres de eventos, de tipo cadena, se recomienda en minúsculas y separados por punto (`.`).
- Un evento puede tener múltiples funciones de manejo de eventos, que se ejecutan en el orden configurado.

## Funciones de manejo de eventos
Las funciones de manejo de eventos pueden ser cualquier método de clase, función, función anónima, etc.
Por ejemplo, crea la clase de manejo de eventos `app/event/User.php` (crea el directorio si no existe).
```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## Publicar eventos
Usa `Event::emit($event_name, $data);` para publicar eventos, por ejemplo:
```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```

> **Nota**
> El parámetro `$data` de `Event::emit($event_name, $data);` puede ser cualquier tipo de datos, como un array, una instancia de clase, una cadena, etc.

## Escuchar eventos con comodines
Los registros de escucha de comodines le permiten manejar múltiples eventos en el mismo escuchador, por ejemplo, configurado en `config/event.php`
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
Podemos obtener el nombre de evento específico a través del segundo parámetro `$event_data` de la función de manejo de eventos
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // nombre de evento específico, como user.register, user.logout, etc.
        var_export($user);
    }
}
```

## Detener la difusión de eventos
Cuando regresamos `false` en la función de manejo de eventos, se detendrá la difusión del evento.

## Función de manejo de eventos como función anónima
La función de manejo de eventos puede ser un método de clase o una función anónima, por ejemplo

```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

## Ver eventos y escuchadores
Usa el comando `php webman event:list` para ver todos los eventos y escuchadores configurados en el proyecto.

## Consideraciones
El manejo de eventos no es asíncrono. No es adecuado para manejar procesos lentos. Los procesos lentos deberían ser manejados mediante colas de mensajes, como [webman/redis-queue](https://www.workerman.net/plugin/12).
