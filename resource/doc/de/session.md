# Session-Verwaltung

## Beispiel
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('Hallo ' . $session->get('name'));
    }
}
```

Mit `$request->session();` erhalten Sie eine Instanz von `Workerman\Protocols\Http\Session` und können über die Methoden der Instanz Session-Daten hinzufügen, ändern und löschen.

> Beachten Sie: Beim Zerstören des Session-Objekts werden die Sitzungsdaten automatisch gespeichert. Speichern Sie daher das von `$request->session()` zurückgegebene Objekt nicht in globalen Arrays oder Klassenattributen, da sonst die Sitzung nicht gespeichert wird.

## Abrufen aller Session-Daten
```php
$session = $request->session();
$all = $session->all();
```
Es wird ein Array zurückgegeben. Wenn keine Sitzungsdaten vorhanden sind, wird ein leeres Array zurückgegeben.

## Abrufen eines Werts aus der Session
```php
$session = $request->session();
$name = $session->get('name');
```
Wenn die Daten nicht vorhanden sind, wird null zurückgegeben.

Sie können der `get`-Methode auch einen Standardwert als zweiten Parameter übergeben, der zurückgegeben wird, wenn der Wert in der Sitzungsgruppe nicht gefunden wird. Beispiel:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Speichern von Session-Daten
Verwenden Sie die `set`-Methode, um einen bestimmten Datensatz zu speichern.
```php
$session = $request->session();
$session->set('name', 'tom');
```
Die `set`-Methode gibt keinen Wert zurück. Die Sitzung wird automatisch gespeichert, wenn das Sitzungsobjekt zerstört wird.

Bei der Speicherung mehrerer Werte verwenden Sie die `put`-Methode.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Auch die `put`-Methode gibt keinen Wert zurück.

## Löschen von Session-Daten
Verwenden Sie die `forget`-Methode, um bestimmte oder mehrere Session-Daten zu löschen.
```php
$session = $request->session();
// Löschen eines Eintrags
$session->forget('name');
// Löschen mehrerer Einträge
$session->forget(['name', 'age']);
```

Außerdem gibt es die Methode `delete`, die im Gegensatz zur `forget`-Methode nur einen Eintrag löschen kann.
```php
$session = $request->session();
// Äquivalent zu $session->forget('name');
$session->delete('name');
```

## Abrufen und Löschen eines Werts aus der Session
```php
$session = $request->session();
$name = $session->pull('name');
```
Dies hat den gleichen Effekt wie der folgende Code:
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
Wenn die entsprechende Sitzung nicht existiert, wird null zurückgegeben.

## Alle Session-Daten löschen
```php
$request->session()->flush();
```
Auch hier wird kein Wert zurückgegeben, und die Sitzung wird automatisch aus dem Speicher gelöscht, wenn das Sitzungsobjekt zerstört wird.

## Überprüfen, ob bestimmte Session-Daten vorhanden sind
```php
$session = $request->session();
$has = $session->has('name');
```
Falls die entsprechende Sitzung nicht vorhanden ist oder ihr Wert null ist, wird false zurückgegeben, ansonsten true.

```php
$session = $request->session();
$has = $session->exists('name');
```
Diese Codezeile dient auch dazu, zu überprüfen, ob Session-Daten vorhanden sind. Der Unterschied besteht darin, dass true zurückgegeben wird, wenn der Wert der betreffenden Sitzung null ist.

## Hilfsfunktion session()
> Neu ab 9. Dezember 2020

Webman bietet die Hilfsfunktion `session()` an, um die gleichen Funktionen auszuführen.
```php
// Erhalten Sie eine Session-Instanz
$session = session();
// Äquivalent zu
$session = $request->session();

// Holen eines Werts
$value = session('key', 'default');
// Äquivalent zu
$value = session()->get('key', 'default');
// Äquivalent zu
$value = $request->session()->get('key', 'default');

// Setzen von Werten in der Session
session(['key1'=>'value1', 'key2' => 'value2']);
// Entspricht
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Entspricht
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## Konfigurationsdatei
Die Session-Konfigurationsdatei befindet sich unter `config/session.php` und sieht ähnlich aus wie folgt:

```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class oder RedisSessionHandler::class oder RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // Wenn der Benutzer FileSessionHandler::class verwendet, ist der Wert 'file',
    // Wenn der Benutzer RedisSessionHandler::class verwendet, ist der Wert 'redis',
    // Wenn der Benutzer RedisClusterSessionHandler::class verwendet, ist der Wert 'redis_cluster', was auf ein Redis-Cluster hinweist
    'type'    => 'file',

    // Unterschiedliche Handler verwenden unterschiedliche Konfigurationen
    'config' => [
        // Konfiguration des Dateityps
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Konfiguration des Redis-Typs
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // Name des Cookies, um die session_id zu speichern
    
    // === Die folgenden Konfigurationen sind für webman-framework>=1.3.14 workerman>=4.0.37 erforderlich ===
    'auto_update_timestamp' => false,  // Automatisches Aktualisieren der Sitzung, standardmäßig deaktiviert
    'lifetime' => 7*24*60*60,          // Ablaufzeit der Sitzung
    'cookie_lifetime' => 365*24*60*60, // Gültigkeitsdauer des Cookies, um die session_id zu speichern
    'cookie_path' => '/',              // Pfad des Cookies, um die session_id zu speichern
    'domain' => '',                    // Domäne des Cookies, um die session_id zu speichern
    'http_only' => true,               // Aktivierung von httpOnly, standardmäßig aktiviert
    'secure' => false,                 // Aktivierung der Sitzung nur in HTTPS, standardmäßig deaktiviert
    'same_site' => '',                 // Verhindert CSRF-Angriffe und Benutzer-Tracking, mögliche Werte: strict/lax/none
    'gc_probability' => [1, 1000],     // Wahrscheinlichkeit der Sitzungsrückgewinnung
];
```

> **Anmerkung** 
> Ab webman 1.4.0 wurde der Namespace für SessionHandler von  
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> in  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  
> geändert.


## Ablaufkonfiguration
Wenn webman-framework < 1.3.14 verwendet wird, muss die Ablaufzeit der Sitzung in der `php.ini` konfiguriert werden.

```ini
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Angenommen, die Gültigkeitsdauer beträgt 1440 Sekunden, dann lautet die Konfiguration wie folgt:
```ini
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Hinweis**
> Verwenden Sie den Befehl `php --ini`, um den Speicherort der `php.ini`-Datei zu finden.
