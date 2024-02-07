## Очередь Stomp

Stomp - это простой текстовый протокол для направленной передачи сообщений, обеспечивающий взаимодействие клиентов Stomp с любым посредником сообщений Stomp (брокером). [workerman/stomp](https://github.com/walkor/stomp) реализует клиент Stomp, который в основном используется для сценариев очередей сообщений, таких как RabbitMQ, Apollo, ActiveMQ и т. д.

## Установка
`composer require webman/stomp`

## Конфигурация
Файл конфигурации находится в `config/plugin/webman/stomp`

## Отправка сообщений
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Очередь
        $queue = 'examples';
        // Данные (при передаче массива необходимо выполнить его собственную сериализацию, например, используя json_encode, serialize и т. д.)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // Отправить сообщение
        Client::send($queue, $data);

        return response('тест очереди Redis');
    }

}
```
> Для обеспечения совместимости с другими проектами компонент Stomp не предоставляет автоматическую сериализацию и десериализацию данных. Если передаются массивы данных, необходимо выполнить их собственную сериализацию, а затем выполнить десериализацию при потреблении.

## Потребление сообщений
Создайте файл `app/queue/stomp/MyMailSend.php` (название класса произвольное, главное, чтобы оно соответствовало правилам psr4).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Название очереди
    public $queue = 'examples';

    // Имя соединения, соответствующее подключению в stomp.php
    public $connection = 'default';

    // При значении "client" необходимо вызвать $ack_resolver->ack(), чтобы сообщить серверу, что сообщение успешно потреблено
    // При значении "auto" вызов $ack_resolver->ack() не требуется
    public $ack = 'auto';

    // Потребление
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Если данные представляют собой массив, необходима собственная десериализация
        var_export(json_decode($data, true)); // вывод ['to' => 'tom@gmail.com', 'content' => 'hello']
        // Подтвердить успешное потребление серверу
        $ack_resolver->ack(); // при ack = auto это вызов можно пропустить
    }
}
```


# Включение протокола stomp в rabbitmq
По умолчанию протокол stomp не включен в rabbitmq и требует выполнения следующей команды для включения
```bash
rabbitmq-plugins enable rabbitmq_stomp
```
После включения порт stomp по умолчанию равен 61613.
