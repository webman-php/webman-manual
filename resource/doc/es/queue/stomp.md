## Cola STOMP

STOMP es un Protocolo Simple de Mensajes basado en texto que proporciona un formato de conexión interoperable que permite a los clientes STOMP interactuar con cualquier intermediario de mensajes (broker) STOMP. [workerman/stomp](https://github.com/walkor/stomp) implementa un cliente STOMP, que se utiliza principalmente en escenarios de cola de mensajes como RabbitMQ, Apollo, ActiveMQ, etc.

## Instalación
```composer require webman/stomp```

## Configuración
El archivo de configuración se encuentra en `config/plugin/webman/stomp`

## Envío de mensajes
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Cola
        $queue = 'ejemplos';
        // Datos (si se está enviando un array, es necesario serializarlo manualmente, por ejemplo usando json_encode, serialize, etc.)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // Realizar el envío
        Client::send($queue, $data);

        return response('Prueba de cola de redis');
    }

}
```
> Para ser compatible con otros proyectos, el componente STOMP no proporciona funciones de serialización y deserialización automáticas. Si se envían datos en forma de array, es necesario serializarlos manualmente y deserializarlos al consumirlos.

## Consumo de mensajes
Crear el archivo `app/queue/stomp/MyMailSend.php` (el nombre de la clase puede ser cualquiera, siempre que cumpla con el estándar PSR-4).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Nombre de la cola
    public $queue = 'ejemplos';

    // Nombre de la conexión, en correspondencia con la conexión en stomp.php
    public $connection = 'default';

    // Si el valor es 'client', se debe llamar a $ack_resolver->ack() para notificar al servidor que se ha consumido exitosamente
    // Si el valor es 'auto', no es necesario llamar a $ack_resolver->ack()
    public $ack = 'auto';

    // Consumir
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Si los datos son un array, es necesario deserializarlos manualmente
        var_export(json_decode($data, true)); // imprime ['to' => 'tom@gmail.com', 'content' => 'hello']
        // Notificar al servidor que se ha consumido exitosamente
        $ack_resolver->ack(); // cuando ack es 'auto', este paso puede omitirse
    }
}
```

# Habilitar el protocolo STOMP en rabbitmq
Por defecto, rabbitmq no tiene habilitado el protocolo STOMP. Es necesario ejecutar el siguiente comando para habilitarlo.
```rabbitmq-plugins enable rabbitmq_stomp```
Una vez habilitado, el puerto por defecto para STOMP es el 61613.
