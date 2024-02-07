## Stomp Queue

Stomp is a Simple (Streaming) Text Orientated Messaging Protocol that provides an interoperable wire format that allows clients to communicate with any STOMP message broker. The [workerman/stomp](https://github.com/walkor/stomp) implements the Stomp client and is mainly used for message queue scenarios such as RabbitMQ, Apollo, ActiveMQ, etc.

## Installation
`composer require webman/stomp`

## Configuration
The configuration file is located under `config/plugin/webman/stomp`

## Sending Messages
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Queue name
        $queue = 'examples';
        // Data (serialization is required when passing an array, for example, using json_encode, serialize, etc.)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // Perform delivery
        Client::send($queue, $data);

        return response('redis queue test');
    }

}
```
> For compatibility with other projects, the Stomp component does not provide automatic serialization and deserialization. If array data is to be sent, serialization is required, and deserialization is required when consuming.

## Consuming Messages
Create a new file `app/queue/stomp/MyMailSend.php` (the class name can be arbitrary as long as it complies with the psr4 standard).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Queue name
    public $queue = 'examples';

    // Connection name, corresponding to the connection in stomp.php
    public $connection = 'default';

    // When the value is 'client', it is necessary to use $ack_resolver->ack() to inform the server that the consumption was successful
    // When the value is 'auto', there is no need to call $ack_resolver->ack()
    public $ack = 'auto';

    // Consumption
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // If the data is an array, deserialization is required
        var_export(json_decode($data, true)); // Outputs ['to' => 'tom@gmail.com', 'content' => 'hello']
        // Inform the server that the consumption was successful
        $ack_resolver->ack(); // This call can be omitted when ack is set to auto
    }
}
```

# Enable STOMP Protocol in RabbitMQ
By default, RabbitMQ does not enable the STOMP protocol and requires the following command to enable it:
```
rabbitmq-plugins enable rabbitmq_stomp
```
Once enabled, the default port for STOMP is 61613.
