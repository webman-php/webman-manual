# Validatori
Composer offre molti validatori che possono essere utilizzati direttamente, ad esempio:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validatori top-think/think-validate

### Descrizione
Validatore ufficiale di ThinkPHP

### Indirizzo del progetto
https://github.com/top-think/think-validate

### Installazione
`composer require topthink/think-validate`

### Inizio veloce

**Nuovo file `app/index/validate/User.php`**

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
        'name.require' => 'Nome obbligatorio',
        'name.max'     => 'Il nome non può contenere più di 25 caratteri',
        'age.number'   => 'L\'età deve essere un numero',
        'age.between'  => 'L\'età deve essere compresa tra 1 e 120',
        'email'        => 'Formato email errato',    
    ];

}
```
  
**Utilizzo**
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
# Validatori workerman/validation

### Descrizione
Progetto per la versione cinese di https://github.com/Respect/Validation

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
            'nickname' => v::length(1, 64)->setName('Nickname'),
            'username' => v::alnum()->length(5, 64)->setName('Username'),
            'password' => v::length(5, 64)->setName('Password')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Accesso tramite jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Risultato ottenuto:

`{"code":500,"msg":"Username può contenere solo lettere (a-z) e numeri (0-9)"}`

Spiegazione:

`v::input(array $input, array $rules)` viene utilizzato per convalidare e raccogliere i dati. Se la convalida dei dati fallisce, viene generata un'eccezione `Respect\Validation\Exceptions\ValidationException`, in caso di successo verranno restituiti i dati convalidati (array).

Se il codice di business non cattura l'eccezione di convalida, il framework webman la catturerà automaticamente e restituirà i dati in formato JSON (simile a `{"code":500, "msg":"xxx"}`) o una pagina di eccezione normale in base all'intestazione della richiesta HTTP. Se il formato restituito non soddisfa i requisiti del business, lo sviluppatore può catturare manualmente l'eccezione `ValidationException` e restituire i dati desiderati, come nell'esempio seguente:

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
                'username' => v::alnum()->length(5, 64)->setName('Username'),
                'password' => v::length(5, 64)->setName('Password')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Guida alle funzionalità del Validator

```php
use Respect\Validation\Validator as v;

// Validazione di regola singola
$number = 123;
v::numericVal()->validate($number); // true

// Validazione di catene di regole multiple
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Ottenere il primo motivo di fallimento della validazione
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username può contenere solo lettere (a-z) e numeri (0-9)
}

// Ottenere tutti i motivi di fallimento della validazione
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Stampa
    // - Username deve essere conforme ai seguenti criteri
    //     - Username può contenere solo lettere (a-z) e numeri (0-9)
    //     - Username non può contenere spazi
  
    var_export($exception->getMessages());
    // Stampa
    // array (
    //   'alnum' => 'Username può contenere solo lettere (a-z) e numeri (0-9)',
    //   'noWhitespace' => 'Username non può contenere spazi',
    // )
}

// Personalizzazione dei messaggi di errore
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username può contenere solo lettere e numeri',
        'noWhitespace' => 'Username non può contenere spazi',
        'length' => 'La lunghezza è conforme alle regole, quindi questo messaggio non verrà visualizzato'
    ]);
    // Stampa 
    // array(
    //    'alnum' => 'Username può contenere solo lettere e numeri',
    //    'noWhitespace' => 'Username non può contenere spazi'
    // )
}

// Validazione degli oggetti
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validazione degli array
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
    ->assert($data); // Può anche essere utilizzato check() o validate()

// Validazione opzionale
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regola negativa
v::not(v::intVal())->validate(10); // false
```
  
### Differenze tra i tre metodi del Validator `validate()` `check()` `assert()`

`validate()` restituisce un booleano, non genera un'eccezione

`check()` genera un'eccezione in caso di fallimento della validazione, restituendo il motivo del primo fallimento tramite `$exception->getMessage()`

`assert()` genera un'eccezione in caso di fallimento della validazione, possibile ottenere tutti i motivi di fallimento tramite `$exception->getFullMessage()`
  
### Elenco delle regole di convalida comuni

`Alnum()` contiene solo lettere e numeri

`Alpha()` contiene solo lettere

`ArrayType()` tipo array

`Between(mixed $minimum, mixed $maximum)` verifica se l'input si trova tra due valori

`BoolType()` verifica se è di tipo booleano

`Contains(mixed $expectedValue)` verifica se l'input contiene determinati valori

`ContainsAny(array $needles)` verifica se l'input contiene almeno un valore definito

`Digit()` verifica se l'input contiene solo cifre

`Domain()` verifica se è un dominio valido

`Email()` verifica se è un'email valida

`Extension(string $extension)` verifica l'estensione del file

`FloatType()` verifica se è di tipo float

`IntType()` verifica se è un numero intero

`Ip()` verifica se è un indirizzo IP

`Json()` verifica se è un dato JSON

`Length(int $min, int $max)` verifica se la lunghezza è nell'intervallo specificato

`LessThan(mixed $compareTo)` verifica se la lunghezza è inferiore a un valore specificato

`Lowercase()` verifica se sono tutte lettere minuscole

`MacAddress()` verifica se è un indirizzo MAC

`NotEmpty()` verifica se non è vuoto

`NullType()` verifica se è nullo

`Number()` verifica se è un numero

`ObjectType()` verifica se è di tipo oggetto

`StringType()` verifica se è di tipo stringa

`Url()` verifica se è un URL
  
Per ulteriori regole di convalida, consultare https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### Ulteriori informazioni

Visita https://respect-validation.readthedocs.io/en/2.0/
