# Abfrage-Builder
## Abrufen aller Zeilen
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
        return view('user/all', ['benutzer' => $users]);
    }
}
```

## Abrufen von bestimmten Spalten
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Abrufen einer Zeile
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Abrufen einer Spalte
```php
$titles = Db::table('roles')->pluck('title');
```
Wert der spezifizierten ID-Spalte als Index
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Abrufen eines einzelnen Werts (Feldes)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Duplikate entfernen
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Ergebnisse in Blöcke aufteilen
Wenn Sie Tausende von Datensätzen aus einer Datenbank abrufen müssen, kann das vollständige Abrufen dieser Daten sehr zeitaufwändig sein und zu einem Speicherüberlauf führen. In solchen Fällen können Sie die `chunkById`-Methode in Betracht ziehen. Diese Methode ruft das Ergebnis in kleinen Blöcken ab und übergibt es einer Closure-Funktion zur Verarbeitung. Sie könnten beispielsweise alle Datensätze der Tabelle "users" in Blöcke von 100 Datensätzen aufteilen:

```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
Sie können die Iteration durch die Blöcke beenden, indem Sie in der Closure `false` zurückgeben.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Verarbeitung der Datensätze...

    return false;
});
```

> Hinweis: Löschen Sie keine Daten in der Closure, da dies dazu führen kann, dass einige Datensätze nicht im Ergebnis enthalten sind.

## Aggregation
Der Abfrage-Builder bietet auch verschiedene Aggregationsmethoden wie z.B. count, max, min, avg, sum usw.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Überprüfen, ob ein Datensatz existiert
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Raw-Abfrageausdrücke
Prototyp
```php
selectRaw($expression, $bindings = [])
```
Manchmal müssen Sie in einer Abfrage einen Raw-Query-Ausdruck verwenden. Sie können `selectRaw()` verwenden, um einen Raw-Query-Ausdruck zu erstellen:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

Ebenso stehen `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()` und `groupByRaw()`-Methoden für Raw-Query-Ausdrücke zur Verfügung.

`Db::raw($value)` wird ebenfalls verwendet, um einen Raw-Query-Ausdruck zu erstellen, hat jedoch keine Bindungsparameter-Funktion und sollte daher vorsichtig bei der Verwendung aufgrund von möglichen SQL-Injection-Problemen sein.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```

## Join-Klauseln
```php
// join
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// leftJoin            
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// rightJoin
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// crossJoin    
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

## Union-Klauseln
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## WHERE-Anweisung
Prototyp
```php
where($column, $operator = null, $value = null)
```
Der erste Parameter ist der Spaltenname, der zweite Parameter ist ein beliebiger Operator, den das Datenbanksystem unterstützt, und der dritte ist der Wert, mit dem die Spalte verglichen werden soll.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Wenn der Operator das Gleichheitszeichen ist, kann es weggelassen werden, daher ist dieser Ausdruck mit dem vorherigen äquivalent
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

Sie können auch ein Bedingungsarray an die where-Funktion übergeben:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

Die orWhere-Methode akzeptiert die gleichen Parameter wie die where-Methode:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Sie können der orWhere-Methode auch eine Closure als ersten Parameter übergeben:
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

Die whereBetween / orWhereBetween Methode überprüft, ob der Wert des Felds zwischen den beiden angegebenen Werten liegt:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Die whereNotBetween / orWhereNotBetween Methode überprüft, ob der Wert des Felds nicht zwischen den beiden angegebenen Werten liegt:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Die Methoden whereIn / whereNotIn / orWhereIn / orWhereNotIn überprüfen, ob der Wert des Felds in einem angegebenen Array vorhanden sein muss:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

Die Methoden whereNull / whereNotNull / orWhereNull / orWhereNotNull überprüfen, ob das angegebene Feld NULL sein muss bzw. nicht NULL sein darf:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

Die Methoden whereDate / whereMonth / whereDay / whereYear / whereTime werden verwendet, um den Wert des Felds mit dem angegebenen Datum zu vergleichen:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

Die whereColumn / orWhereColumn Methoden werden verwendet, um zu überprüfen, ob die Werte zweier Spalten gleich sind:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// Sie können auch einen Vergleichsoperator übergeben
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// Die whereColumn-Methode akzeptiert auch Arrays
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

whereExists
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

## orderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Zufällige Reihenfolge
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> Die zufällige Reihenfolge kann die Serverleistung erheblich beeinträchtigen und wird daher nicht empfohlen.

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// Sie können der groupBy-Methode auch mehrere Parameter übergeben
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## offset / limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```

## Einfügen
Einfügen von einzelnen Datensätzen
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Einfügen von mehreren Datensätzen
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## Automatische ID-Zuweisung
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> Hinweis: Bei der Verwendung von PostgreSQL weist die insertGetId-Methode standardmäßig "id" als den automatisch inkrementierten Feldnamen zu. Wenn Sie die ID aus einer anderen "Sequenz" erhalten möchten, können Sie den Feldnamen als zweiten Parameter an die insertGetId-Methode übergeben.

## Aktualisierung
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Aktualisieren oder Einfügen
Manchmal möchten Sie vorhandene Datensätze in der Datenbank aktualisieren oder, falls keine übereinstimmenden Datensätze gefunden werden, diese erstellen:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Die updateOrInsert-Methode versucht zunächst, anhand des Schlüssels und des Werts im ersten Parameter übereinstimmende Datensätze in der Datenbank zu finden. Wenn ein Datensatz vorhanden ist, werden die Datensätze mit den Werten im zweiten Parameter aktualisiert. Wenn kein Datensatz gefunden wird, wird ein neuer Datensatz mit den kombinierten Daten beider Arrays eingefügt.

## Inkrementieren & Dekrementieren
Beide Methoden akzeptieren mindestens eine Spalte als Parameter. Der zweite Parameter ist optional und dient zur Steuerung des Inkrements oder Dekrements der Spalte:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
Sie können auch das zu aktualisierende Feld während des Vorgangs angeben:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Löschen
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Wenn Sie alle Datensätze in einer Tabelle löschen möchten, können Sie die truncate-Methode verwenden, die alle Zeilen löscht und die automatische ID auf null zurücksetzt:
```php
Db::table('users')->truncate();
```

## Pessimistisches Sperren
Der Query Builder enthält auch einige Funktionen, die Ihnen bei der Umsetzung von "pessimistischem Locking" in SELECT-Statements helfen können. Wenn Sie in Ihrer Abfrage ein "shared lock" implementieren möchten, können Sie die sharedLock-Methode verwenden. Der shared lock verhindert, dass die ausgewählten Datensätze geändert werden, bis die Transaktion abgeschlossen ist:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Alternativ können Sie die lockForUpdate-Methode verwenden. Die Verwendung des "update"-Locks verhindert, dass vom Locking anderer Datensätze geändert oder ausgewählt wird:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```
## Debugging
Sie können die Ergebnisse von Abfragen oder SQL-Anweisungen mit der `dd`- oder `dump`-Methode ausgeben. Die Verwendung der `dd`-Methode zeigt Debug-Informationen an und stoppt dann die Ausführung der Anfrage. Die `dump`-Methode zeigt ebenfalls Debug-Informationen an, stoppt jedoch nicht die Ausführung der Anfrage:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Hinweis**
> Zur Debugging ist die Installation von `symfony/var-dumper` erforderlich. Verwenden Sie den Befehl `composer require symfony/var-dumper` zur Installation.
