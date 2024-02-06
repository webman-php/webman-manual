Используйте [redis-queue плагин](https://www.workerman.net/plugin/12) для управления очередью сообщений Redis.

Создайте экземпляр плагина:

```php
use Webman\Redis\RedisQueue;
$queue = new RedisQueue('tcp://127.0.0.1:6379', 'your_queue_name');
```

Помещение элемента в очередь:

```php
$queue->push('your_message');
```

Извлечение элемента из очереди:

```php
$message = $queue->pop();
```

Получение количества элементов в очереди:

```php
$count = $queue->len();
```

Удаление всех элементов из очереди:

```php
$queue->flush();
```
