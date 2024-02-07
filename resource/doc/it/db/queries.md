# Costruttore delle query
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
Specificare il valore dell'ID come indice
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

## Distinct
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## Risultati a blocchi
Se è necessario gestire migliaia di record del database, leggerli tutti in una volta può richiedere molto tempo e può facilmente portare a un superamento della memoria. In questo caso è possibile utilizzare il metodo chunkById. Questo metodo recupera un piccolo blocco del set di risultati alla volta e lo passa a una funzione di chiusura per il trattamento. Ad esempio, possiamo suddividere tutti i dati della tabella users in blocchi di 100 record:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
È possibile interrompere il recupero dei blocchi di risultati restituendo false nella funzione di chiusura.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Process the records...

    return false;
});
```
> Nota: non eliminare i dati nel callback, poiché potrebbe causare l'esclusione di alcuni record dal set risultante.

## Aggregati
Il costruttore della query fornisce anche vari metodi di aggregazione, come count, max, min, avg, sum, ecc.
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
A volte potresti dover utilizzare un'espressione nativa nella query. È possibile utilizzare `selectRaw()` per creare un'espressione nativa:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

Allo stesso modo, sono disponibili anche i metodi di espressione nativa `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()`.

`Db::raw($value)` viene utilizzato anche per creare un'espressione nativa, ma non ha la funzionalità di binding dei parametri, quindi attenzione ai problemi di SQL injection.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## Join
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

## Union
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## Clausola WHERE
Prototipo
```php
where($column, $operator = null, $value = null)
```
Il primo argomento è il nome della colonna, il secondo è un operatore supportato da qualsiasi sistema di database e il terzo è il valore con cui confrontare la colonna.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// Quando l'operatore è l'uguale, può essere omesso, quindi questa espressione ha lo stesso effetto della precedente
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

Puoi anche passare un array di condizioni alla funzione where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();
```

Il metodo orWhere accetta gli stessi parametri del metodo where:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

Puoi passare una funzione di chiusura al metodo orWhere come primo argomento:
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

I metodi whereBetween / orWhereBetween verificano se il valore del campo si trova tra i due valori specificati:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

I metodi whereNotBetween / orWhereNotBetween verificano se il valore del campo si trova al di fuori dei due valori specificati:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

I metodi whereIn / whereNotIn / orWhereIn / orWhereNotIn verificano se il valore del campo deve essere presente in un array specificato:
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

I metodi whereDate / whereMonth / whereDay / whereYear / whereTime sono utilizzati per confrontare il valore del campo con la data specificata:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

Il metodo whereColumn / orWhereColumn è utilizzato per confrontare i valori di due campi:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// È anche possibile passare un operatore di confronto
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// Il metodo whereColumn può anche accettare un array
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

Gruppi di parametri
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

## Ordine casuale
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> L'ordinamento casuale può avere un forte impatto sulle prestazioni del server, quindi non è consigliato utilizzarlo.

## GroupBy / Having
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

## Inserimento
Inserisci singolo elemento
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
Inserisci più elementi
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
> Nota: Quando si utilizza PostgreSQL, il metodo insertGetId utilizzerà di default "id" come nome del campo con incremento automatico. Se si desidera ottenere l'ID da un'altra "sequenza", è possibile passare il nome del campo come secondo argomento al metodo insertGetId.

## Aggiornamento
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## Aggiorna o inserisci
A volte potresti voler aggiornare un record esistente nel database o, se non esiste un record corrispondente, crearne uno nuovo:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Il metodo updateOrInsert cercherà prima un record corrispondente utilizzando la chiave e il valore del primo parametro. Se trova un record, lo aggiornerà con i valori del secondo parametro. Se non trova il record, inserirà un nuovo record con i dati dei due array.

## Incremento e decremento
Entrambi i metodi accettano almeno un parametro: il nome della colonna da modificare. Il secondo parametro è opzionale e controlla la quantità di incremento o decremento della colonna:
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
## Elimina
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
Se hai bisogno di svuotare una tabella, puoi utilizzare il metodo truncate, che eliminerà tutte le righe e reimposterà l'ID incrementale a zero:
```php
Db::table('users')->truncate();
```

## Blocco pessimista
Il costruttore della query include anche alcune funzioni che possono aiutarti a implementare il "blocco pessimista" nella sintassi della select. Se desideri implementare un "blocco condiviso" nella query, puoi utilizzare il metodo sharedLock. Il blocco condiviso impedisce che le colonne dei dati selezionati vengano modificate fino a quando la transazione non viene confermata:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
Oppure, puoi utilizzare il metodo lockForUpdate. Utilizzando il blocco "update" si evita che le righe vengano modificate o selezionate da altri blocchi condivisi:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## Debug
Puoi utilizzare i metodi dd o dump per visualizzare i risultati della query o le istruzioni SQL. Con il metodo dd puoi visualizzare le informazioni di debug e interrompere l'esecuzione della richiesta. Il metodo dump, invece, mostra le informazioni di debug senza interrompere l'esecuzione della richiesta:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **Nota**
> Per il debug è necessario installare `symfony/var-dumper`, tramite il comando `composer require symfony/var-dumper`.
