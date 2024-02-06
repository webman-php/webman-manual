# Пользовательские процессы

В webman вы можете создавать пользовательские процессы для прослушивания или обработки, подобно тому, как это делается в workerman.

> **Примечание**
> Пользователям Windows нужно использовать `php windows.php` для запуска webman и пользовательских процессов.

## Пользовательский HTTP-сервер
Иногда у вас может возникнуть специфическая потребность в изменении ядра HTTP-сервера webman. В этом случае можно использовать пользовательский процесс для реализации этой задачи.

Например, создайте файл app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Здесь переопределяются методы из Webman\App
}
```

Добавьте следующую конфигурацию в `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Другие конфигурации...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // Количество процессов
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // Установить класс запроса
            'logger' => \support\Log::channel('default'), // Экземпляр журнала
            'app_path' => app_path(), // Местоположение каталога приложения
            'public_path' => public_path() // Местоположение каталога public
        ]
    ]
];
```

> **Подсказка**
> Если вы хотите отключить встроенный HTTP-сервер webman, просто установите `listen=>''` в файле конфигурации `config/server.php`.

## Пример пользовательского прослушивания WebSocket

Создайте файл `app/Pusher.php`
```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> Обратите внимание: все свойства onXXX являются public.

Добавьте следующую конфигурацию в `config/process.php`
```php
return [
    // ... Другие конфигурации процессов...
    
    // websocket_test - это имя процесса
    'websocket_test' => [
        // Здесь указывается класс процесса, который был определен выше - Pusher
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## Пример пользовательского процесса без прослушивания

Создайте файл `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Каждые 10 секунд проверяем, зарегистрировался ли новый пользователь в базе данных
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
Добавьте следующую конфигурацию в `config/process.php`
```php
return [
    // ... Другие конфигурации процессов...
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

> Обратите внимание: если пропустить параметр listen, то процесс не будет прослушивать никакой порт, если пропустить параметр count, то количество процессов будет по умолчанию равно 1.

## Объяснение файла конфигурации

Полная настройка процесса выглядит следующим образом:
```php
return [
    // ... 
    
    // websocket_test - это имя процесса
    'websocket_test' => [
        // Здесь указывается класс процесса
        'handler' => app\Pusher::class,
        // Протокол, IP и порт для прослушивания (необязательно)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Количество процессов (необязательно, по умолчанию 1)
        'count'   => 2,
        // Пользователь для запуска процесса (необязательно, по умолчанию текущий пользователь)
        'user'    => '',
        // Группа пользователя для запуска процесса (необязательно, по умолчанию текущая группа пользователя)
        'group'   => '',
        // Поддерживает ли процесс перезапуск (необязательно, по умолчанию true)
        'reloadable' => true,
        // Включить ли reusePort (необязательно, требуется PHP>=7.0, по умолчанию true)
        'reusePort'  => true,
        // Транспорт (необязательно, установить в ssl, когда требуется ssl, по умолчанию tcp)
        'transport'  => 'tcp',
        // Контекст (необязательно, установить путь к сертификату, когда установлен транспорт в ssl)
        'context'    => [], 
        // Параметры конструктора процесса, здесь мы указываем параметры конструктора класса process\Pusher::class (необязательно)
        'constructor' => [],
    ],
];
```

## Вывод
Пользовательские процессы в webman фактически являются простым оболочкой для workerman. Они разделяют конфигурацию и бизнес-логику, и реализуют обратные вызовы `onXXX` workerman через методы класса, остальные возможности остаются такими же, как в workerman.
