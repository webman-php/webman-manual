## Coda di Stomp

Stomp è un protocollo di messaggistica testuale semplice (Streaming), che fornisce un formato di connessione interoperabile che consente ai client STOMP di interagire con qualsiasi broker di messaggi STOMP. [workerman/stomp](https://github.com/walkor/stomp) implementa un client Stomp, principalmente utilizzato per scenari di code di messaggi come RabbitMQ, Apollo, ActiveMQ e altri.

## Installazione
Eseguire il comando `composer require webman/stomp` per installare il pacchetto.

## Configurazione
Il file di configurazione si trova in `config/plugin/webman/stomp`.

## Invio di un messaggio
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // Coda
        $queue = 'esempi';
        // Dati (quando si passano degli array è necessario serializzarli manualmente, ad esempio usando json_encode, serialize, ecc.)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'ciao']);
        // Esecuzione dell'invio
        Client::send($queue, $data);

        return response('test della coda di Redis');
    }

}
```
> Per garantire la compatibilità con altri progetti, il componente Stomp non offre funzionalità di serializzazione e deserializzazione automatica. Se si inviano dati sotto forma di array, è necessario serializzarli manualmente e deserializzarli durante la lettura.

## Consumo di un messaggio
Creare il file `app/queue/stomp/MyMailSend.php` (il nome della classe è arbitrario, purché rispetti le specifiche Psr4).

```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Nome della coda
    public $queue = 'esempi';

    // Nome della connessione, corrispondente alla connessione definita in stomp.php
    public $connection = 'predefinito';

    // Se il valore è "client", è necessario chiamare $ack_resolver->ack() per confermare al server il corretto consumo del messaggio.
    // Se il valore è "auto", non è necessario chiamare $ack_resolver->ack().
    public $ack = 'auto';

    // Consumo del messaggio
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Se i dati sono un array, è necessario deserializzarli manualmente
        var_export(json_decode($data, true)); // output ['to' => 'tom@gmail.com', 'content' => 'ciao']
        // Comunicazione al server del corretto consumo del messaggio
        $ack_resolver->ack(); // la chiamata a ack può essere omessa quando ack è impostato su auto
    }
}
```

# Abilitare il protocollo Stomp in rabbitmq
Di default, rabbitmq non abilita il protocollo Stomp, è necessario eseguire il seguente comando per abilitarlo:
```shell
rabbitmq-plugins enable rabbitmq_stomp
```
Dopo l'abilitazione, la porta predefinita per Stomp è 61613.
