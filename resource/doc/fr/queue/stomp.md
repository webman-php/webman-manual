## File d'attente Stomp

Stomp est un protocole simple de messagerie orienté texte qui fournit un format de connexion interopérable permettant aux clients STOMP d'interagir avec n'importe quel courtier de messages STOMP. [workerman/stomp](https://github.com/walkor/stomp) implémente un client STOMP principalement utilisé dans des scénarios de file d'attente de messages tels que RabbitMQ, Apollo, ActiveMQ, etc.

## Installation
Exécutez `composer require webman/stomp` pour installer.

## Configuration
Le fichier de configuration se trouve dans `config/plugin/webman/stomp`.

## Envoi de messages
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // File d'attente
        $queue = 'examples';
        // Données (si vous transmettez un tableau, vous devez le sérialiser vous-même, par exemple en utilisant json_encode, serialize, etc.)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // Envoi du message
        Client::send($queue, $data);

        return response('test de file d'attente Redis');
    }

}
```
> Pour des raisons de compatibilité avec d'autres projets, le composant Stomp ne fournit pas de fonctionnalité de sérialisation et désérialisation automatiques. Si vous envoyez des données sous forme de tableau, assurez-vous de les sérialiser manuellement et de les désérialiser lors de la consommation.

## Consommation de messages
Créez un nouveau fichier `app/queue/stomp/MyMailSend.php` (le nom de la classe est arbitraire, tant qu'il est conforme à la norme PSR-4).
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // Nom de la file d'attente
    public $queue = 'examples';

    // Nom de la connexion correspondant à la connexion dans stomp.php
    public $connection = 'default';

    // Lorsque la valeur est 'client', vous devez appeler $ack_resolver->ack() pour indiquer au serveur que la consommation a réussi.
    // Lorsque la valeur est 'auto', vous n'avez pas besoin d'appeler $ack_resolver->ack().
    public $ack = 'auto';

    // Consommer
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // Si les données sont sous forme de tableau, vous devez les désérialiser manuellement
        var_export(json_decode($data, true)); // renvoie ['to' => 'tom@gmail.com', 'content' => 'hello']
        // Indiquer au serveur que la consommation a réussi
        $ack_resolver->ack(); // L'appel à ack peut être omis lorsque ack vaut 'auto'
    }
}
```


# Activation du protocole Stomp pour RabbitMQ
Par défaut, RabbitMQ n'active pas le protocole Stomp. Vous devez exécuter la commande suivante pour l'activer.
```
rabbitmq-plugins enable rabbitmq_stomp
```
Après l'activation, le port par défaut pour Stomp est 61613.
