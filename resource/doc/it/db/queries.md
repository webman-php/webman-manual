# Costruttore della query
## Ottenere tutte le righe
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

## Ottenere colonne specifiche
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## Ottenere una riga
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## Ottenere una colonna
```php
$titles = Db::table('roles')->pluck('title');
```
Specificare il valore del campo id come indice
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## Ottenere un singolo valore (campo)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## Rimuovere duplicati
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Risultato a blocchi
Se è necessario gestire migliaia di record di database, recuperarli tutti in una volta sarebbe molto dispendioso in termini di tempo e potrebbe causare facilmente un superamento della memoria. In tal caso, è possibile considerare l'utilizzo del metodo `chunkById`. Questo metodo recupera un piccolo insieme di record risultanti e lo passa a una funzione di chiusura per il trattamento. Ad esempio, possiamo suddividere tutti i dati della tabella "users" in un piccolo insieme di 100 record da elaborare una sola volta:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
È possibile interrompere il recupero dei risultati a blocchi restituendo false all'interno della funzione di chiusura.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Elabora i record...

    return false;
});
```

> Nota: evitare di eliminare i dati all'interno della funzione di richiamata, poiché ciò potrebbe portare a risultati mancanti nel set di risultati

## Aggregazione
Il costruttore della query fornisce anche vari metodi di aggregazione come count, max, min, avg, sum, ecc.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## Verificare se un record esiste
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## Espressioni native
Prototipo
```php
selectRaw($expression, $bindings = [])
```
A volte potresti dover utilizzare espressioni native nella query. È possibile utilizzare `selectRaw()` per creare un'espressione nativa:

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

Allo stesso modo, sono disponibili anche i metodi di espressione nativa `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()`.

`Db::raw($value)` viene anche utilizzato per creare un'espressione nativa, ma non supporta il binding dei parametri, quindi è necessario prestare attenzione ai problemi di SQL injection durante l'utilizzo.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## Join statement
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

## Union statement
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## Dichiarazione Where
Prototipo 
```php
where($column, $operator = null, $value = null)
```
Il primo parametro è il nome della colonna, il secondo parametro è qualsiasi operatore supportato dal sistema di database e il terzo è il valore con cui confrontare la colonna.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Quando l'operatore è l'uguale, può essere omesso, quindi questa istruzione è equivalente alla precedente
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

È inoltre possibile passare un array di condizioni alla funzione Where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

Il metodo orWhere funziona esattamente come il metodo where:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

È possibile passare una funzione di chiusura al metodo orWhere come primo parametro:
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

Il metodo whereBetween / orWhereBetween verifica se il valore del campo è compreso tra i due valori specificati:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

Il metodo whereNotBetween / orWhereNotBetween verifica se il valore del campo è al di fuori dei due valori specificati:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

Il metodo whereIn / whereNotIn / orWhereIn / orWhereNotIn verifica se il valore del campo esiste nell'array specificato:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

I metodi whereNull / whereNotNull / orWhereNull / orWhereNotNull verificano se il campo specificato deve essere NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

Il metodo whereNotNull verifica se il campo specificato non è NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

I metodi whereDate / whereMonth / whereDay / whereYear / whereTime confrontano il valore del campo con la data specificata:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

I metodi whereColumn / orWhereColumn confrontano se i valori di due campi sono uguali:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// È anche possibile passare un operatore di confronto
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// Il metodo whereColumn può accettare anche un array
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Raggruppamento di parametri
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

## OrderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## Ordinamento casuale
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> L'ordinamento casuale avrà un grande impatto sulle prestazioni del server, quindi non è consigliato l'utilizzo

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// È possibile passare più parametri al metodo groupBy
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

## Inserir
Inserire singolarmente
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Inserire multipli
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## ID incrementale
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> Nota: Quando si utilizza PostgreSQL, il metodo insertGetId prenderà in modo predefinito `id` come nome del campo incrementale automatico. Se si desidera ottenere l'ID da altre "sequenze", è possibile passare il nome del campo come secondo parametro al metodo insertGetId.

## Aggiornare
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Aggiornare o inserire
A volte potresti voler aggiornare un record esistente nel database o, se non esiste, crearlo:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Il metodo updateOrInsert cercherà prima di utilizzare la chiave e il valore del primo parametro per trovare un record corrispondente nel database. Se il record esiste, verrà aggiornato con i valori del secondo parametro. Se il record non è trovato, verrà inserito un nuovo record con i dati dei due array.

## Incremento e decremento
Entrambi i metodi richiedono almeno un parametro: la colonna da modificare. Il secondo parametro è facoltativo e controlla la quantità di incremento o decremento della colonna:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
È anche possibile specificare il campo da aggiornare durante l'operazione:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## Eliminare
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Se è necessario svuotare la tabella, è possibile utilizzare il metodo truncate, che eliminerà tutte le righe e reimposterà l'ID incrementale a zero:
```php
Db::table('users')->truncate();
```

## Blocco pessimistico
Il costruttore della query include anche alcune funzioni per aiutare nell'implementazione del "blocco pessimistico" nella sintassi `select`. Se si desidera implementare un "blocco condiviso" nella query, è possibile utilizzare il metodo sharedLock. Il blocco condiviso impedisce la modifica delle colonne dei dati selezionate fino a quando la transazione non viene confermata:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
In alternativa, è possibile utilizzare il metodo lockForUpdate. Utilizzando il blocco "update" si evita che le righe vengano modificate o selezionate da altri blocchi condivisi:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Debug
È possibile utilizzare i metodi dd o dump per visualizzare i risultati della query o l'istruzione SQL. Utilizzando il metodo dd si ottiene la visualizzazione delle informazioni di debug e l'arresto dell'esecuzione della richiesta. Il metodo dump mostra le informazioni di debug, ma non ferma l'esecuzione della richiesta:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Nota**
> Per il debug è necessario installare `symfony/var-dumper`, il comando è `composer require symfony/var-dumper`.
