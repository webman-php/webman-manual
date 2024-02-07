# Redis

Компонент Redis в webman по умолчанию использует [illuminate/redis](https://github.com/illuminate/redis), то есть библиотеку Redis из Laravel, которая используется также, как и в Laravel.

Перед использованием `illuminate/redis`, необходимо установить расширение Redis для `php-cli`.

> **Обратите внимание**
> Для проверки установлено ли расширение Redis для `php-cli`, используйте команду `php -m | grep redis`. Обратите внимание, что даже если вы установили расширение Redis для `php-fpm`, это не означает, что вы можете его использовать в `php-cli`, поскольку `php-cli` и `php-fpm` - это разные приложения, вероятно, использующие разные файлы конфигурации `php.ini`. Чтобы узнать, какой файл конфигурации `php.ini` используется для `php-cli`, используйте команду `php --ini`.

## Установка

```php
composer require -W illuminate/redis illuminate/events
```

После установки необходимо выполнить restart (reload не работает).

## Конфигурация

Файл конфигурации Redis находится в `config/redis.php`
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
Аналогично
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

> **Обратите внимание**
> Будьте осторожны при использовании интерфейса `Redis::select($db)`, поскольку webman - это постоянный фреймворк, и если один запрос использует `Redis::select($db)` для изменения базы данных, это повлияет на последующие запросы. Для использования нескольких баз данных рекомендуется использовать различные настройки `$db` для разных подключений Redis.

## Использование нескольких подключений к Redis

Например, файл конфигурации `config/redis.php`
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
По умолчанию используется подключение, настроенное в разделе `default`, однако вы можете использовать метод `Redis::connection()` для выбора подключения к Redis.
```php
$redis = Redis::connection('cache');
$redis->get('test_key');
```

## Конфигурация кластера

Если ваше приложение использует кластер серверов Redis, вам следует использовать ключи clusters в файле конфигурации Redis для определения этих кластеров:
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

По умолчанию кластер может реализовать клиентское фрагментирование на узлах, позволяя вам реализовать пул узлов и создавать большое количество доступной памяти. Однако следует отметить, что общий клиент не обрабатывает сбои; поэтому эта функция главным образом подходит для кеширования данных, полученных из другой основной базы данных. Если требуется использовать нативный кластер Redis, необходимо указать это в разделе options файла конфигурации:
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

## Команды в конвейере

Когда вам нужно отправить много команд серверу в одной операции, целесообразно использовать команды в конвейере. Метод pipeline принимает замыкание экземпляра Redis. Вы можете отправить все команды в экземпляр Redis, и они будут выполнены в одной операции:
```php
Redis::pipeline(function ($pipe) {
    for ($i = 0; $i < 1000; $i++) {
        $pipe->set("key:$i", $i);
    }
});
```
