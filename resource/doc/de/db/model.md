# Schnellstart

Das webman-Modell basiert auf [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Jede Datenbanktabelle hat ein entsprechendes "Modell", um mit dieser Tabelle zu interagieren. Sie können durch das Modell Daten aus der Datenbanktabelle abfragen und neue Datensätze in die Tabelle einfügen.

Stellen Sie vor dem Start sicher, dass die Datenbankverbindung in `config/database.php` konfiguriert ist.

> Hinweis: Um das Modell Observer von Eloquent ORM zu unterstützen, muss zusätzlich `composer require "illuminate/events"` importiert werden. [Beispiel](#Modellbeobachter)

## Beispiel
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Der Name der verknüpften Tabelle des Modells
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Neudefinition des Primärschlüssels, standardmäßig ist es id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Gibt an, ob die Zeitstempel automatisch gewartet werden
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Tabellenname
Sie können einen benutzerdefinierten Tabellennamen angeben, indem Sie das Attribut `table` im Modell definieren:
```php
class User extends Model
{
    /**
     * Der Name der verknüpften Tabelle des Modells
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Primärschlüssel
Eloquent geht auch davon aus, dass jede Datenbanktabelle eine Primärschlüsselspalte mit dem Namen `id` hat. Sie können ein geschütztes `$primaryKey`-Attribut definieren, um diese Konvention zu überschreiben.
```php
class User extends Model
{
    /**
     * Neudefinition des Primärschlüssels, standardmäßig ist es id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent geht davon aus, dass der Primärschlüssel ein automatisch inkrementierender Integer-Wert ist, was bedeutet, dass der Primärschlüssel standardmäßig automatisch in den int-Typ konvertiert wird. Wenn Sie einen nicht-inkrementierenden oder nicht-numerischen Primärschlüssel verwenden möchten, müssen Sie das öffentliche Attribut `$incrementing` auf false setzen.
```php
class User extends Model
{
    /**
     * Gibt an, ob der Primärschlüssel des Modells inkrementiert
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Wenn Ihr Primärschlüssel kein Integer ist, müssen Sie das geschützte `$keyType`-Attribut des Modells auf string setzen:
```php
class User extends Model
{
    /**
     * "Typ" der automatisch inkrementierenden ID.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Zeitstempel
Standardmäßig erwartet Eloquent, dass Ihre Datenbanktabelle `created_at` und `updated_at` enthält. Wenn Sie nicht möchten, dass Eloquent diese beiden Spalten automatisch verwaltet, setzen Sie das Attribut `$timestamps` des Modells auf false:
```php
class User extends Model
{
    /**
     * Gibt an, ob die Zeitstempel automatisch gewartet werden
     *
     * @var bool
     */
    public $timestamps = false;
}
```
Wenn Sie das Format des Zeitstempels anpassen müssen, setzen Sie das Attribut `$dateFormat` in Ihrem Modell. Dieses Attribut bestimmt, wie Datumsattribute in der Datenbank gespeichert werden und wie das Modell in ein Array oder JSON serialisiert wird:
```php
class User extends Model
{
    /**
     * Speicherformat des Zeitstempels
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```
Wenn Sie die Feldnamen für die Speicherung der Zeitstempel anpassen möchten, können Sie in Ihrem Modell die Werte der Konstanten `CREATED_AT` und `UPDATED_AT` festlegen:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Datenbankverbindung
Standardmäßig verwendet das Eloquent-Modell die in Ihrer Anwendungs konfigurierte Standard-Datenbankverbindung. Wenn Sie dem Modell eine andere Verbindung zuweisen möchten, setzen Sie das Attribut `$connection`:
```php
class User extends Model
{
    /**
     * Name der Modellverbindung
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Standardattributwerte
Wenn Sie Standardwerte für bestimmte Attribute des Modells definieren möchten, können Sie das Attribut `$attributes` im Modell festlegen:
```php
class User extends Model
{
    /**
     * Standardattributwerte des Modells.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Modellabfrage
Nachdem Sie das Modell und die damit verbundene Datenbanktabelle erstellt haben, können Sie Daten aus der Datenbank abfragen. Stellen Sie sich jedes Eloquent-Modell als leistungsstarken Query Builder vor, mit dem Sie schnell die mit der Tabelle verknüpften Daten abfragen können. Zum Beispiel:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Hinweis: Da Eloquent-Modelle auch Query-Builder sind, sollten Sie alle verfügbaren Methoden im [Query-Builder](queries.md) durchlesen. Sie können diese Methoden auch in Eloquent-Abfragen verwenden.

## Zusätzliche Einschränkungen
Die `all`-Methode von Eloquent gibt alle Ergebnisse des Modells zurück. Da jedes Eloquent-Modell als Query Builder fungiert, können Sie auch Abfragebedingungen hinzufügen und dann die `get`-Methode zum Abrufen der Abfrageergebnisse verwenden:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Modell neu laden
Sie können die Methoden `fresh` und `refresh` verwenden, um das Modell neu zu laden. Die Methode `fresh` ruft das Modell erneut aus der Datenbank ab. Die vorhandene Modellinstanz bleibt unberührt:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

Die `refresh`-Methode ersetzt die vorhandene Modellinstanz durch neue Daten aus der Datenbank. Darüber hinaus werden bereits geladene Beziehungen erneut geladen:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Sammlung
Die Methoden `all` und `get` von Eloquent können mehrere Ergebnisse abfragen und ein `Illuminate\Database\Eloquent\Collection`-Objekt zurückgeben. Die `Collection`-Klasse bietet viele Hilfsfunktionen zur Verarbeitung von Eloquent-Ergebnissen:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Verwendung von Cursors
Die `cursor`-Methode ermöglicht es Ihnen, einen Cursor zur Durchquerung der Datenbank zu verwenden. Sie führt die Abfrage nur einmal aus. Bei der Verarbeitung großer Datenmengen kann die `cursor`-Methode den Speicherbedarf erheblich reduzieren:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

`cursor` gibt eine Instanz von `Illuminate\Support\LazyCollection` zurück. [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) erlauben die Verwendung der meisten Methoden von Laravel Collection und laden jeweils nur ein einzelnes Modell in den Speicher:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Unterabfragen
Eloquent unterstützt fortschrittliche Unterabfragen, mit denen Sie Informationen aus einer zugehörigen Tabelle in einer einzigen Abfrage extrahieren können. Zum Beispiel, wenn wir eine Tabelle destinations und eine Tabelle flights haben, die an bestimmte Ziele fliegt. Die flights-Tabelle enthält ein Arrival_at-Feld, das angibt, wann der Flug am Ziel ankommt.

Mit den select- und addSelect-Methoden, die durch die Unt 
## Einzelnes Modell / Kollektion abrufen
Neben dem Abrufen aller Datensätze aus einer bestimmten Tabelle können Sie die Methoden `find`, `first` oder `firstWhere` verwenden, um ein einzelnes Modell abzurufen. Diese Methoden geben eine einzelne Modellinstanz zurück anstelle einer Kollektion von Modellen:
```php
// Ein Modell anhand des Primärschlüssels suchen...
$flight = app\model\Flight::find(1);

// Das erste Modell finden, das der Abfrage entspricht...
$flight = app\model\Flight::where('active', 1)->first();

// Schnelle Implementierung des ersten Modells, das der Abfrage entspricht...
$flight = app\model\Flight::firstWhere('active', 1);
```

Sie können auch ein Array von Primärschlüsseln als Parameter für die Methode `find` verwenden, um übereinstimmende Datensätze zu erhalten:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Manchmal möchten Sie möglicherweise zusätzliche Aktionen ausführen, wenn ein erster Eintrag nicht gefunden wird. Die `firstOr`-Methode gibt das erste Ergebnis zurück, wenn es gefunden wird, andernfalls führt sie die angegebene Rückruffunktion aus. Der Rückgabewert der Rückruffunktion wird als Rückgabewert der `firstOr`-Methode verwendet:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
Die `firstOr`-Methode akzeptiert auch ein Array von Spalten, nach denen gesucht werden soll:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "Nicht gefunden" Ausnahme
Manchmal möchten Sie eine Ausnahme auslösen, wenn kein Modell gefunden wird. Dies ist besonders nützlich in Controllern und Routen. Die Methoden `findOrFail` und `firstOrFail` holen das erste Ergebnis der Abfrage und werfen eine Illuminate\Database\Eloquent\ModelNotFoundException-Ausnahme, wenn keine Übereinstimmung gefunden wird:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## Kollektionen abrufen
Sie können auch die Methoden count, sum und max des Query-Generators und andere Sammlungsfunktionen verwenden, um Kollektionen zu bearbeiten. Diese Methoden geben nur geeignete skalare Werte zurück, anstelle einer Modelinstanz:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Einfügen
Um einen Datensatz in die Datenbank einzufügen, erstellen Sie zuerst eine neue Modelinstanz, setzen Sie die Attribute der Instanz und rufen Sie dann die save-Methode auf:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Fügt einen neuen Datensatz in die Benutzertabelle ein
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Überprüfen der Anfrage

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

Die Zeitstempel created_at und updated_at werden automatisch festgelegt (wenn die $timestamps-Eigenschaft des Modells true ist) und müssen nicht manuell zugewiesen werden.

## Aktualisieren
Die save-Methode kann auch zum Aktualisieren eines bereits vorhandenen Modells in der Datenbank verwendet werden. Um ein Modell zu aktualisieren, müssen Sie es zuerst abrufen, die zu aktualisierenden Attribute festlegen und dann die save-Methode aufrufen. Ebenso wird der updated_at-Zeitstempel automatisch aktualisiert und muss daher nicht manuell zugewiesen werden:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Batch-Updates
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Überprüfen von Attributänderungen
Eloquent bietet die Methoden isDirty, isClean und wasChanged, um den internen Zustand des Modells zu überprüfen und festzustellen, wie sich seine Attribute seit dem Laden ursprünglich geändert haben.
Die Methode isDirty bestimmt, ob seit dem Laden des Modells irgendwelche Attribute geändert wurden. Sie können einen spezifischen Attributnamen übergeben, um festzustellen, ob ein bestimmtes Attribut schmutzig geworden ist. Die Methode isClean ist das Gegenteil von isDirty und akzeptiert ebenfalls optionale Attributparameter:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
Die Methode wasChanged bestimmt, ob seit der letzten Speicherung des Modells innerhalb des aktuellen Anfragezyklus irgendwelche Attribute geändert wurden. Sie können auch einen Attributnamen übergeben, um zu sehen, ob dieses spezifische Attribut geändert wurde:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```

## Massenzuweisung
Sie können auch die create-Methode verwenden, um neue Modelle zu speichern. Diese Methode gibt eine Modelinstanz zurück. Bevor Sie sie jedoch verwenden, müssen Sie auf dem Modell angeben, welche Attribute massiv zugewiesen werden können, da alle Eloquent-Modelle standardmäßig nicht massiv zugewiesen werden können.

Wenn ein Benutzer unerwartete HTTP-Parameter übermittelt und diese die Felder in der Datenbank ändern, die Sie nicht ändern möchten, kann es zu Massenzuweisungslücken kommen. Zum Beispiel könnte ein bösartiger Benutzer den Parameter is_admin über einen HTTP-Request übermitteln und ihn dann der create-Methode übergeben, um sich selbst zum Administrator zu befördern.

Deshalb sollten Sie vorher definieren, welche Attribute auf dem Modell massiv zugewiesen werden können. Dies können Sie durch die Verwendung der $fillable-Eigenschaft des Modells erreichen. Zum Beispiel können Sie das Attribut name des Flugmodells massiv zuweisen:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Die Attribute, die massiv zugewiesen werden können.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
Sobald die massiv zuweisbaren Attribute festgelegt sind, können Sie die create-Methode verwenden, um neue Daten in die Datenbank einzufügen. Die create-Methode gibt die gespeicherte Modelinstanz zurück:
```php
$flight = app\model\Flight::create(['name' => 'Flight 10']);
```
Wenn Sie bereits eine Modelinstanz haben, können Sie ein Array an die fill-Methode übergeben, um die Zuweisung vorzunehmen:
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable kann als eine Art "Whitelist" für Massenzuweisungen betrachtet werden. Sie können auch die $guarded-Eigenschaft verwenden, um dies zu erreichen. Die $guarded-Eigenschaft enthält ein Array von Attributen, die nicht massiv zugewiesen werden dürfen. Mit anderen Worten, $guarded fungiert funktional eher als eine "Blacklist". Beachten Sie: Sie können nur $fillable oder $guarded verwenden, nicht beide gleichzeitig. In folgendem Beispiel können alle Attribute außer dem price-Attribut massiv zugewiesen werden:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Die Attribute, die nicht massiv zugewiesen werden dürfen.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Wenn Sie möchten, dass alle Attribute massiv zugewiesen werden können, können Sie die $guarded-Eigenschaft als leeres Array definieren:
```php
/**
 * Die Attribute, die nicht massiv zugewiesen werden dürfen.
 *
 * @var array
 */
protected $guarded = [];
```
## Andere Erstellungsmethoden
firstOrCreate / firstOrNew
Hier sind zwei Methoden, die Sie zur Massenzuweisung verwenden können: firstOrCreate und firstOrNew. Die firstOrCreate-Methode passt die in der Datenbank gefundenen Daten an die angegebenen Schlüssel/Wert-Paare an. Wenn in der Datenbank kein Modell gefunden wird, wird ein Datensatz eingefügt, der die Attribute des ersten Parameters und optional des zweiten Parameters enthält.

Die firstOrNew-Methode versucht, wie die firstOrCreate-Methode, durch die angegebenen Attribute ein entsprechendes Modell in der Datenbank zu finden. Wenn die firstOrNew-Methode jedoch kein entsprechendes Modell findet, wird eine neue Modellinstanz zurückgegeben. Beachten Sie, dass die von firstOrNew zurückgegebene Modellinstanz noch nicht in der Datenbank gespeichert ist und Sie die save-Methode manuell aufrufen müssen, um sie zu speichern:
```php
// Suchen Sie nach einem Flug anhand des Namens und erstellen Sie ihn, falls er nicht gefunden wird...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Suchen Sie nach einem Flug anhand des Namens, oder erstellen Sie ihn mit den Attributen 'delayed' und 'arrival_time' zusammen mit dem Namen ...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Suchen Sie nach einem Flug anhand des Namens, oder erstellen Sie eine Instanz...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Suchen Sie nach einem Flug anhand des Namens, oder erstellen Sie eine Modellinstanz mit den Attributen 'delayed' und 'arrival_time' zusammen mit dem Namen ...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

Es kann auch vorkommen, dass Sie ein vorhandenes Modell aktualisieren möchten oder ein neues Modell erstellen, wenn keines vorhanden ist. Dies kann mit der Methode updateOrCreate in einem Schritt erreicht werden. Ähnlich wie die firstOrCreate-Methode persistiert updateOrCreate das Modell und erfordert daher keinen Aufruf von save():
```php
// Wenn ein Flug von Oakland nach San Diego existiert, wird der Preis auf 99 US-Dollar festgelegt.
// Wenn keine übereinstimmende Modelle gefunden werden, wird eines erstellt.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Modell löschen
Sie können die delete-Methode auf einer Modelinstanz aufrufen, um die Instanz zu löschen:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Modell durch Primärschlüssel löschen
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## Modell durch Abfrage löschen
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Modell kopieren
Sie können die replicate-Methode verwenden, um eine neue, noch nicht in die Datenbank gespeicherte Instanz zu kopieren. Diese Methode ist sehr nützlich, wenn Modelle viele gemeinsame Attribute teilen.
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Beispiel Straße',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();
```

## Modellvergleich
Manchmal müssen Sie überprüfen, ob zwei Modelle "identisch" sind. Die is-Methode kann verwendet werden, um schnell zu überprüfen, ob zwei Modelle dieselben Primärschlüssel, Tabellen und Datenbankverbindungen haben:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Modellbeobachter
Weitere Informationen zu Eloquent-Modellereignissen und -Beobachtern finden Sie unter [Laravel 中的模型事件与 Observer](https://learnku.com/articles/6657/model-events-and-observer-in-laravel).

Hinweis: Zur Unterstützung von Modellbeobachtern in Eloquent ORM müssen Sie zusätzlich `composer require "illuminate/events"` importieren.
```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```
