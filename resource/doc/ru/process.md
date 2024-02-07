# Пользовательские процессы

В webman вы можете создавать пользовательские прослушивающие или рабочие процессы, подобно workerman.

> **Заметка**
> Пользователям Windows нужно использовать `php windows.php` для запуска webman и пользовательских процессов.

## Пользовательский HTTP-сервер
Иногда у вас может возникнуть специфическая потребность в изменении ядерного кода службы HTTP в webman, и для этого можно использовать пользовательские процессы.

Например, создайте файл app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // Переопределить методы из Webman\App здесь
}
```

Добавьте следующую конфигурацию в `config/process.php`

```php
use Workerman\Worker;

return [
    // ... Здесь опущены другие настройки...

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
            'app_path' => app_path(), // Местоположение папки приложения
            'public_path' => public_path() // Местоположение публичной папки
        ]
    ]
];
```

> **Совет**
> Если вы хотите отключить встроенный HTTP-процесс webman, просто укажите `listen=>''` в `config/server.php`

## Пример пользовательского прослушивающего вебсокета

Создайте `app/Pusher.php`
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
> Примечание: все свойства onXXX должны быть public

Добавьте следующую конфигурацию в `config/process.php`
```php
return [
    // ... Остальные настройки для процессов опущены...

    // websocket_test - название процесса
    'websocket_test' => [
        // Указание класса процесса
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ]
];
```

## Пример пользовательского непрослушивающего процесса
Создайте `app/TaskTest.php`
```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // Каждые 10 секунд проверять, есть ли новые зарегистрированные пользователи в базе данных
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```

Добавьте следующую конфигурацию в `config/process.php`
```php
return [
    // ... Остальные настройки для процессов опущены...

    'task' => [
        'handler'  => app\TaskTest::class
    ]
];
```

> Примечание: если listen опущен, то не будет происходить прослушивание никакого порта, если count опущен, то количество процессов по умолчанию будет равно 1.

## Объяснение конфигурационных файлов

Полная конфигурация для процесса выглядит следующим образом:
```php
return [
    // ... 

    // websocket_test - название процесса
    'websocket_test' => [
        // Указание класса процесса
        'handler' => app\Pusher::class,
        // Протокол, IP и порт для прослушивания (опционально)
        'listen'  => 'websocket://0.0.0.0:8888',
        // Количество процессов (опционально, по умолчанию 1)
        'count'   => 2,
        // Пользователь для выполнения процесса (опционально, по умолчанию текущий пользователь)
        'user'    => '',
        // Группа пользователей для выполнения процесса (опционально, по умолчанию текущая группа)
        'group'   => '',
        // Возможность перезагрузки процесса (опционально, по умолчанию true)
        'reloadable' => true,
        // Включение использования reusePort (опционально, требуется php>=7.0 по умолчанию true)
        'reusePort'  => true,
        // Транспорт (опционально, установить значение ssl, если необходимо включить SSL, по умолчанию tcp)
        'transport'  => 'tcp',
        // Контекст (опционально, необходимо передать путь к сертификату, если transport установлен как ssl)
        'context'    => [], 
        // Параметры конструктора класса процесса, в данном случае для класса process\Pusher::class (опционально)
        'constructor' => [],
    ],
];
```

## Вывод
Пользовательские процессы в webman, по сути, являются простой оболочкой для workerman. Они отделяют конфигурацию от бизнес-логики и реализуют методы обратного вызова `onXXX` workerman через методы класса, сохраняя полную совместимость с workerman для других целей.
