# Event Eventverarbeitung
`webman/event` bietet einen raffinierten Ereignismechanismus, der es ermöglicht, einige Geschäftslogiken auszuführen, ohne den Code zu ändern, um eine Entkopplung zwischen Geschäftsmodulen zu erreichen. Ein typisches Szenario ist beispielsweise, dass bei erfolgreicher Registrierung eines neuen Benutzers einfach ein benutzerdefiniertes Ereignis wie `user.register` veröffentlicht wird, und die verschiedenen Module das Ereignis erhalten und die entsprechenden Geschäftslogiken ausführen.

## Installation
`composer require webman/event`

## Ereignis abonnieren
Das Abonnieren von Ereignissen wird einheitlich in der Datei `config/event.php` konfiguriert
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...andere Ereignisfunktionen...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...andere Ereignisfunktionen...
    ]
];
```
**Erklärung:**
- `user.register`, `user.logout` usw. sind Ereignisnamen, Strings, die aus kleinen Wörtern bestehen und durch Punkte (`.`) getrennt sind.
- Ein Ereignis kann mehreren Ereignisfunktionen entsprechen, die in der Reihenfolge der Konfiguration aufgerufen werden.

## Ereignisverarbeitungsfunktion
Ereignisverarbeitungsfunktionen können beliebige Klassenmethoden, Funktionen, Closure-Funktionen usw. sein.
Zum Beispiel, erstellen Sie eine Ereignisverarbeitungsklasse `app/event/User.php` (Erstellen Sie das Verzeichnis, falls es nicht existiert)
```php
<?php
namespace app\event;
class User
{
    function register($user)
    {
        var_export($user);
    }
 
    function logout($user)
    {
        var_export($user);
    }
}
```

## Ereignis veröffentlichen
Verwenden Sie `Event::emit($event_name, $data);` zur Veröffentlichung von Ereignissen, beispielsweise
```php
<?php
namespace app\controller;
use support\Request;
use Webman\Event\Event;
class User
{
    public function register(Request $request)
    {
        $user = [
            'name' => 'webman',
            'age' => 2
        ];
        Event::emit('user.register', $user);
    }
}
```

> **Hinweis**
> Der Parameter `$data` von `Event::emit($event_name, $data);` kann beliebige Daten wie Arrays, Klasseninstanzen, Strings usw. sein.

## Wildcard-Ereignisüberwachung
Die Registrierung von Wildcard-Beobachtern ermöglicht es Ihnen, mehrere Ereignisse mit demselben Listener zu verarbeiten, beispielsweise wie in der Konfigurationsdatei `config/event.php`:
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
Durch den zweiten Parameter `$event_data` in der Ereignisverarbeitungsfunktion erhalten wir den spezifischen Ereignisnamen.
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // Spezifischer Ereignisname, z.B. user.register, user.logout, usw.
        var_export($user);
    }
}
```

## Ereignisübertragung stoppen
Wenn wir in der Ereignisverarbeitungsfunktion `false` zurückgeben, wird das Ereignis abgebrochen.

## Closure-Funktion zur Ereignisverarbeitung
Die Ereignisverarbeitungsfunktion kann eine Klassenmethode oder eine Closure-Funktion sein. Zum Beispiel:
```php
<?php
return [
    'user.login' => [
        function($user){
            var_dump($user);
        }
    ]
];
```

## Ereignisse und Zuhörer anzeigen
Verwenden Sie den Befehl `php webman event:list`, um alle im Projekt konfigurierten Ereignisse und Zuhörer anzuzeigen.

## Hinweise
Event-Eventverarbeitung ist nicht asynchron und eignet sich nicht für die Verarbeitung von langsamen Geschäftslogiken, die in Warteschlangen verarbeitet werden sollten, z.B. mit [webman/redis-queue](https://www.workerman.net/plugin/12).
