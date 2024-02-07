# Event Event Handling

`webman/event` provides an elegant event mechanism that allows you to execute some business logic without modifying the code, achieving decoupling between business modules. For example, when a new user is successfully registered, you can simply publish a custom event like `user.register`, and each module can receive the event and execute the corresponding business logic.

## Installation
`composer require webman/event`

## Subscribe to Events
Event subscriptions are configured through the `config/event.php` file.

```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...other event handling functions...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...other event handling functions...
    ]
];
```

**Note:**
- `user.register` and `user.logout` are event names, which are strings. It's recommended to use lowercase words separated by dots (`.`).
- An event can have multiple event handling functions, which are executed in the order specified in the configuration.

## Event Handling Functions
Event handling functions can be any class methods, functions, closure functions, etc. 

For example, create an event handling class `app/event/User.php` (create the directory if it doesn't exist).

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

## Publish an Event
Use `Event::emit($event_name, $data);` to publish an event. For example:

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

> **Note:**
> The `$data` parameter of `Event::emit($event_name, $data);` can be any data, such as an array, an object instance, a string, etc.

## Wildcard Event Listening
You can use wildcard event subscriptions to handle multiple events on the same listener. For example, in the `config/event.php` file:

```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```

In the event handling function, you can get the specific event name through the second parameter `$event_data`.

```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // specific event name, such as user.register, user.logout, etc.
        var_export($user);
    }
}
```

## Stopping Event Broadcasting
When we return `false` in the event handling function, the event broadcasting will be stopped.

## Closure Function for Event Handling
The event handling function can also be a closure function. For example:

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

## Viewing Events and Listeners
Use the command `php webman event:list` to view all the events and listeners configured in the project.

## Note
Event handling is not asynchronous. Events are not suitable for handling slow business logic, which should be done using message queues. For example, [webman/redis-queue](https://www.workerman.net/plugin/12).
