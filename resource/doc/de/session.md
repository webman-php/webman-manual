# Sitzungsverwaltung

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
        return response('hallo ' . $session->get('name'));
    }
}
```

Mit `$request->session();` erhalten Sie eine Instanz von `Workerman\Protocols\Http\Session`, um Sitzungsdaten hinzuzufügen, zu ändern und zu löschen.

> Hinweis: Wenn das Sitzungsobjekt zerstört wird, werden die Sitzungsdaten automatisch gespeichert. Daher sollten Sie das von `$request->session()` zurückgegebene Objekt nicht in globalen Arrays oder Klassenattributen speichern, da dies dazu führen kann, dass die Sitzung nicht gespeichert wird.

## Abrufen aller Sitzungsdaten
```php
$session = $request->session();
$all = $session->all();
```
Es wird ein Array zurückgegeben. Wenn keine Sitzungsdaten vorhanden sind, wird ein leeres Array zurückgegeben.

## Abrufen eines Werts aus der Sitzung
```php
$session = $request->session();
$name = $session->get('name');
```
Wenn die Daten nicht vorhanden sind, wird null zurückgegeben.

Sie können dem `get`-Methode auch einen Standardwert als zweiten Parameter übergeben. Wenn der Wert in der Sitzung nicht gefunden wird, wird der Standardwert zurückgegeben. Zum Beispiel:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```

## Speichern der Sitzung
Verwenden Sie die `set`-Methode, um einen bestimmten Datenwert zu speichern.
```php
$session = $request->session();
$session->set('name', 'tom');
```
`set` gibt keinen Wert zurück, da die Sitzung automatisch gespeichert wird, wenn das Sitzungsobjekt zerstört wird.

Bei der Speicherung mehrerer Werte verwenden Sie die `put`-Methode.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
Auch hier gibt `put` keinen Wert zurück.

## Löschen von Sitzungsdaten
Um bestimmte oder mehrere Sitzungsdaten zu löschen, verwenden Sie die `forget`-Methode.
```php
$session = $request->session();
// Löschen eines Elements
$session->forget('name');
// Löschen von mehreren Elementen
$session->forget(['name', 'age']);
```

Darüber hinaus gibt es auch die `delete`-Methode, die sich von der `forget`-Methode unterscheidet, da sie nur ein Element löschen kann.
```php
$session = $request->session();
// Äquivalent zu $session->forget('name');
$session->delete('name');
```

## Abrufen und Löschen eines Werts aus der Sitzung
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
Wenn die entsprechende Sitzung nicht vorhanden ist, wird null zurückgegeben.

## Löschen aller Sitzungsdaten
```php
$request->session()->flush();
```
Es wird kein Wert zurückgegeben, da die Sitzung automatisch aus dem Speicher gelöscht wird, wenn das Sitzungsobjekt zerstört wird.

## Überprüfen, ob entsprechende Sitzungsdaten vorhanden sind
```php
$session = $request->session();
$has = $session->has('name');
```
Für den obigen Fall, wenn die entsprechende Sitzung nicht vorhanden ist oder der Wert der Sitzung null ist, wird false zurückgegeben, ansonsten true.

```php
$session = $request->session();
$has = $session->exists('name');
```
Der obige Code dient ebenfalls zur Überprüfung, ob die Sitzungsdaten vorhanden sind. Der Unterschied besteht darin, dass true zurückgegeben wird, wenn der Wert des entsprechenden Sitzungselements null ist.

## Hilfsfunktion `session()`
> 2020-12-09 hinzugefügt

Webman bietet die Hilfsfunktion `session()`, um die gleiche Funktionalität zu erreichen.
```php
// Sitzungsinstantz erhalten
$session = session();
// Äquivalent zu
$session = $request->session();

// Wert abrufen
$value = session('key', 'default');
// Äquivalent zu
$value = session()->get('key', 'default');
// Äquivalent zu
$value = $request->session()->get('key', 'default');

// Sitzungswerte zuweisen
session(['key1'=>'value1', 'key2' => 'value2']);
// Äquivalent zu
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// Äquivalent zu
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## Konfigurationsdatei
Die Sitzungskonfigurationsdatei befindet sich in `config/session.php` und sieht wie folgt aus:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class oder RedisSessionHandler::class oder RedisClusterSessionHandler::class  
    'handler' => FileSessionHandler::class,
    
    // Wenn der Handler FileSessionHandler::class ist, ist der Wert 'file',
    // wenn der Handler RedisSessionHandler::class ist, ist der Wert 'redis'
    // wenn der Handler RedisClusterSessionHandler::class ist, ist der Wert 'redis_cluster' für Redis-Cluster
    'type'    => 'file',

    // Verschiedene Handler verwenden unterschiedliche Konfigurationen
    'config' => [
        // Konfigurationstyp für 'file'
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // Konfigurationstyp für 'redis'
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

    'session_name' => 'PHPSID', // Name des Cookies zur Speicherung der Sitzungs-ID
    
    // === Die folgende Konfiguration erfordert webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // Automatisches Aktualisieren der Sitzung aktivieren, standardmäßig deaktiviert
    'lifetime' => 7*24*60*60,          // Lebensdauer der Sitzung
    'cookie_lifetime' => 365*24*60*60, // Lebensdauer des Cookies zur Speicherung der Sitzungs-ID
    'cookie_path' => '/',              // Pfad des Cookies zur Speicherung der Sitzungs-ID
    'domain' => '',                    // Domain des Cookies zur Speicherung der Sitzungs-ID
    'http_only' => true,               // HttpOnly aktivieren, standardmäßig aktiviert
    'secure' => false,                 // Sitzung nur bei https aktivieren, standardmäßig deaktiviert
    'same_site' => '',                 // Verhindert CSRF-Angriffe und Benutzertracking, gültige Werte: strict/lax/none
    'gc_probability' => [1, 1000],     // Wahrscheinlichkeit der Sitzungsbereinigung
];
```

> **Hinweis** 
> Ab webman 1.4.0 hat sich der Namensraum des SessionHandlers von 
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> zu  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  
> geändert.

## Konfiguration der Gültigkeitsdauer
Wenn webman-framework < 1.3.14 verwendet wird, muss die Gültigkeitsdauer der Sitzung in der Datei `php.ini` konfiguriert werden.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

Angenommen, die Gültigkeitsdauer beträgt 1440 Sekunden, dann wird die Konfiguration wie folgt aussehen
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **Hinweis**
> Sie können den Befehl `php --ini` verwenden, um den Speicherort der `php.ini`-Datei zu finden.
