# Inizio rapido

Il modello webman è basato su [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). Ogni tabella del database ha un "modello" corrispondente per interagire con quella tabella. Puoi utilizzare il modello per interrogare i dati nella tabella e inserire nuovi record nella tabella.

Prima di iniziare, assicurati di configurare la connessione al database nel file `config/database.php`.

> Nota: Per supportare l'osservatore del modello con Eloquent ORM, è necessario importare "illuminate/events" aggiuntivo con `composer require "illuminate/events"` [Esempio](#modello-osservatore)

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
     * Ridefinisce la chiave primaria, di default è id
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

## Nome tabella
Puoi specificare una tabella personalizzata definendo l'attributo table nel modello:
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

## Chiave primaria
Eloquent presume anche che ogni tabella abbia una colonna chiave primaria chiamata id. Puoi definire un attributo protetto $primaryKey per sovrascrivere questa convenzione.
```php
class User extends Model
{
    /**
     * Ridefinisce la chiave primaria, di default è id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent presume che la chiave primaria sia un valore intero incrementale, il che significa che di default la chiave primaria verrà automaticamente convertita in tipo int. Se desideri utilizzare una chiave primaria non incrementale o non numerica, devi impostare l'attributo pubblico $incrementing su false.
```php
class User extends Model
{
    /**
     * Indica se la chiave primaria è incrementale
     *
     * @var bool
     */
    public $incrementing = false;
}
```

Se la tua chiave primaria non è un intero, devi impostare l'attributo protetto $keyType del modello su stringa:
```php
class User extends Model
{
    /**
     * "Tipo" dell'ID incrementale automatico.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## Timestamp
Per impostazione predefinita, Eloquent si aspetta che nella tua tabella ci siano created_at e updated_at. Se non desideri che Eloquent gestisca automaticamente queste due colonne, imposta l'attributo $timestamps del modello su false:
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

Se è necessario personalizzare il formato dei timestamp, imposta l'attributo $dateFormat nel modello. Questo attributo determina il modo in cui le proprietà della data vengono memorizzate nel database e il formato in cui il modello viene serializzato in un array o JSON:
```php
class User extends Model
{
    /**
     * Formato di memorizzazione dei timestamp
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

Se è necessario personalizzare i nomi dei campi in cui memorizzare i timestamp, è possibile impostare i valori delle costanti CREATED_AT e UPDATED_AT nel modello:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## Connessione al database
Di default, il modello Eloquent utilizza la connessione al database predefinita configurata dalla tua applicazione. Se desideri specificare una connessione diversa per il modello, imposta l'attributo $connection:
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

## Valori di default
Se desideri definire valori predefiniti per alcune proprietà del modello, puoi definire l'attributo $attributes nel modello:
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

## Recupero del modello
Dopo aver creato il modello e la tabella associata, è possibile interrogare i dati dal database. Immagina ogni modello Eloquent come un potente costruttore di query che consente di interrogare rapidamente la tabella associata. Ad esempio:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> Nota: Poiché il modello Eloquent è anche un costruttore di query, è consigliabile leggere tutti i metodi disponibili nel [costruttore di query](queries.md) e utilizzarli nelle query Eloquent.

## Vincoli aggiuntivi
Il metodo all di Eloquent restituirà tutti i risultati del modello. Poiché ogni modello Eloquent funge da costruttore di query, è possibile aggiungere condizioni di ricerca e quindi ottenere i risultati della query con il metodo get:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## Ricarica del modello
È possibile utilizzare i metodi fresh e refresh per ricaricare il modello. Il metodo fresh ricaricherà il modello dal database, senza influenzare l'istanza del modello esistente:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

Il metodo refresh assegnerà di nuovo i dati del modello dall'origine dei dati nel database all'istanza esistente del modello. Inoltre, le relazioni caricate verranno ricaricate:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## Collezione
I metodi all e get di Eloquent possono restituire più risultati, restituendo un'istanza di `Illuminate\Database\Eloquent\Collection`. La classe `Collection` fornisce numerose funzioni di supporto per manipolare i risultati Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## Uso del cursore
Il metodo cursor consente di attraversare il database utilizzando un cursore, eseguendo la query una sola volta. Quando si lavora con grandi quantità di dati, il metodo cursor può ridurre notevolmente l'utilizzo della memoria:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

Il metodo cursor restituisce un'istanza di `Illuminate\Support\LazyCollection`. Le [Lazy Collection](https://laravel.com/docs/7.x/collections#lazy-collections) ti consente di utilizzare la maggior parte dei metodi di raccolta di Laravel e carica solo un singolo modello in memoria per volta:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```
## Sottoquery nelle selezioni
Eloquent fornisce un supporto avanzato per le sottoquery, consentendoti di estrarre informazioni dalle tabelle correlate con una singola query. Ad esempio, supponiamo di avere una tabella delle destinazioni chiamata destinations e una tabella dei voli per raggiungere le destinazioni chiamata flights. La tabella dei voli flights contiene un campo arrival_at per indicare quando il volo arriva alla destinazione.

Utilizzando i metodi select e addSelect forniti dalla funzionalità delle sottoquery, possiamo eseguire una singola query per estrarre tutte le destinazioni e il nome dell'ultimo volo ad arrivare a ciascuna destinazione:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```
## Ordinamento basato su sottoquery
Inoltre, il metodo orderBy della query builder supporta anche le sottoquery. Possiamo utilizzare questa funzionalità per ordinare tutte le destinazioni in base al tempo dell'ultimo volo ad arrivare alla destinazione. Di nuovo, questo può essere fatto con una singola query al database:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## Recupero di un singolo modello / collezione
Oltre a recuperare tutti i record da una tabella specifica, è possibile utilizzare i metodi find, first o firstWhere per recuperare un singolo record. Questi metodi restituiscono un'istanza del modello anziché una collezione di modelli:
```php
// Recupera un modello tramite la chiave primaria...
$flight = app\model\Flight::find(1);

// Recupera il primo modello corrispondente alla condizione di query...
$flight = app\model\Flight::where('active', 1)->first();

// Implementazione rapida del recupero del primo modello corrispondente alla condizione di query...
$flight = app\model\Flight::firstWhere('active', 1);
```
Puoi anche chiamare il metodo find con un array di chiavi primarie come argomento e restituirà una collezione di record corrispondente:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```
A volte potresti voler eseguire un'azione alternativa nel caso in cui non venga trovato un risultato durante la ricerca del primo risultato. Il metodo firstOr restituirà il primo risultato se trovato, altrimenti eseguirà il callback fornito. Il valore restituito dal callback sarà il valore restituito dal metodo firstOr:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
Il metodo firstOr accetta anche un array di colonne come query:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## Eccezione "non trovato"
A volte potresti desiderare di generare un'eccezione quando un modello non viene trovato. Questo è particolarmente utile nei controller e nelle route. I metodi findOrFail e firstOrFail recupereranno il primo risultato della query e, se non trovato, solleveranno un'eccezione Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## Recupero di una collezione
È inoltre possibile utilizzare i metodi count, sum e max forniti dal query builder, insieme ad altre funzioni di aggregazione, per operare su una collezione. Questi metodi restituiranno solo valori scalari appropriati anziché un'istanza del modello:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## Inserimento
Per inserire un nuovo record nel database, prima crea una nuova istanza del modello, imposta le proprietà dell'istanza e successivamente chiama il metodo save:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * Aggiunge un nuovo record alla tabella degli utenti
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Valida la richiesta

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```
I timestamp created_at e updated_at verranno impostati automaticamente (quando la proprietà $timestamps del modello è true) e non è necessario assegnarli manualmente.

## Aggiornamento
Il metodo save può anche essere utilizzato per aggiornare un modello esistente nel database. Per aggiornare un modello, devi prima recuperarlo, impostare le proprietà da aggiornare e successivamente chiamare il metodo save. Allo stesso modo, il timestamp updated_at verrà automaticamente aggiornato, quindi non è necessario assegnarlo manualmente:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## Aggiornamento batch
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## Verifica delle modifiche dell'attributo
Eloquent fornisce i metodi isDirty, isClean e wasChanged per verificare lo stato interno del modello e determinare come sono cambiate le sue proprietà dal momento del caricamento iniziale. Il metodo isDirty determina se sia stata modificata una qualsiasi proprietà dal momento del caricamento del modello. Puoi passare il nome di una proprietà specifica per determinare se una determinata proprietà è stata modificata. Il metodo isClean è l'opposto di isDirty e accetta anche un parametro proprietà opzionale:
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
Il metodo wasChanged determina se una qualsiasi proprietà è stata modificata dall'ultimo salvataggio del modello durante il ciclo di richiesta corrente. Puoi anche passare il nome di una proprietà per vedere se una proprietà specifica è stata modificata:
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
## Assegnazione di massa
Puoi anche utilizzare il metodo `create` per salvare un nuovo modello. Questo metodo restituirà un'istanza del modello. Tuttavia, prima di utilizzarlo, è necessario specificare le proprietà `fillable` o `guarded` del modello, in quanto tutti i modelli Eloquent non consentono l'assegnazione di massa per impostazione predefinita.

Un'asimmetria di assegnazione di massa si verifica quando un utente invia parametri HTTP imprevisti tramite una richiesta, e questi parametri modificano campi nel database che non dovrebbero essere modificati. Ad esempio, un utente malintenzionato potrebbe inviare il parametro `is_admin` tramite una richiesta HTTP e passarlo al metodo `create`, consentendo così all'utente di autoproclamarsi amministratore.

Pertanto, prima di iniziare, è necessario definire quali attributi del modello possono essere assegnati in massa. Questo può essere realizzato tramite la proprietà `$fillable` del modello. Ad esempio, per consentire l'assegnazione in massa dell'attributo `name` del modello `Flight`:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributi che possono essere assegnati in massa.
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```

Una volta definiti gli attributi che possono essere assegnati in massa, è possibile inserire nuovi dati nel database utilizzando il metodo `create`. Questo metodo restituirà l'istanza del modello salvato:
```php
$flight = app\modle\Flight::create(['name' => 'Volo 10']);
```

Se si dispone già di un'istanza del modello, è possibile passare un array al metodo `fill` per assegnare i valori:
```php
$flight->fill(['name' => 'Volo 22']);
```

Il `$fillable` può essere considerato come una "lista bianca" per l'assegnazione di massa. È anche possibile utilizzare la proprietà `$guarded` per ottenere lo stesso risultato. La proprietà `$guarded` contiene un array di attributi che non possono essere assegnati in massa. In altre parole, `$guarded` funziona più o meno come una "lista nera". Nota: è possibile utilizzare solo una delle proprietà `$fillable` o `$guarded`, non entrambe contemporaneamente. Ad esempio, nell'esempio seguente, tutti gli attributi tranne `price` possono essere assegnati in massa:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * Attributi che non possono essere assegnati in massa.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

Se si desidera consentire l'assegnazione in massa di tutti gli attributi, è possibile definire `$guarded` come un array vuoto:
```php
/**
 * Attributi che non possono essere assegnati in massa.
 *
 * @var array
 */
protected $guarded = [];
```

## Altri metodi di creazione
firstOrCreate / firstOrNew
Ci sono due metodi che potresti utilizzare per l'assegnazione di massa: `firstOrCreate` e `firstOrNew`. Il metodo `firstOrCreate` cercherà nel database i dati corrispondenti alla coppia chiave/valore fornita. Se non trova un modello nel database, ne inserirà uno nuovo con i valori del primo parametro e, facoltativamente, del secondo parametro.

Il metodo `firstOrNew` funziona in modo simile al `firstOrCreate`, ma se non trova un modello corrispondente, restituirà una nuova istanza del modello. È importante notare che l'istanza restituita da `firstOrNew` non è ancora salvata nel database e richiederà una chiamata al metodo `save` per essere salvata:
```php
// Cerca un volo per nome, se non esiste, lo crea...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Volo 10']);

// Cerca un volo per nome, o crea un volo con l'attributo di ritardo e l'ora di arrivo specificati.
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Volo 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// Cerca un volo per nome, se non esiste, crea un'istanza...
$flight = app\modle\Flight::firstOrNew(['name' => 'Volo 10']);

// Cerca un volo per nome, o crea un'istanza con l'attributo di ritardo e l'ora di arrivo specificati...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Volo 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

Potresti anche incontrare situazioni in cui desideri aggiornare un modello esistente o, se non esiste, crearne uno nuovo. In tal caso, puoi utilizzare il metodo `updateOrCreate` per farlo in un'unica operazione. Simile a `firstOrCreate`, `updateOrCreate` persiste il modello, quindi non è necessario chiamare `save()`.
```php
// Se esiste un volo da Oakland a San Diego, imposta il prezzo a 99 dollari.
// Se nessun modello corrisponde, ne crea uno.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## Eliminazione del modello
È possibile chiamare il metodo `delete` sull'istanza del modello per eliminarla:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## Eliminazione del modello tramite chiave primaria
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## Eliminazione del modello tramite query
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## Copia del modello
Puoi utilizzare il metodo `replicate` per duplicare un'istanza del modello che non è ancora salvata nel database. Questo metodo è utile quando le istanze del modello condividono molte proprietà simili.
```php
$shipping = App\Address::create([
    'type' => 'spedizione',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'fatturazione'
]);

$billing->save();
```

## Confronto dei modelli
A volte potrebbe essere necessario verificare se due modelli sono "uguali". Il metodo `is` può essere utilizzato per verificare rapidamente se due modelli hanno la stessa chiave primaria, tabella e connessione al database:
```php
if ($post->is($anotherPost)) {
    //
}
```

## Osservatori del modello
Per ulteriori informazioni sugli eventi del modello e sugli osservatori, si prega di fare riferimento a [Eventi del modello e Osservatori in Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

Nota: Per abilitare gli osservatori del modello in Eloquent ORM, è necessario importare ulteriormente tramite composer require "illuminate/events".

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
