# Validierer
Es gibt viele Validierer, die direkt in Composer verwendet werden können, zum Beispiel:

#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validator top-think/think-validate

### Beschreibung
Offizieller Validator von ThinkPHP

### Projektadresse
https://github.com/top-think/think-validate

### Installation
`composer require topthink/think-validate`

### Schnellstart

**Erstellen Sie `app/index/validate/User.php`**

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
        'name.require' => 'Name ist erforderlich',
        'name.max'     => 'Name darf maximal 25 Zeichen lang sein',
        'age.number'   => 'Alter muss eine Zahl sein',
        'age.between'  => 'Alter muss zwischen 1 und 120 liegen',
        'email'        => 'Ungültiges E-Mail-Format',    
    ];

}
```  

**Verwendung**
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

# Validator workerman/validation

### Beschreibung
Projekt ist die chinesischsprachige Version von https://github.com/Respect/Validation

### Projektadresse
https://github.com/walkor/validation

### Installation
```php
composer require workerman/validation
```

### Schnellstart

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
            'nickname' => v::length(1, 64)->setName('Spitzname'),
            'username' => v::alnum()->length(5, 64)->setName('Benutzername'),
            'password' => v::length(5, 64)->setName('Passwort')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
**Über jquery zugreifen**
```js
$.ajax({
    url : 'http://127.0.0.1:8787',
    type : "post",
    dataType:'json',
    data : {nickname:'Tom', username:'tom cat', password: '123456'}
});
```
Ergebnis erhalten:

`{"code":500,"msg":"Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten"}`

Erklärung:

`v::input(array $input, array $rules)` wird verwendet, um Daten zu validieren und zu sammeln. Wenn die Datenvalidierung fehlschlägt, wird eine Ausnahme `Respect\Validation\Exceptions\ValidationException` geworfen. Bei erfolgreicher Validierung werden die validierten Daten (Array) zurückgegeben.

Wenn der Geschäftscode die Validierungsausnahme nicht abfängt, fängt das Webman-Framework automatisch die Ausnahme ab und gibt basierend auf dem HTTP-Anforderungsheader JSON-Daten zurück (ähnlich wie `{"code":500, "msg":"xxx"}`) oder eine normale Fehlerseite. Wenn das Rückgabeformat nicht den Geschäftsanforderungen entspricht, kann der Entwickler die Ausnahme `ValidationException` selbst abfangen und die benötigten Daten zurückgeben, ähnlich wie im folgenden Beispiel:

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
                'username' => v::alnum()->length(5, 64)->setName('Benutzername'),
                'password' => v::length(5, 64)->setName('Passwort')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```
### Validator Funktionsleitfaden
```php
use Respect\Validation\Validator as v;

// Einzelne Regelvalidierung
$number = 123;
v::numericVal()->validate($number); // true

// Mehrere regelkettenvalidierung
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Erhalten des ersten fehlgeschlagenen Validierungsgrundes
try {
    $usernameValidator->setName('Benutzername')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten
}

// Erhalten aller fehlgeschlagenen Validierungsgründe
try {
    $usernameValidator->setName('Benutzername')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Hier werden
    // - Benutzername muss folgenden Regeln entsprechen
    //     - Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten
    //     - Benutzername darf keine Leerzeichen enthalten
  
    var_export($exception->getMessages());
    // Ausgabe
    // array (
    //   'alnum' => 'Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten',
    //   'noWhitespace' => 'Benutzername darf keine Leerzeichen enthalten',
    // )
}

// Anpassung von Fehlermeldungen
try {
    $usernameValidator->setName('Benutzername')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Benutzername darf nur Buchstaben und Zahlen enthalten',
        'noWhitespace' => 'Benutzername darf keine Leerzeichen enthalten',
        'length' => 'Länge entspricht den Regeln, daher wird diese Meldung nicht angezeigt'
    ]);
    // Ausgabe
    // array(
    //    'alnum' => 'Benutzername darf nur Buchstaben und Zahlen enthalten',
    //    'noWhitespace' => 'Benutzername darf keine Leerzeichen enthalten'
    // )
}

// Objektvalidierung
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Array validieren
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
    ->assert($data); // auch mit check() oder validate()
  
// Optionale Validierung
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negativregel
v::not(v::intVal())->validate(10); // false
```

### Validator Unterschied zwischen den drei Methoden `validate()` `check()` `assert()`

`validate()` gibt einen booleschen Wert zurück und wirft keine Ausnahme

`check()` wirft eine Ausnahme, wenn die Validierung fehlschlägt, gibt die erste Validierungsfehlergrund zurück

`assert()` wirft eine Ausnahme, wenn die Validierung fehlschlägt, gibt alle Validierungsfehlergründe zurück

### Liste der häufig verwendeten Validierungsregeln

`Alnum()` enthält nur Buchstaben und Zahlen

`Alpha()` enthält nur Buchstaben

`ArrayType()` Array-Typ

`Between(mixed $minimum, mixed $maximum)` überprüft, ob die Eingabe zwischen zwei Werten liegt

`BoolType()` überprüft, ob es sich um einen Booleschen Wert handelt

`Contains(mixed $expectedValue)` überprüft, ob die Eingabe bestimmte Werte enthält

`ContainsAny(array $needles)` überprüft, ob die Eingabe mindestens einen bestimmten Wert enthält

`Digit()` überprüft, ob die Eingabe nur Zahlen enthält

`Domain()` überprüft, ob es sich um eine gültige Domain handelt

`Email()` überprüft, ob es sich um eine gültige E-Mail-Adresse handelt

`Extension(string $extension)` überprüft die Dateierweiterung

`FloatType()` überprüft, ob es sich um eine Gleitkommazahl handelt

`IntType()` überprüft, ob es sich um eine ganze Zahl handelt

`Ip()` überprüft, ob es sich um eine IP-Adresse handelt

`Json()` überprüft, ob es sich um JSON-Daten handelt

`Length(int $min, int $max)` überprüft, ob die Länge innerhalb des angegebenen Bereichs liegt

`LessThan(mixed $compareTo)` überprüft, ob die Länge kleiner als der angegebene Wert ist

`Lowercase()` überprüft, ob es sich um Kleinbuchstaben handelt

`MacAddress()` überprüft, ob es sich um eine MAC-Adresse handelt

`NotEmpty()` überprüft, ob es nicht leer ist

`NullType()` überprüft, ob es sich um `null` handelt

`Number()` überprüft, ob es sich um eine Nummer handelt

`ObjectType()` überprüft, ob es sich um ein Objekt handelt

`StringType()` überprüft, ob es sich um einen Zeichenfolgentyp handelt

`Url()` überprüft, ob es sich um eine URL handelt
  
Weitere Validierungsregeln finden Sie unter https://respect-validation.readthedocs.io/en/2.0/list-of-rules/

### Weitere Informationen

Besuchen Sie https://respect-validation.readthedocs.io/en/2.0/
