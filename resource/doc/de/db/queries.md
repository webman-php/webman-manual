# Abfrage-Generator
## Alle Zeilen abrufen
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function all(Request $request)
    {
        $users = Db::table('users')->get();
        return view('user/all', ['users' => $users]);
    }
}
```

## Spezifische Spalten abrufen
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Eine Zeile abrufen
```php
$user = Db::table('users')->where('name', 'John')->first();
```

Einzelne Spalte abrufen
```php
$titles = Db::table('roles')->pluck('title');
```
Werte des angegebenen ID-Feldes als Index festlegen
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Einen einzelnen Wert (Feld) abrufen
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```
## Duplikate entfernen
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Ergebnisse in Blöcken abrufen
Wenn Sie Tausende von Datenbankeinträgen verarbeiten müssen, kann das Abrufen dieser Daten auf einmal zeitaufwändig und zu einem Speicherüberlauf führen. In diesem Fall können Sie die `chunkById`-Methode in Betracht ziehen. Mit dieser Methode können Sie das Ergebnis in kleine Blöcke aufteilen und es an die Closure-Funktion übergeben, um es zu verarbeiten. Zum Beispiel können wir alle Daten der `users`-Tabelle in kleine Blöcke von 100 Datensätzen aufteilen und verarbeiten:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Sie können die Weitergabe der Ergebnisblöcke durch Rückgabe von `false` in der Closure beenden.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Bearbeiten der Datensätze...

    return false;
});
```

> Hinweis: Löschen Sie keine Daten in der Rückruffunktion, da dies dazu führen kann, dass einige Datensätze nicht im Ergebnis enthalten sind.

## Aggregationen
Der Abfrage-Generator bietet auch verschiedene Aggregationsmethoden wie count, max, min, avg, sum usw.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Überprüfen, ob ein Eintrag existiert
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Rohausdrücke
Prototyp
```php
selectRaw($expression, $bindings = [])
```
Manchmal müssen Sie in der Abfrage Rohausdrücke verwenden. Sie können `selectRaw()` verwenden, um einen Rohausdruck zu erstellen:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```
Ebenso stehen die Methoden `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()` für Rohausdrücke zur Verfügung.

`Db::raw($value)` wird ebenfalls zum Erstellen eines Rohausdrucks verwendet, hat jedoch keine Bindungsparameter, weshalb bei der Verwendung Vorsicht vor SQL-Injections geboten ist.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## Join-Anweisungen
```php
// Join
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// Left Join
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// Right Join
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// Cross Join
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

## Union-Anweisungen
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Where-Anweisungen
Prototyp
```php
where($column, $operator = null, $value = null)
```
Das erste Argument ist der Spaltenname, das zweite Argument kann ein beliebiger Operator sein, den das Datenbanksystem unterstützt, und das dritte ist der Wert, mit dem die Spalte verglichen werden soll.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Wenn der Operator ein Gleichheitszeichen ist, können Sie ihn weglassen, sodass dieser Ausdruck denselben Effekt hat wie der obige
$users = Db::table('users')->where('votes', 100)->get();

$users = Db::table('users')
                ->where('votes', '>=', 100)
                ->get();

$users = Db::table('users')
                ->where('votes', '<>', 100)
                ->get();

$users = Db::table('users')
                ->where('name', 'like', 'T%')
                ->get();
```

Sie können auch ein Bedingungsarray an die `where`-Funktion übergeben:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

Die `orWhere`-Methode akzeptiert die gleichen Parameter wie die `where`-Methode:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Sie können der `orWhere`-Methode als ersten Parameter auch eine Closure übergeben:
```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$users = Db::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();

```

`whereBetween` / `orWhereBetween` dient zur Überprüfung, ob der Spaltenwert zwischen zwei gegebenen Werten liegt:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

`whereNotBetween` / `orWhereNotBetween` dient zur Überprüfung, ob der Spaltenwert nicht zwischen zwei gegebenen Werten liegt:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

`whereIn` / `whereNotIn` / `orWhereIn` / `orWhereNotIn` dient zur Überprüfung, ob der Wert der Spalte in einem angegebenen Array vorhanden sein muss:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

`whereNull` / `whereNotNull` / `orWhereNull` / `orWhereNotNull` dient zur Überprüfung, ob das angegebene Feld NULL sein muss:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

`whereNotNull` dient zur Überprüfung, ob das angegebene Feld nicht NULL sein muss:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

`whereDate` / `whereMonth` / `whereDay` / `whereYear` / `whereTime` dient zum Vergleichen des Spaltenwerts mit einem angegebenen Datum:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

`whereColumn` / `orWhereColumn` dient zum Vergleichen der Werte zweier Spalten:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();

// Sie können auch einen Vergleichsoperator übergeben
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();

// Die Methode 'whereColumn' akzeptiert auch ein Array
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Parametergruppierung
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
               $query->where('votes', '>', 100)
                     ->orWhere('title', '=', 'Admin');
           })
           ->get();
```

`whereExists`
```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$users = Db::table('users')
           ->whereExists(function ($query) {
               $query->select(Db::raw(1))
                     ->from('orders')
                     ->whereRaw('orders.user_id = users.id');
           })
           ->get();
```

## Sortieren nach
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Zufällige Sortierung
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Random Order kann die Serverleistung stark beeinträchtigen, daher wird davon abgeraten, sie zu verwenden

## GroupBy / Having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Sie können der 'groupBy'-Methode auch mehrere Parameter übergeben
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## Offset / Limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```
## Einfügen
Einfügen eines einzelnen Datensatzes
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Einfügen mehrerer Datensätze
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Autoinkrement-ID
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Hinweis: Bei Verwendung von PostgreSQL wird die Methode insertGetId standardmäßig das Feld id als den Namen des automatisch inkrementierten Feldes verwenden. Wenn Sie die ID aus einer anderen "Sequenz" abrufen möchten, können Sie den Feldnamen als zweiten Parameter an die Methode insertGetId übergeben.

## Aktualisieren
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Aktualisieren oder einfügen
Manchmal möchten Sie vorhandene Datensätze in der Datenbank aktualisieren oder sie erstellen, wenn kein übereinstimmender Datensatz vorhanden ist:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Die updateOrInsert Methode wird zuerst versuchen, anhand des Schlüssels und Werts im ersten Parameter übereinstimmende Datensätze in der Datenbank zu finden. Wenn ein Datensatz gefunden wird, wird er mit den Werten im zweiten Parameter aktualisiert. Wenn kein Datensatz gefunden wird, wird ein neuer Datensatz mit den Daten aus beiden Arrays eingefügt.

## Inkrement & Dekrement
Diese Methoden akzeptieren mindestens eine Spalte, die geändert werden soll. Der zweite Parameter ist optional und steuert die Höhe der Inkrementierung oder Dekrementierung:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Sie können auch das Feld angeben, das während des Vorgangs aktualisiert werden soll:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Löschen
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Wenn Sie die Tabelle leeren möchten, können Sie die truncate Methode verwenden, die alle Zeilen löscht und den Autoinkrement-ID auf Null zurücksetzt:
```php
Db::table('users')->truncate();
```

## Pessimistisches Sperren
Der Abfrage-Builder enthält auch einige Funktionen, mit denen Sie auf der Abfrageebene eine "pessimistische Sperre" implementieren können. Wenn Sie ein "Shared Lock" in der Abfrage implementieren möchten, können Sie die Methode sharedLock verwenden. Ein Shared Lock kann verhindern, dass ausgewählte Datenspalten geändert werden, bis die Transaktion abgeschlossen ist:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Alternativ können Sie auch die Methode lockForUpdate verwenden. Durch Verwendung eines "Update Locks" können Zeilen davor geschützt werden, von anderen Shared Locks geändert oder ausgewählt zu werden:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Debuggen
Sie können die dd oder dump Methode verwenden, um Abfrageergebnisse oder SQL-Anweisungen anzuzeigen. Mit der dd Methode können Sie Debug-Informationen anzeigen und die Ausführung der Anfrage anhalten. Die dump Methode zeigt ebenfalls Debug-Informationen an, hält jedoch nicht die Ausführung der Anfrage an:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Hinweis**
> Zur Verwendung des Debuggens muss `symfony/var-dumper` installiert sein. Das entsprechende Kommando lautet: `composer require symfony/var-dumper`
