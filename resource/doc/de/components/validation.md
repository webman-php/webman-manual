# Validierer

Es gibt viele Validierer, die direkt verwendet werden können, z.B.:

#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validierer top-think/think-validate

### Beschreibung
Offizieller Validierer von ThinkPHP

### Projektadresse
https://github.com/top-think/think-validate

### Installation
`composer require topthink/think-validate`

### Schnellstart

**Erstellen von `app/index/validate/User.php`**

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
# Validierer  workerman/validation

### Beschreibung

Projekt ist die chinesische Version von https://github.com/Respect/Validation

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
  
**Durch jquery Zugriff**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'Tomkat', password: '123456'}
  });
  ```
  
Ergebnis:

`{"code":500,"msg":"Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten"}`

Erklärung:

`v::input(array $input, array $rules)` wird verwendet, um Daten zu validieren und zu sammeln. Bei ungültigen Daten wird eine `Respect\Validation\Exceptions\ValidationException`-Ausnahme ausgelöst, andernfalls wird das validierte Daten (Array) zurückgegeben.

Wenn die Validierungsausnahme nicht im Geschäftscode abgefangen wird, fängt das Webman-Framework automatisch die Validierungs-Ausnahme ein und gibt JSON-Daten zurück (ähnlich wie `{"code":500, "msg":"xxx"}`) oder eine normale Ausnahme-Seite, je nachdem was im HTTP-Request-Header angegeben ist. Wenn das Rückgabeformat nicht den Geschäftsanforderungen entspricht, kann der Entwickler die `ValidationException`-Ausnahme selbst abfangen und die erforderlichen Daten zurückgeben, ähnlich wie im folgenden Beispiel:

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

### Validator Funktionenübersicht

```php
use Respect\Validation\Validator as v;

// Einzelne Regelvalidierung
$number = 123;
v::numericVal()->validate($number); // true

// Mehrere Regeln verketten
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Den ersten Validierungsfehler erhalten
try {
    $usernameValidator->setName('Benutzername')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten
}

// Alle Validierungsfehler erhalten
try {
    $usernameValidator->setName('Benutzername')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Wird ausgeben
    // -  Benutzername muss folgende Regeln erfüllen
    //     - Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten
    //     - Benutzername darf keine Leerzeichen enthalten
  
    var_export($exception->getMessages());
    // Wird ausgeben
    // array (
    //   'alnum' => 'Benutzername darf nur Buchstaben (a-z) und Zahlen (0-9) enthalten',
    //   'noWhitespace' => 'Benutzername darf keine Leerzeichen enthalten',
    // )
}

// Benutzerdefinierte Fehlermeldung
try {
    $usernameValidator->setName('Benutzername')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Benutzername darf nur Buchstaben und Zahlen enthalten',
        'noWhitespace' => 'Benutzername darf keine Leerzeichen enthalten',
        'length' => 'Länge entspricht der Regel, daher wird diese Meldung nicht angezeigt'
    ]);
    // Wird ausgeben 
    // array(
    //    'alnum' => 'Benutzername darf nur Buchstaben und Zahlen enthalten',
    //    'noWhitespace' => 'Benutzername darf keine Leerzeichen enthalten'
    // )
}

// Objekt validieren
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
    ->assert($data); // Kann auch mit check() oder validate() verwendet werden
 
// Optionale Validierung
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negierte Regel
v::not(v::intVal())->validate(10); // false
```
  
### Validator Methode `validate()` `check()` `assert()` Unterschiede

`validate()` gibt einen Boolean zurück und wirft keine Ausnahme

`check()` wirft bei ungültiger Validierung eine Ausnahme und gibt die erste fehlgeschlagene Validierung mit `$exception->getMessage()` aus

`assert()` wirft bei ungültiger Validierung eine Ausnahme und mit `$exception->getFullMessage()` können alle fehlgeschlagenen Validierungen abgerufen werden
  
  
### Liste gängiger Validierungsregeln

`Alnum()` enthält nur Buchstaben und Zahlen

`Alpha()` enthält nur Buchstaben

`ArrayType()` Array Typ

`Between(mixed $minimum, mixed $maximum)` überprüft, ob der Eingabe zwischen zwei Werten liegt.

`BoolType()` überprüft, ob es sich um einen Bool-Typ handelt

`Contains(mixed $expectedValue)` überprüft, ob die Eingabe bestimmte Werte enthält

`ContainsAny(array $needles)` überprüft, ob die Eingabe mindestens einen definierten Wert enthält

`Digit()` überprüft, ob die Eingabe nur Ziffern enthält

`Domain()` überprüft, ob es sich um eine gültige Domain handelt

`Email()` überprüft, ob es sich um eine gültige E-Mail-Adresse handelt

`Extension(string $extension)` überprüft die Dateierweiterung

`FloatType()` überprüft, ob es sich um einen Fließkommazahl-Typ handelt

`IntType()` überprüft, ob es sich um einen Integer-Typ handelt

`Ip()` überprüft, ob es sich um eine IP-Adresse handelt

`Json()` überprüft, ob es sich um JSON-Daten handelt

`Length(int $min, int $max)` überprüft die Länge im angegebenen Bereich

`LessThan(mixed $compareTo)` überprüft, ob die Länge kleiner als ein bestimmter Wert ist

`Lowercase()` überprüft, ob es sich um Kleinbuchstaben handelt

`MacAddress()` überprüft, ob es sich um eine MAC-Adresse handelt

`NotEmpty()` überprüft, ob nicht leer ist

`NullType()` überprüft, ob es sich um Null handelt

`Number()` überprüft, ob es sich um eine Nummer handelt

`ObjectType()` überprüft, ob es sich um einen Objekt-Typ handelt

`StringType()` überprüft, ob es sich um einen String-Typ handelt

`Url()` überprüft, ob es sich um eine URL handelt
  
Weitere Validierungsregeln finden Sie unter https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### Weitere Informationen

Besuchen Sie https://respect-validation.readthedocs.io/en/2.0/
