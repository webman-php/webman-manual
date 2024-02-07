# Gestione degli eventi
`webman/event` fornisce un meccanismo per gestire gli eventi in modo agile, consentendo di eseguire la logica di business senza modificare il codice sorgente e ottenendo il disaccoppiamento dei moduli di business. Uno scenario tipico è quando si registra un nuovo utente con successo, è sufficiente pubblicare un evento personalizzato come `user.register`, affinché tutti i moduli possano ricevere tale evento ed eseguire la relativa logica di business.

## Installazione
`composer require webman/event`

## Sottoscrizione degli eventi
La sottoscrizione degli eventi viene configurata in modo centralizzato nel file `config/event.php` come mostrato di seguito:
```php
<?php
return [
    'user.register' => [
        [app\event\User::class, 'register'],
        // ...altre funzioni di gestione degli eventi...
    ],
    'user.logout' => [
        [app\event\User::class, 'logout'],
        // ...altre funzioni di gestione degli eventi...
    ]
];
```
**Spiegazione:**
- `user.register`, `user.logout` sono nomi degli eventi, di tipo stringa, è consigliato usarli in minuscolo e separati da un punto (`.`)
- Un evento può avere più di una funzione di gestione degli eventi, che vengono eseguite nell'ordine specificato nella configurazione

## Funzioni di gestione degli eventi
Le funzioni di gestione degli eventi possono essere qualsiasi metodo di classe, funzione o chiusura.
Ad esempio, creare una classe di gestione degli eventi in `app/event/User.php` (creare la cartella se non esiste):
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

## Pubblicazione degli eventi
Utilizzare `Event::emit($event_name, $data);` per pubblicare un evento, ad esempio:
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
> **Nota**
> Il parametro `$data` di `Event::emit($event_name, $data);` può essere qualsiasi dato, ad esempio un array, un'istanza di classe, una stringa, ecc.

## Ascolto degli eventi tramite wildcard
La registrazione tramite wildcard consente di gestire più eventi con lo stesso gestore, ad esempio la configurazione nel file `config/event.php` è la seguente:
```php
<?php
return [
    'user.*' => [
        [app\event\User::class, 'deal']
    ],
];
```
È possibile ottenere il nome specifico dell'evento passando il secondo parametro `$event_data` alla funzione di gestione degli eventi, come mostrato di seguito:
```php
<?php
namespace app\event;
class User
{
    function deal($user, $event_name)
    {
        echo $event_name; // nome specifico dell'evento, ad esempio user.register, user.logout, ecc.
        var_export($user);
    }
}
```

## Arresto della diffusione degli eventi
Quando una funzione di gestione degli eventi restituisce `false`, l'evento viene interrotto.

## Funzioni di gestione degli eventi come chiusura
Le funzioni di gestione degli eventi possono essere sia metodi di classe che chiusure, ad esempio:
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

## Visualizzazione degli eventi e degli ascoltatori
Utilizzare il comando `php webman event:list` per visualizzare tutti gli eventi e gli ascoltatori configurati nel progetto.

## Note
La gestione degli eventi non è asincrona e non è adatta per gestire operazioni lente. Le operazioni lente dovrebbero essere gestite tramite code di messaggistica, ad esempio con [webman/redis-queue](https://www.workerman.net/plugin/12).
