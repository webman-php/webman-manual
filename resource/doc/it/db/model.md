# Guida Rapida

Il modello webman si basa su [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Ogni tabella del database ha un "modello" corrispondente per interagire con quella tabella. Puoi utilizzare il modello per interrogare i dati della tabella e inserire nuovi record nella tabella.

Prima di iniziare, assicurati di configurare la connessione al database nel file `config/database.php`.

> Nota: Per supportare gli osservatori del modello Eloquent ORM, è necessario importare anche `composer require "illuminate/events"` [Esempio](#model-observers)

## Esempio
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * Nome della tabella associata al modello
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * Ridefinire la chiave primaria, di default è id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * Indica se mantenere automaticamente i timestamp
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## Nome della Tabella
Puoi specificare una tabella personalizzata per il modello definendo l'attributo table nel modello:
```php
class User extends Model
{
    /**
     * Nome della tabella associata al modello
     *
     * @var string
     */
    protected $table = 'user';
}
```

## Chiave Primaria
Eloquent assume che ogni tabella abbia una colonna chiave primaria chiamata id. Puoi definire un attributo protetto $primaryKey per sovrascrivere questa convenzione.
```php
class User extends Model
{
    /**
     * Ridefinire la chiave primaria, di default è id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent assume che la chiave primaria sia un valore intero incrementale, il che significa che di default la chiave primaria verrà automaticamente convertita in tipo int. Se desideri utilizzare una chiave primaria non incrementale o non numerica, è necessario impostare l'attributo pubblico $incrementing su false.
```php
class User extends Model
{
    /**
     * Indica se la chiave primaria del modello è incrementale
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Se la tua chiave primaria non è un intero, dovrai impostare l'attributo protetto $keyType del modello su stringa:
```php
class User extends Model
{
    /**
     * "Tipo" dell'ID auto incrementante.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Timestamp
Per impostazione predefinita, Eloquent si aspetta che nella tua tabella esistano created_at e updated_at. Se non desideri che Eloquent gestisca automaticamente queste due colonne, imposta l'attributo $timestamps del modello su false:
```php
class User extends Model
{
    /**
     * Indica se mantenere automaticamente i timestamp
     *
     * @var bool
     */
    public $timestamps = false;
}
```
Se hai bisogno di personalizzare il formato dei timestamp, imposta l'attributo $dateFormat nel tuo modello. Questo attributo determina il formato di archiviazione della data nel database e il formato di serializzazione del modello in un array o JSON:
```php
class User extends Model
{
    /**
     * Formato di archiviazione del timestamp
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Se devi personalizzare i nomi dei campi di archiviazione dei timestamp, puoi farlo impostando i valori delle costanti CREATED_AT e UPDATED_AT nel modello:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Connessione al Database
Per default, i modelli Eloquent utilizzeranno la connessione al database predefinita configurata nell'applicazione. Se desideri specificare una connessione diversa per il modello, imposta l'attributo $connection:
```php
class User extends Model
{
    /**
     * Nome della connessione del modello
     *
     * @var string
     */
    protected $connection = 'nome-connessione';
}
```

## Valori Predefiniti
Se desideri definire valori predefiniti per alcuni attributi del modello, puoi farlo definendo l'attributo $attributes nel modello:
```php
class User extends Model
{
    /**
     * Valori predefiniti del modello.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## Recupero del Modello
Dopo aver creato il modello e associato la tabella del database, puoi iniziare a interrogare i dati dal database. Immagina ogni modello Eloquent come un potente costruttore di query, puoi usarlo per recuperare rapidamente i dati associati alla tabella. Ad esempio:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Nota: Poiché i modelli Eloquent sono anche costruttori di query, dovresti anche leggere tutti i metodi disponibili nel [costruttore di query](queries.md) che puoi utilizzare nelle query Eloquent.

## Vincoli Aggiuntivi
Il metodo all di Eloquent restituirà tutti i risultati del modello. Poiché ogni modello Eloquent funge anche da costruttore di query, puoi aggiungere condizioni di query e quindi utilizzare il metodo get per ottenere i risultati della query:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Ricarica del Modello
Puoi utilizzare i metodi fresh e refresh per ricaricare il modello. Il metodo fresh ricaricherà il modello dal database. L'istanza esistente del modello non verrà influenzata:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

Il metodo refresh utilizzerà i nuovi dati dal database per aggiornare l'istanza esistente del modello. Inoltre, le relazioni caricate verranno ricaricate:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Collezioni
I metodi all e get di Eloquent possono restituire più risultati, restituendo un'istanza di `Illuminate\Database\Eloquent\Collection`. La classe `Collection` fornisce numerosi metodi di utilità per manipolare i risultati Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Utilizzo di Cursori
Il metodo cursor ti consente di attraversare il database utilizzando un cursore, eseguendo una sola query. Quando si lavora con grandi quantità di dati, il metodo cursor può ridurre notevolmente l'utilizzo della memoria:
```php
foreach (app\model\User::where('sex', 1)->cursor() as $user) {
    //
}
```

Il metodo cursor restituisce un'istanza di `Illuminate\Support\LazyCollection`. Le [collezioni lazily](https://laravel.com/docs/7.x/collections#lazy-collections) ti consentono di utilizzare la maggior parte dei metodi di collezione di Laravel, caricando un unico modello in memoria ogni volta:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## Sottoquery Selects
Eloquent offre un supporto avanzato alle sottoquery, consentendoti di estrarre informazioni da tabelle correlate con una singola query. Ad esempio, immaginiamo di avere una tabella di destinazioni destinations e una tabella dei voli flight che includa un campo di arrivo arrived_at, che indica quando un volo arriva a destinazione.

Utilizzando i metodi select e addSelect forniti dalle funzionalità delle sottoquery, possiamo selezionare tutte le destinazioni destinations e il nome dell'ultimo volo arrivato in ciascuna destinazione:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## Ordinamento tramite Sottoquery
Inoltre, il metodo orderBy del costruttore della query supporta anche le sottoquery. Possiamo utilizzare questa funzionalità per ordinare tutte le destinazioni in base all'orario di arrivo dell'ultimo volo a destinazione. Allo stesso modo, ciò consente di eseguire una singola query sul database:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Recupero di un Singolo Modello/Collezione
Oltre a recuperare tutti i record da una tabella specificata, è possibile utilizzare i metodi find, first o firstWhere per recuperare un singolo record. Questi metodi restituiscono un'istanza singola del modello anziché un insieme di modelli:
```php
// Trovare un modello per chiave primaria...
$flight = app\model\Flight::find(1);

// Trovare il primo modello che soddisfa la condizione di ricerca...
$flight = app\model\Flight::where('active', 1)->first();

// Trovare rapidamente il primo modello che soddisfa la condizione di ricerca...
$flight = app\model\Flight::firstWhere('active', 1);
```

È anche possibile utilizzare come parametro un array di chiavi primarie per il metodo find, il quale restituirà una collezione di record corrispondenti:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

A volte si potrebbe desiderare di eseguire determinate azioni in caso di mancata ricerca del primo risultato. Il metodo firstOr restituirà il primo risultato se trovato, altrimenti eseguirà il callback specificato. Il valore restituito dal callback sarà il valore restituito dal metodo firstOr:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
Il metodo firstOr accetta anche un array di colonne per la query:
```php
$model = app\model\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## Eccezione "Modello Non Trovato"
A volte potresti desiderare di lanciare un'eccezione se il modello non è trovato. Questo è particolarmente utile nei controller e nelle route. I metodi findOrFail e firstOrFail recupereranno il primo risultato della query e, se non trovato, lanceranno un'eccezione Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\model\Flight::findOrFail(1);
$model = app\model\Flight::where('legs', '>', 100)->firstOrFail();
```
## Collezioni di recupero
Puoi utilizzare anche i metodi count, sum e max forniti dal Query Builder e altre funzioni delle collezioni per operare sulle collezioni. Questi metodi restituiranno solo valori scalari appropriati anziché un'istanza del modello:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Inserimento
Per inserire un nuovo record nel database, prima crea un'istanza del nuovo modello, imposta le sue proprietà e quindi chiama il metodo save:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Aggiunge un nuovo record nella tabella degli utenti
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validazione della richiesta

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

I timestamp created_at e updated_at verranno impostati automaticamente (quando la proprietà $timestamps del modello è impostata su true), senza la necessità di assegnarli manualmente.


## Aggiornamento
Il metodo save può anche essere utilizzato per aggiornare un modello esistente nel database. Per aggiornare un modello, è necessario recuperarlo, impostare le proprietà da aggiornare e quindi chiamare il metodo save. Allo stesso modo, il timestamp updated_at viene aggiornato automaticamente e non è necessario assegnarlo manualmente:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Aggiornamento in batch
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Verifica delle modifiche alle proprietà
Eloquent fornisce i metodi isDirty, isClean e wasChanged per verificare lo stato interno del modello e determinare come sono cambiate le sue proprietà dall'ultimo caricamento. Il metodo isDirty determina se sono state apportate modifiche a qualsiasi proprietà dal caricamento del modello. È possibile passare il nome di una specifica proprietà per determinare se quella proprietà è stata modificata. Il metodo isClean è l'opposto di isDirty e accetta anche un parametro di proprietà opzionale:
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
Il metodo wasChanged determina se è stata apportata una modifica a qualsiasi proprietà dall'ultimo salvataggio del modello durante il ciclo di richiesta corrente. È inoltre possibile passare il nome di una specifica proprietà per vedere se quella proprietà è stata modificata:
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

## Assegnazione in batch
Puoi anche utilizzare il metodo create per salvare un nuovo modello. Questo metodo restituirà un'istanza del modello. Tuttavia, prima di utilizzarlo, è necessario specificare le proprietà fillable o guarded sul modello, poiché tutti i modelli Eloquent, per impostazione predefinita, non consentono l'assegnazione in batch.

Quando gli utenti inviano imprevisti parametri HTTP e quei parametri modificano campi nel database che non si desidera modificare, si verifica una vulnerabilità di assegnazione in batch. Ad esempio, un utente malintenzionato potrebbe inviare il parametro is_admin tramite una richiesta HTTP e quindi passarlo al metodo create, consentendo all'utente di promuoversi a amministratore.

Quindi, prima di iniziare, è necessario definire quali attributi del modello possono essere assegnati in batch. Questo può essere fatto attraverso la proprietà $fillable del modello. Ad esempio, diciamo che l'attributo name del modello Flight può essere assegnato in batch:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributi che possono essere assegnati in batch.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
Una volta definiti gli attributi che possono essere assegnati in batch, è possibile utilizzare il metodo create per inserire nuovi dati nel database. Il metodo create restituirà un'istanza del modello salvato:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
Se si dispone già di un'istanza del modello, è possibile passare un array al metodo fill per assegnare i valori:
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable può essere considerato come una "lista bianca" per l'assegnazione in batch, e è anche possibile utilizzare la proprietà $guarded per lo stesso scopo. $guarded contiene un array di attributi che non possono essere assegnati in batch, quindi, funzionalmente, $guarded assomiglierà più a una "lista nera". Nota: è possibile utilizzare solo $fillable o $guarded, non entrambi contemporaneamente. Nell'esempio seguente, tutti gli attributi tranne price possono essere assegnati in batch:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributi che non possono essere assegnati in batch.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

Se si desidera consentire l'assegnazione in batch di tutti gli attributi, è possibile definire $guarded come un array vuoto:
```php
/**
 * Attributi che non possono essere assegnati in batch.
 *
 * @var array
 */
protected $guarded = [];
```

## Altri metodi di creazione
firstOrCreate / firstOrNew
Ci sono due metodi che potresti utilizzare per l'assegnazione in batch: firstOrCreate e firstOrNew. Il metodo firstOrCreate cercherà nel database per trovare i dati corrispondenti tramite coppie chiave/valore fornite. Se il modello non viene trovato nel database, verrà inserito un record contenente i valori del primo parametro, e facoltativamente, i valori del secondo parametro.

Il metodo firstOrNew funziona in modo simile al metodo firstOrCreate, ma se non trova un modello corrispondente, restituirà una nuova istanza del modello. Si noti che l'istanza di modello restituita da firstOrNew non è ancora salvata nel database e sarà necessario chiamare manualmente il metodo save:
```php
// Cerca il volo per nome, se non esiste, crea...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// Cerca il volo per nome, o crea un record con name, delayed e arrival_time indicati...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Cerca il volo per nome, se non esiste, crea un'istanza...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// Cerca il volo per nome, o crea un'istanza con name, delayed e arrival_time indicati...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

Potresti anche incontrare situazioni in cui desideri aggiornare un modello esistente o crearne uno nuovo se non esiste. Il metodo updateOrCreate consente di fare tutto questo in un'unica operazione. Simile al metodo firstOrCreate, updateOrCreate persiste il modello, quindi non è necessario chiamare save():
```php
// Se esiste un volo da Oakland a San Diego, il prezzo sarà di 99 dollari.
// Se non corrisponde a un modello esistente, ne crea uno.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Eliminazione del modello
Puoi chiamare il metodo delete sull'istanza del modello per eliminarla:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Eliminazione del modello per primary key
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Eliminazione del modello per query
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Copia del modello
È possibile utilizzare il metodo replicate per copiare una nuova istanza non salvata nel database; questo metodo è utile quando le istanze dei modelli condividono molte proprietà simili:
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();

```

## Confronto tra modelli
A volte potresti aver bisogno di verificare se due modelli sono "uguali". Il metodo is può essere utilizzato per verificare rapidamente se due modelli hanno la stessa chiave primaria, tabella e connessione al database:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Osservatori del modello
Per ulteriori informazioni sull'utilizzo degli osservatori del modello, consulta [Eventi del modello e osservatore in Laravel]
(https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Nota: Eloquent ORM richiede l'importazione aggiuntiva di composer require "illuminate/events" per supportare gli osservatori del modello.

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
