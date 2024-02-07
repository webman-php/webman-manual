# Validatore
Composer offre molti validatori che possono essere utilizzati direttamente, ad esempio:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validatore top-think/think-validate

### Descrizione
Validatore ufficiale di ThinkPHP

### Indirizzo del progetto
https://github.com/top-think/think-validate

### Installazione
`composer require topthink/think-validate`

### Inizio veloce

**Crea un nuovo file `app/index/validate/User.php`**

```php
<?php 
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];

    protected $message  =   [
        'name.require' => 'Il nome è obbligatorio',
        'name.max'     => 'Il nome non può superare i 25 caratteri',
        'age.number'   => 'L\'età deve essere un numero',
        'age.between'  => 'L\'età deve essere compresa tra 1 e 120',
        'email'        => 'Formato email errato',    
    ];

}
```

**Uso**
```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

<a name="respect-validation"></a>
# Validatore workerman/validation

### Descrizione

Progetto per la versione in cinese semplificato https://github.com/Respect/Validation

### Indirizzo del progetto

https://github.com/walkor/validation

### Installazione

```php
composer require workerman/validation
```

### Inizio veloce

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        $data = v::input($request->post(), [
            'nickname' => v::length(1, 64)->setName('Nick'),
            'username' => v::alnum()->length(5, 64)->setName('Nome utente'),
            'password' => v::length(5, 64)->setName('Password')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```

**Accedi tramite jquery**
 
 ```js
 $.ajax({
     url: 'http://127.0.0.1:8787',
     type: "post",
     dataType: 'json',
     data: {nickname: 'Tom', username: 'tom cat', password: '123456'}
 });
 ```

Risultato:

`{"code":500,"msg":"Nome utente può contenere solo lettere (a-z) e numeri (0-9)"}`

Spiegazione:

`v::input(array $input, array $rules)` è utilizzato per convalidare e raccogliere dati. Se la convalida dei dati fallisce, verrà generata un'eccezione `Respect\Validation\Exceptions\ValidationException`, altrimenti verranno restituiti i dati convalidati (array).

Se il codice di business non cattura l'eccezione di convalida, il framework webman catturerà automaticamente l'eccezione e restituirà dati JSON (simile a `{"code":500, "msg":"xxx"}`) o una pagina di errore normale in base all'intestazione della richiesta HTTP. Se il formato di risposta non soddisfa i requisiti del business, lo sviluppatore può gestire l'eccezione `ValidationException` e restituire i dati richiesti personalizzati, come nell'esempio seguente:

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class IndexController
{
    public function index(Request $request)
    {
        try {
            $data = v::input($request->post(), [
                'username' => v::alnum()->length(5, 64)->setName('Nome utente'),
                'password' => v::length(5, 64)->setName('Password')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Guida alle funzionalità del Validatore

```php
use Respect\Validation\Validator as v;

// Convalida di una singola regola
$number = 123;
v::numericVal()->validate($number); // true

// Convalida di più regole in catena
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Ottenere la prima causa di convalida fallita
try {
    $usernameValidator->setName('Nome utente')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Nome utente può contenere solo lettere (a-z) e numeri (0-9)
}

// Ottenere tutte le cause di convalida fallita
try {
    $usernameValidator->setName('Nome utente')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Stampa
    // - Il nome utente deve soddisfare le seguenti regole
    //     - Nome utente può contenere solo lettere (a-z) e numeri (0-9)
    //     - Nome utente non può contenere spazi
  
    var_export($exception->getMessages());
    // Stampa
    // array (
    //   'alnum' => 'Nome utente può contenere solo lettere (a-z) e numeri (0-9)',
    //   'noWhitespace' => 'Nome utente non può contenere spazi',
    // )
}

// Messaggi di errore personalizzati
try {
    $usernameValidator->setName('Nome utente')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Il nome utente può contenere solo lettere e numeri',
        'noWhitespace' => 'Il nome utente non può contenere spazi',
        'length' => 'La lunghezza è valida. Questo non sarà visualizzato'
    ]);
    // Stampa 
    // array(
    //    'alnum' => 'Il nome utente può contenere solo lettere e numeri',
    //    'noWhitespace' => 'Il nome utente non può contenere spazi'
    // )
}

// Convalida dell'oggetto
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Convalida di un array
$data = [
    'parentKey' => [
        'field1' => 'value1',
        'field2' => 'value2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // Può anche utilizzare check() o validate()
  
// Convalida facoltativa
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regole di negazione
v::not(v::intVal())->validate(10); // false
```

### Differenza tra i tre metodi del Validatore `validate()` `check()` `assert()`

`validate()` restituisce un booleano, non genera un'eccezione

`check()` genera un'eccezione in caso di convalida fallita, restituendo la prima causa di convalida fallita attraverso `$exception->getMessage()`

`assert()` genera un'eccezione in caso di convalida fallita, restituendo tutte le cause di convalida fallita attraverso `$exception->getFullMessage()`

### Elenco delle regole di convalida comuni

`Alnum()` contiene solo lettere e numeri

`Alpha()` contiene solo lettere

`ArrayType()` tipo di array

`Between(mixed $minimum, mixed $maximum)` convalida se l'input si trova tra due valori specificati.

`BoolType()` convalida se è di tipo booleano

`Contains(mixed $expectedValue)` convalida se l'input contiene qualche valore

`ContainsAny(array $needles)` convalida se l'input contiene almeno uno dei valori definiti

`Digit()` convalida se l'input contiene solo cifre

`Domain()` convalida se è un dominio valido

`Email()` convalida se è un indirizzo email valido

`Extension(string $extension)` convalida l'estensione del file

`FloatType()` convalida se è di tipo float

`IntType()` convalida se è un numero intero

`Ip()` convalida se è un indirizzo IP

`Json()` convalida se è un dato JSON

`Length(int $min, int $max)` convalida la lunghezza all'interno dell'intervallo specificato

`LessThan(mixed $compareTo)` convalida se la lunghezza è inferiore al valore specificato

`Lowercase()` convalida se sono presenti solo lettere minuscole

`MacAddress()` convalida se è un indirizzo MAC

`NotEmpty()` convalida se non è vuoto

`NullType()` convalida se è nullo

`Number()` convalida se è un numero

`ObjectType()` convalida se è un oggetto

`StringType()` convalida se è di tipo stringa

`Url()` convalida se è un URL
  
Per ulteriori regole di convalida, visitare https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### Ulteriori dettagli

Visita https://respect-validation.readthedocs.io/en/2.0/
