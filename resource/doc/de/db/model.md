# Schnellstart

Das Webman-Modell basiert auf [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Jede Datenbanktabelle hat ein entsprechendes "Modell", das mit dieser Tabelle interagiert. Sie können das Modell verwenden, um Daten aus der Tabelle abzufragen und neue Datensätze in die Tabelle einzufügen.

Bevor Sie beginnen, stellen Sie sicher, dass die Datenbankverbindung in `config/database.php` konfiguriert ist.

> Hinweis: Um Modellbeobachter mit Eloquent ORM zu verwenden, müssen Sie zusätzlich `composer require "illuminate/events"` importieren. [Beispiel](#模型观察者)

## Beispiel
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Name der mit dem Modell verknüpften Tabelle
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Neudefinition des Primärschlüssels, standardmäßig ist es "id"
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Gibt an, ob Zeitstempel automatisch gepflegt werden
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Tabellenname
Sie können einen benutzerdefinierten Tabellennamen angeben, indem Sie das `table`-Attribut im Modell definieren:
```php
class User extends Model
{
    /**
     * Name der mit dem Modell verknüpften Tabelle
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Primärschlüssel
Eloquent geht auch davon aus, dass jede Datenbanktabelle eine Spalte mit dem Namen "id" als Primärschlüssel hat. Sie können ein geschütztes Attribut `$primaryKey` definieren, um diese Konvention zu überschreiben.
```php
class User extends Model
{
    /**
     * Neudefinition des Primärschlüssels, standardmäßig ist es "id"
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent nimmt an, dass der Primärschlüssel ein inkrementierter Ganzzahlwert ist, was bedeutet, dass der Primärschlüssel standardmäßig in einen int-Typ umgewandelt wird. Wenn Sie einen nicht-inkrementierenden oder nicht-numerischen Primärschlüssel verwenden möchten, müssen Sie das öffentliche Attribut `$incrementing` auf `false` setzen.
```php
class User extends Model
{
    /**
     * Gibt an, ob der Modell-Primärschlüssel inkrementiert wird
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Wenn Ihr Primärschlüssel kein Integer ist, müssen Sie das geschützte `$keyType`-Attribut im Modell auf "string" setzen:
```php
class User extends Model
{
    /**
     * "Typ" des automatisch inkrementierten IDs
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Zeitstempel
Standardmäßig erwartet Eloquent, dass Ihre Datenbanktabelle `created_at` und `updated_at` enthält. Wenn Sie nicht möchten, dass Eloquent diese beiden Spalten automatisch verwaltet, setzen Sie das `$timestamps` Attribut im Modell auf `false`:
```php
class User extends Model
{
    /**
     * Gibt an, ob Zeitstempel automatisch gepflegt werden
     *
     * @var bool
     */
    public $timestamps = false;
}
```

Wenn Sie das Format des Zeitstempels anpassen möchten, setzen Sie das `$dateFormat` Attribut in Ihrem Modell. Dieses Attribut bestimmt die Speicherweise des Datumsattributs in der Datenbank sowie das Format, in dem das Modell als Array oder JSON serialisiert wird:
```php
class User extends Model
{
    /**
     * Format für die Speicherung des Zeitstempels
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Wenn Sie die Namen der Zeitstempelfelder anpassen möchten, können Sie die Werte der Konstanten `CREATED_AT` und `UPDATED_AT` im Modell setzen:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Datenbankverbindung
Standardmäßig verwendet das Eloquent-Modell die in Ihrer Anwendungsconfiguration festgelegte Standarddatenbankverbindung. Wenn Sie dem Modell eine andere Verbindung zuweisen möchten, setzen Sie das `$connection` Attribut:
```php
class User extends Model
{
    /**
     * Name der Verbindung des Modells
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## Standardattributwerte
Wenn Sie Standardwerte für bestimmte Attribute des Modells definieren möchten, können Sie das `$attributes` Attribut im Modell definieren:
```php
class User extends Model
{
    /**
     * Standardattributwerte des Modells
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Modellabfrage
Nachdem Sie das Modell und die zugehörige Datenbanktabelle erstellt haben, können Sie nun Daten aus der Datenbank abrufen. Stellen Sie sich jedes Eloquent-Modell als leistungsstarken Abfrage-Builder vor, mit dem Sie schnell auf die damit verbundene Datenbanktabelle zugreifen können. Zum Beispiel:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Hinweis: Da Eloquent-Modelle auch Abfrage-Builder sind, sollten Sie alle verfügbaren Methoden der [Abfrage-Builder](queries.md) einsehen. Diese Methoden können in Eloquent-Abfragen verwendet werden.

## Zusätzliche Einschränkungen
Die `all`-Methode von Eloquent gibt alle Ergebnisse des Modells zurück. Da jedes Eloquent-Modell als Abfrage-Builder fungiert, können Sie auch Abfragebedingungen hinzufügen und die `get`-Methode verwenden, um die Abfrageergebnisse abzurufen:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Modell neu laden
Sie können die Methoden `fresh` und `refresh` verwenden, um das Modell neu zu laden. Die `fresh`-Methode ruft das Modell erneut aus der Datenbank ab. Die bestehende Modellinstanz bleibt unberührt:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

Die `refresh`-Methode aktualisiert das vorhandene Modell mit den neuen Daten aus der Datenbank. Darüber hinaus werden bereits geladene Beziehungen ebenfalls aktualisiert:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Sammlung
Die Methoden `all` und `get` von Eloquent können mehrere Ergebnisse abfragen und eine Instanz der Klasse `Illuminate\Database\Eloquent\Collection` zurückgeben. Die Klasse `Collection` bietet viele Hilfsfunktionen zum Verarbeiten von Eloquent-Ergebnissen:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Verwendung von Cursor
Die Methode `cursor` ermöglicht es Ihnen, den Cursor zum Durchlaufen der Datenbank zu verwenden. Es führt nur eine Abfrage durch. Bei der Verarbeitung großer Datenmengen kann die Methode `cursor` den Speicherbedarf erheblich reduzieren:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

`cursor` gibt eine Instanz von `Illuminate\Support\LazyCollection` zurück. [Lazy Collections](https://laravel.com/docs/7.x/collections#lazy-collections) ermöglichen die Verwendung der meisten Methoden von Laravel Collections und laden jedes Mal nur ein einzelnes Modell in den Speicher:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```
## Selects-Subquery
Eloquent bietet fortschrittliche Unterabfrage-Unterstützung, mit der Sie mit einer einzelnen Abfrage Informationen aus verwandten Tabellen abrufen können. Zum Beispiel, nehmen wir an, wir haben eine Tabelle `destinations` und eine Tabelle `flights`, die die Flüge zu den Zielen enthält. Die `flights`-Tabelle enthält ein Feld `arrival_at`, das angibt, wann der Flug am Ziel ankommt.

Mit den Funktionen `select` und `addSelect` von Subqueries können wir mit einer einzigen Abfrage alle Destinationen abfragen und den Namen des letzten Flugs, der an jedem Ziel angekommen ist, abrufen:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Sortieren nach einer Unterabfrage
Außerdem unterstützt die `orderBy`-Funktion des Abfrage-Builders auch Unterabfragen. Mit dieser Funktion können wir alle Ziele basierend auf der Ankunftszeit des letzten Flugs am Ziel sortieren. Auch hier erfolgt nur eine einzelne Abfrage an die Datenbank:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```
## Einzelnes Modell / Sammlung abrufen
Neben dem Abrufen aller Datensätze aus einer bestimmten Datenbanktabelle können Sie die Methoden find, first oder firstWhere verwenden, um einen einzelnen Datensatz abzurufen. Diese Methoden geben eine einzelne Modellinstanz zurück, anstatt eine Modellsammlung zurückzugeben:
```php
// Ein Modell anhand des Primärschlüssels finden...
$flight = app\model\Flight::find(1);

// Das erste Modell abrufen, das der Abfragebedingung entspricht...
$flight = app\model\Flight::where('active', 1)->first();

// Schnelle Implementierung, um das erste Modell abzurufen, das der Abfragebedingung entspricht...
$flight = app\model\Flight::firstWhere('active', 1);
```

Sie können auch die Methode find mit einem Array als Parameter aufrufen, um eine Sammlung von übereinstimmenden Datensätzen zurückzugeben:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

Manchmal möchten Sie zusätzliche Aktionen ausführen, wenn Sie nach dem ersten Ergebnis suchen, das nicht gefunden wird. Die Methode firstOr gibt das erste Ergebnis zurück, wenn eines gefunden wird, andernfalls führt sie den angegebenen Callback aus. Der Rückgabewert des Callbacks wird als Rückgabewert der Methode firstOr verwendet:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
Die firstOr Methode akzeptiert auch ein Feldarray für die Abfrage:
```php
$model = app\model\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "Nicht gefunden" Ausnahme
Manchmal möchten Sie eine Ausnahme auslösen, wenn das Modell nicht gefunden wird. Dies ist besonders nützlich in Controllern und Routen. Die Methoden findOrFail und firstOrFail rufen das erste abgefragte Ergebnis ab. Wenn kein Ergebnis gefunden wird, wird eine Illuminate\Database\Eloquent\ModelNotFoundException-Ausnahme ausgelöst:
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```

## Sammlung abrufen
Sie können auch die count, sum und max Methoden sowie andere Sammlungsfunktionen des Abfrage-Generators verwenden, um mit Sammlungen zu arbeiten. Diese Methoden geben nur den entsprechenden skalaren Wert zurück, anstatt eine Modellinstanz:
```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## Einfügen
Um einen Datensatz in die Datenbank einzufügen, erstellen Sie zunächst eine neue Modellinstanz, setzen Sie die Attribute und rufen Sie dann die save Methode auf:
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
        // Anfrage validieren

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at und updated_at Zeitstempel werden automatisch gesetzt (wenn die $timestamps Eigenschaft im Modell true ist) und müssen nicht manuell zugewiesen werden.

## Aktualisierung
Die save Methode kann auch zum Aktualisieren eines bereits vorhandenen Modells in der Datenbank verwendet werden. Um ein Modell zu aktualisieren, müssen Sie es zuerst abrufen, die zu aktualisierenden Attribute festlegen und dann die save Methode aufrufen. Ebenso wird der updated_at Zeitstempel automatisch aktualisiert, sodass keine manuelle Zuweisung erforderlich ist:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Massenaktualisierung
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Überprüfen von Attributänderungen
Eloquent bietet die isDirty, isClean und wasChanged Methoden, um den internen Zustand des Modells zu überprüfen und festzustellen, wie sich seine Attribute seit dem ursprünglichen Laden geändert haben.
Die isDirty Methode bestimmt, ob seit dem Laden des Modells Änderungen an einem Attribut vorgenommen wurden. Sie können einen spezifischen Attributnamen übergeben, um festzustellen, ob ein bestimmtes Attribut schmutzig ist. Die Methode isClean ist das Gegenteil von isDirty und akzeptiert auch optionale Attributparameter:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Entwickler',
]);

$user->title = 'Maler';

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
Die wasChanged Methode bestimmt, ob seit dem letzten Speichern des Modells in der aktuellen Anforderungsperiode Änderungen an einem Attribut vorgenommen wurden. Sie können auch einen Attributnamen übergeben, um festzustellen, ob ein bestimmtes Attribut geändert wurde:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Entwickler',
]);

$user->title = 'Maler';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```
## Massenzuweisung
Sie können auch die create Methode verwenden, um ein neues Modell zu speichern. Diese Methode gibt eine Modellinstanz zurück. Bevor Sie sie verwenden, müssen Sie jedoch die fillable- oder guarded-Eigenschaften des Modells festlegen, da alle Eloquent-Modelle standardmäßig nicht für Massenzuweisungen zugelassen sind.

Wenn ein Benutzer versehentlich unerwartete HTTP-Parameter überträgt, die zu Feldern in der Datenbank führen, die Sie nicht ändern möchten, kann es zu einer Massenzuweisungslücke kommen. Zum Beispiel könnte ein bösartiger Benutzer den Parameter is_admin über ein HTTP-Request übertragen und diesen an die create Methode übergeben, was es dem Benutzer ermöglichen würde, sich selbst zum Administrator zu befördern.

Daher ist es wichtig, vorher festzulegen, welche Eigenschaften des Modells für Massenzuweisungen zugelassen sind. Sie können dies mit der $fillable-Eigenschaft des Modells erreichen. Zum Beispiel können Sie das name-Attribut des Flight-Modells für Massenzuweisungen zulassen:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Eigenschaften, die für Massenzuweisungen zugelassen sind.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```
Sobald Sie die für Massenzuweisungen zugelassenen Eigenschaften festgelegt haben, können Sie die create Methode verwenden, um neue Daten in die Datenbank einzufügen. Die create Methode gibt die gespeicherte Modellinstanz zurück:
```php
$flight = app\modle\Flight::create(['name' => 'Flug 10']);
```
Wenn Sie bereits eine Modellinstanz haben, können Sie ein Array an die fill Methode übergeben, um Werte zuzuweisen:
```php
$flight->fill(['name' => 'Flug 22']);
```

$fillable kann als eine Art "Whitelist" für Massenzuweisungen betrachtet werden. Sie können auch die $guarded-Eigenschaft verwenden. Die $guarded-Eigenschaft enthält ein Array von Attributen, die nicht für Massenzuweisungen zugelassen sind. Mit anderen Worten, $guarded wird funktional eher wie eine "Blacklist" sein. Beachten Sie: Sie können nur $fillable oder $guarded verwenden, nicht beide gleichzeitig. Im folgenden Beispiel können alle Attribute außer dem Preis für Massenzuweisungen verwendet werden:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Nicht für Massenzuweisungen zugelassene Eigenschaften.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```
Wenn Sie möchten, dass alle Eigenschaften für Massenzuweisungen zugelassen sind, können Sie $guarded als leeres Array definieren:
```php
/**
 * Nicht für Massenzuweisungen zugelassene Eigenschaften.
 *
 * @var array
 */
protected $guarded = [];
```
## Andere Erstellungsmethoden
firstOrCreate/ firstOrNew
Es gibt zwei Methoden, die Sie möglicherweise für Massenzuweisungen verwenden möchten: firstOrCreate und firstOrNew. Die Methode firstOrCreate passt die Datensätze in der Datenbank anhand der angegebenen Schlüssel/Wert-Paare an. Wenn das Modell in der Datenbank nicht gefunden wird, wird ein Datensatz eingefügt, der die Attribute des ersten Parameters sowie optional die Attribute des zweiten Parameters enthält.

Die Methode firstOrNew versucht, ähnlich wie die Methode firstOrCreate, anhand der angegebenen Attribute Datensätze in der Datenbank zu finden. Wenn die Methode firstOrNew jedoch kein entsprechendes Modell findet, wird eine neue Modellinstanz zurückgegeben. Beachten Sie, dass die von firstOrNew zurückgegebene Modellinstanz noch nicht in der Datenbank gespeichert ist. Sie müssen die save-Methode manuell aufrufen, um sie zu speichern:

```php
// Flug anhand des Namens abrufen oder erstellen, wenn nicht vorhanden...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flug 10']);

// Flug anhand des Namens abrufen oder mit den Attributen name, delayed und arrival_time erstellen...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flug 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Flug anhand des Namens abrufen oder eine Instanz erstellen, wenn nicht vorhanden...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flug 10']);

// Flug anhand des Namens abrufen oder eine Modellinstanz mit den Attributen name, delayed und arrival_time erstellen...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flug 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

Es kann auch vorkommen, dass Sie bestehende Modelle aktualisieren oder bei Nichtexistenz neue Modelle erstellen möchten. Dies kann mit der Methode updateOrCreate in einem Schritt erfolgen. Ähnlich wie bei der Methode firstOrCreate persistiert updateOrCreate das Modell, sodass Sie save() nicht aufrufen müssen:

```php
// Wenn ein Flug von Oakland nach San Diego existiert, wird der Preis auf 99 USD festgelegt.
// Wenn kein passendes Modell gefunden wird, wird eins erstellt.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Modell löschen
Sie können die Methode delete auf einer Modellinstanz aufrufen, um die Instanz zu löschen:

```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Modell anhand des Primärschlüssels löschen
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## Modell anhand einer Abfrage löschen
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Modell kopieren
Sie können die Methode replicate verwenden, um eine neue, noch nicht in der Datenbank gespeicherte Instanz zu kopieren. Diese Methode ist besonders nützlich, wenn Modellinstanzen viele gemeinsame Attribute haben:

```php
$shipping = App\Address::create([
    'type' => 'Versand',
    'line_1' => '123 Beispielstraße',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'Rechnungsstellung'
]);

$billing->save();
```

## Modellvergleich
Manchmal müssen Sie überprüfen, ob zwei Modelle "gleich" sind. Die is-Methode kann verwendet werden, um schnell zu überprüfen, ob zwei Modelle dieselben Primärschlüssel, Tabellen und Datenbankverbindungen haben:

```php
if ($post->is($anotherPost)) {
    //
}
```

## Modell-Observer
Verwenden Sie die folgende Referenz[Laravel 中的模型事件与 Observer
](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Hinweis: Um den Modell-Observer in Eloquent ORM zu verwenden, muss zusätzlich composer require "illuminate/events" importiert werden.

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

