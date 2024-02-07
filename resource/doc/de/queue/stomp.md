## Stomp-Warteschlange

Stomp ist ein einfaches (textbasiertes) Messaging-Protokoll, das ein interoperables Verbindungformat bereitstellt und es STOMP-Clients ermöglicht, mit beliebigen STOMP-Nachrichtenbrokern zu interagieren. [workerman/stomp](https://github.com/walkor/stomp) implementiert einen Stomp-Client, der hauptsächlich für Szenarien mit Nachrichtenwarteschlangen wie RabbitMQ, Apollo, ActiveMQ usw. verwendet wird.

## Installation
`composer require webman/stomp`

## Konfiguration
Die Konfigurationsdatei befindet sich unter `config/plugin/webman/stomp`.

## Nachrichtenlieferung
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Warteschlange
        $queue = 'Beispiele';
        // Daten (beim Übergeben von Arrays muss eine eigene Serialisierung, z. B. mit json_encode, serialize etc., durchgeführt werden)
        $data = json_encode(['an' => 'tom@gmail.com', 'inhalt' => 'hallo']);
        // Lieferung durchführen
        Client::send($queue, $data);

        return response('Redis-Warteschlange Test');
    }
}
```
> Um die Kompatibilität mit anderen Projekten zu gewährleisten, bietet das Stomp-Modul keine automatische Serialisierung und Deserialisierung an. Wenn Array-Daten übermittelt werden, muss dies selbst durchgeführt werden, und beim Verbrauch muss dies manuell rückgängig gemacht werden.

## Nachrichtenverbrauch
Erstellen Sie `app/queue/stomp/MyMailSend.php` (beliebiger Klassenname, der dem PSR-4-Standard entspricht).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Warteschlangenname
    public $queue = 'Beispiele';

    // Verbindungsnamen, die mit der Verbindung in stomp.php übereinstimmen
    public $connection = 'standard';

    // Wenn der Wert "client" ist, muss $ack_resolver->ack() aufgerufen werden, um dem Server mitzuteilen, dass die Verarbeitung erfolgreich war
    // Wenn der Wert "auto" ist, muss $ack_resolver->ack() nicht aufgerufen werden
    public $ack = 'auto';

    // Verbrauch
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Bei Array-Daten muss eine manuelle Deserialisierung erfolgen
        var_export(json_decode($data, true)); // Gibt ['an' => 'tom@gmail.com', 'inhalt' => 'hallo'] aus
        // Dem Server mitteilen, dass die Verarbeitung erfolgreich war
        $ack_resolver->ack(); // Bei ack "auto" kann dieser Aufruf ausgelassen werden
    }
}
```

# Aktivierung des Stomp-Protokolls für RabbitMQ
Standardmäßig ist das Stomp-Protokoll in RabbitMQ nicht aktiviert. Die folgenden Befehle müssen ausgeführt werden, um es zu aktivieren.
```bash
rabbitmq-plugins enable rabbitmq_stomp
```
Nach der Aktivierung läuft Stomp standardmäßig auf Port 61613.
