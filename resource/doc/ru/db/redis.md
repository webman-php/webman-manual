# Redis

Компонент redis webman по умолчанию использует [illuminate/redis](https://github.com/illuminate/redis), что является библиотекой redis для laravel, и его использование аналогично laravel.

Перед использованием `illuminate/redis` необходимо установить расширение redis для `php-cli`.

> **Внимание**
> Для проверки того, установлено ли расширение redis для `php-cli`, используйте команду `php -m | grep redis`. Обратите внимание: даже если вы установили расширение redis для `php-fpm`, это не означает, что вы можете использовать его в `php-cli`, потому что `php-cli` и `php-fpm` - это разные приложения, которые могут использовать разные файлы конфигурации `php.ini`. Используйте команду `php --ini`, чтобы проверить, какой файл конфигурации `php.ini` используется вашим `php-cli`.

## Установка

```php
composer require -W illuminate/redis illuminate/events
```

После установки требуется перезагрузить (нельзя использовать reload).

## Настройка
Файл настройки redis находится в `config/redis.php`
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

## Пример
```php
<?php
namespace app\controller;

use support\Request;
use support\Redis;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Redis::set($key, rand());
        return response(Redis::get($key));
    }
}
```

## Интерфейс Redis
```php
Redis::append($key, $value)
Redis::bitCount($key)
Redis::decr($key, $value)
Redis::decrBy($key, $value)
Redis::get($key)
Redis::getBit($key, $offset)
Redis::getRange($key, $start, $end)
Redis::getSet($key, $value)
Redis::incr($key, $value)
Redis::incrBy($key, $value)
Redis::incrByFloat($key, $value)
Redis::mGet(array $keys)
Redis::getMultiple(array $keys)
Redis::mSet($pairs)
Redis::mSetNx($pairs)
Redis::set($key, $value, $expireResolution = null, $expireTTL = null, $flag = null)
Redis::setBit($key, $offset, $value)
Redis::setEx($key, $ttl, $value)
Redis::pSetEx($key, $ttl, $value)
Redis::setNx($key, $value)
Redis::setRange($key, $offset, $value)
Redis::strLen($key)
Redis::del(...$keys)
Redis::exists(...$keys)
Redis::expire($key, $ttl)
Redis::expireAt($key, $timestamp)
Redis::select($dbIndex)
```
Эквивалентно
```php
$redis = Redis::connection('default');
$redis->append($key, $value)
$redis->bitCount($key)
$redis->decr($key, $value)
$redis->decrBy($key, $value)
$redis->get($key)
$redis->getBit($key, $offset)
...
```

> **Внимание**
> Будьте осторожны с интерфейсом `Redis::select($db)`, поскольку webman - это постоянный фреймворк в памяти, если один запрос использует `Redis::select($db)` для переключения базы данных, это повлияет на последующие запросы. Для многодatabase рекомендуется настроить разные настройки `$db` для разных конфигураций подключения Redis.

## Использование нескольких соединений с Redis
Например, файл настройки `config/redis.php`
```php
return [
    'default' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 0,
    ],

    'cache' => [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'database' => 1,
    ],

]
```
По умолчанию используется соединение, настроенное в разделе `default`, но вы можете использовать метод `Redis::connection()` для выбора используемого соединения redis.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Настройка кластера
Если ваше приложение использует кластер серверов Redis, вам следует использовать ключ clusters в файле конфигурации Redis для определения этих кластеров:
```php
return [
    'clusters' => [
        'default' => [
            [
                'host'     => 'localhost',
                'password' => null,
                'port'     => 6379,
                'database' => 0,
            ],
        ],
    ],

];
```

По умолчанию кластер может реализовывать клиентские фрагменты на узлах, позволяя вам создавать пул узлов и создавать большое количество доступной памяти. Важно отметить, что клиентские секции не обрабатывают сбои; поэтому эта функция предназначена в основном для получения кеша данных из другой основной базы данных. Если вы хотите использовать нативный кластер Redis, вам нужно указать следующее в разделе опций файла конфигурации:

```php
return[
    'options' => [
        'cluster' => 'redis',
    ],

    'clusters' => [
        // ...
    ],
];
```

## Команды канала
Если вам нужно отправить много команд на сервер в одной операции, рекомендуется использовать команды канала. Метод pipeline принимает замыкание Redis экземпляра. Вы можете отправить все команды к Redis экземпляру, и они все будут выполнены в рамках одной операции:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
