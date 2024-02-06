# Управление сессиями

## Пример
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

Получите экземпляр `Workerman\Protocols\Http\Session` с помощью `$request->session();`, и используйте методы экземпляра для добавления, изменения или удаления данных сессии.

> Обратите внимание: при уничтожении объекта сессии данные сессии автоматически сохраняются, поэтому не сохраняйте объект, возвращенный `$request->session()`, в глобальном массиве или как член класса, чтобы избежать невозможности сохранения сессии.

## Получение всех данных сессии
```php
$session = $request->session();
$all = $session->all();
```
Возвращает массив. Если нет никаких данных сессии, то возвращается пустой массив.

## Получение определенного значения из сессии
```php
$session = $request->session();
$name = $session->get('name');
```
Если данные отсутствуют, возвращается null.

Также можно передать второй аргумент метода get значение по умолчанию, если соответствующее значение в сессии не найдено.
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Сохранение сессии
Для сохранения данных используйте метод set:
```php
$session = $request->session();
$session->set('name', 'tom');
```
Метод set ничего не возвращает, данные сессии сохраняются автоматически при уничтожении объекта сессии.

При сохранении нескольких значений использовать метод put:
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Аналогично, метод put ничего не возвращает.


## Удаление данных сессии
Для удаления одного или нескольких данных сессии используйте метод `forget`:
```php
$session = $request->session();
// Удалить один элемент
$session->forget('name');
// Удалить несколько элементов
$session->forget(['name', 'age']);
```

Также имеется метод `delete`, который отличается от `forget` только тем, что удаляется только один элемент.
```php
$session = $request->session();
// Эквивалентно $session->forget('name');
$session->delete('name');
```

## Получение и удаление значения из сессии
```php
$session = $request->session();
$name = $session->pull('name');
```
Это эквивалентно следующему коду:
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Если соответствующая сессия отсутствует, возвращается null.


## Удаление всех данных сессии
```php
$request->session()->flush();
```
Это действие ничего не возвращает, и данные сессии автоматически удаляются из хранилища при уничтожении объекта сессии.

## Проверка существования соответствующих данных сессии
```php
$session = $request->session();
$has = $session->has('name');
```
Если соответствующая сессия отсутствует или значение сессии равно null, возвращается false, в противном случае возвращается true.

```php
$session = $request->session();
$has = $session->exists('name');
```
Этот код также используется для проверки существования данных сессии, но в отличие от предыдущего метода, когда значение соответствующего элемента сессии равно null, также возвращается true.


## Функция-помощник session()
> Добавлено 09-12-2020

webman предоставляет функцию-помощник `session()`, выполняющую те же функции.

```php
// Получение объекта сессии
$session = session();
// Эквивалентно
$session = $request->session();

// Получение значения
$value = session('key', 'default');
// То же, что
$value = session()->get('key', 'default');
// Эквивалентно
$value = $request->session()->get('key', 'default');

// Сохранение значений в сессии
session(['key1'=>'value1', 'key2' => 'value2']);
// То же, что
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Эквивалентно
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## Файл конфигурации
Файл конфигурации сессий находится в `config/session.php`, и его содержимое выглядит примерно так:

```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class или RedisSessionHandler::class или RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // при использовании FileSessionHandler::class, значение 'file'
    // при использовании RedisSessionHandler::class, значение 'redis'
    // при использовании RedisClusterSessionHandler::class, значение 'redis_cluster' (кластер redis)
    'type'    => 'file',

    // Разные обработчики используют разные конфигурации
    'config' => [
        // конфигурация для типа file
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // конфигурация для типа redis
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // Имя cookie для хранения session_id
    
    // === Настройки доступны в webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Автоматическое обновление сессии, по умолчанию отключено
    'lifetime' => 7*24*60*60,          // Время жизни сессии
    'cookie_lifetime' => 365*24*60*60, // Время жизни cookie с session_id
    'cookie_path' => '/',              // Путь к cookie с session_id
    'domain' => '',                    // Домен cookie с session_id
    'http_only' => true,               // Опция httpOnly, по умолчанию включена
    'secure' => false,                 // Включение сессий только в https, по умолчанию отключено
    'same_site' => '',                 // Защита от атак CSRF и отслеживания пользователей, доступные значения: strict/lax/none
    'gc_probability' => [1, 1000],     // Вероятность очистки сессии
];
```

> **Примечание**
> С версии webman 1.4.0 пространство имен SessionHandler было изменено с
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;
> на
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  


## Настройка срока действия
Для версии webman-framework < 1.3.14 срок действия сессии настраивается в `php.ini`.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Допустим, установлен срок действия 1440 секунд, то настройки будут такими:
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Подсказка** 
> Можно использовать команду `php --ini` для поиска расположения `php.ini`.
