# Управление сеансом

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

Получите экземпляр `Workerman\Protocols\Http\Session` через `$request->session();` и используйте методы экземпляра для добавления, изменения и удаления данных сеанса.

> Заметка: При уничтожении объекта сеанса данные сеанса автоматически сохраняются. Поэтому не сохраняйте объект, возвращаемый `$request->session()`, в глобальном массиве или в члене класса, чтобы избежать потери сеанса.

## Получение всех данных сеанса
```php
$session = $request->session();
$all = $session->all();
```
Возвращается массив. Если сеансовых данных нет, то возвращается пустой массив.

## Получение определенного значения из сеанса
```php
$session = $request->session();
$name = $session->get('name');
```
Если данных не существует, возвращается null.

Также вы можете передать вторым аргументом метода `get` значение по умолчанию. Если соответствие в сеансовом массиве не найдено, возвращается значение по умолчанию. Например:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Сохранение данных сеанса
Для сохранения данных используйте метод `set`.
```php
$session = $request->session();
$session->set('name', 'tom');
```
Метод `set` ничего не возвращает; данные сеанса будут автоматически сохранены при уничтожении объекта сеанса.

При сохранении нескольких значений используйте метод `put`.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
`Put` также ничего не возвращает.

## Удаление данных сеанса
Для удаления одного или нескольких данных сеанса используйте метод `forget`.
```php
$session = $request->session();
// Удалить одно значение
$session->forget('name');
// Удалить несколько значений
$session->forget(['name', 'age']);
```
Также доступен метод `delete`, отличие от метода `forget` в том, что `delete` может удалить только одно значение.
```php
$session = $request->session();
// То же, что и $session->forget('name');
$session->delete('name');
```

## Получение и удаление значения из сеанса
```php
$session = $request->session();
$name = $session->pull('name');
```
Этот код эквивалентен следующему:
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Если соответствующее значение сеанса не существует, возвращается null.

## Удаление всех данных сеанса
```php
$request->session()->flush();
```
Метод ничего не возвращает; данные сеанса будут автоматически удалены из хранилища при уничтожении объекта сеанса.

## Проверка существования данных в сеансе
```php
$session = $request->session();
$has = $session->has('name');
```
Если соответствующий сеанс отсутствует или его значение равно null, возвращается false, в противном случае возвращается true.

```php
$session = $request->session();
$has = $session->exists('name');
```
Этот код также используется для проверки существования данных сеанса, однако если значение соответствующего сеанса равно null, то возвращается true.

## Функция-помощник session()
> Добавлено 9 декабря 2020

webman предоставляет функцию-помощник `session()` для выполнения тех же функций.
```php
// Получение экземпляра сеанса
$session = session();
// Эквивалентно
$session = $request->session();

// Получение значения
$value = session('key', 'default');
// То же самое, что и 
$value = session()->get('key', 'default');
// Или
$value = $request->session()->get('key', 'default');

// Запись в сеанс
session(['key1'=>'value1', 'key2' => 'value2']);
// Эквивалентно
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Или
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## Файл конфигурации
Файл конфигурации сеанса находится в `config/session.php` и имеет следующий вид::
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class или RedisSessionHandler::class или RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Для FileSessionHandler::class значение 'file'; для RedisSessionHandler::class значение 'redis'; для RedisClusterSessionHandler::class значение 'redis_cluster' (кластер Redis)
    'type'    => 'file',

    // Различные параметры для различных обработчиков
    'config' => [
        // Параметры для type 'file'
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Параметры для type 'redis'
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
    
    // === Настройки, требующие webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Автоматическое обновление сеанса, по умолчанию отключено
    'lifetime' => 7*24*60*60,          // Время жизни сеанса
    'cookie_lifetime' => 365*24*60*60, // Время жизни cookie с session_id
    'cookie_path' => '/',              // Путь к cookie с session_id
    'domain' => '',                    // Домен для cookie с session_id
    'http_only' => true,               // Включен ли httpOnly, по умолчанию включен
    'secure' => false,                 // Включение сеанса только в HTTPS, по умолчанию отключено
    'same_site' => '',                 // Защита от атак CSRF и отслеживания пользователей, доступные значения: strict/lax/none
    'gc_probability' => [1, 1000],     // Вероятность очистки сеанса
];
```

> **Примечание** 
> Начиная с версии webman 1.4.0 пространство имен SessionHandler было изменено с 
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> на  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## Настройка срока действия
Если версия webman-framework < 1.3.14, то время жизни сеанса в webman-е нужно настраивать в `php.ini`.

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Если сеанс должен иметь срок действия 1440 секунд, то параметры будут следующие:
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Совет**
> Вы можете использовать команду `php --ini` для поиска расположения `php.ini`.
