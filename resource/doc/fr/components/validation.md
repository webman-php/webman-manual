# Validateur

Il existe de nombreux validateurs dans Composer qui peuvent être utilisés directement, par exemple :

#### [top-think/think-validate](#think-validate)
#### [respect/validation](#respect-validation)

<a name="think-validate"></a>
## Validateur top-think/think-validate

### Description
Validateur officiel de ThinkPHP

### Adresse du projet
https://github.com/top-think/think-validate

### Installation
`composer require topthink/think-validate`

### Démarrage rapide

**Créer `app/index/validate/User.php`**

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
        'name.require' => 'Le nom est obligatoire',
        'name.max'     => 'Le nom ne peut pas dépasser 25 caractères',
        'age.number'   => 'L'âge doit être un nombre',
        'age.between'  => 'L'âge doit être compris entre 1 et 120',
        'email'        => 'Format email incorrect',    
    ];

}
```
  
**Utilisation**
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
## Valideur de workerman/validation

### Description

Projet pour la version chinoise de https://github.com/Respect/Validation

### Adresse du projet

https://github.com/walkor/validation
  
  
### Installation
 
```php
composer require workerman/validation
```

### Démarrage rapide

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
            'nickname' => v::length(1, 64)->setName('surnom'),
            'username' => v::alnum()->length(5, 64)->setName('nom d utilisateur'),
            'password' => v::length(5, 64)->setName('mot de passe')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Accès via jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'汤姆', username:'tom cat', password: '123456'}
  });
  ```
  
Résultat :

`{"code":500,"msg":"Le nom d utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)"}`

Explication :

`v::input(array $input, array $rules)` sert à valider et collecter les données. En cas d'échec de la validation des données, une exception `Respect\Validation\Exceptions\ValidationException` est levée. En cas de succès de la validation, les données validées (sous forme de tableau) seront retournées.

Si le code métier ne capture pas l'exception de validation, le cadre webman capturera automatiquement et sélectionnera le format de retour des données JSON (similaire à `{"code":500, "msg":"xxx"}`) ou une page d'exception normale en fonction de l'en-tête de la requête HTTP. Si le format de retour ne correspond pas aux besoins du code métier, les développeurs peuvent eux-mêmes capturer l'exception `ValidationException` et retourner les données nécessaires, comme dans l'exemple ci-dessous :

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
                'username' => v::alnum()->length(5, 64)->setName('nom d utilisateur'),
                'password' => v::length(5, 64)->setName('mot de passe')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Guide des fonctionnalités du validateur

```php
use Respect\Validation\Validator as v;

// Validation d'une seule règle
$number = 123;
v::numericVal()->validate($number); // true

// Validation en chaîne de plusieurs règles
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obtenir la première raison de l'échec de la validation
try {
    $usernameValidator->setName('nom d utilisateur')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Le nom d utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)
}

// Obtenir toutes les raisons de l'échec de la validation
try {
    $usernameValidator->setName('nom d utilisateur')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Affichera
    // -  Le nom d utilisateur doit respecter les règles suivantes
    //     - Le nom d utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)
    //     - Le nom d utilisateur ne peut pas contenir d'espaces
  
    var_export($exception->getMessages());
    // Affichera
    // array (
    //   'alnum' => 'Le nom d utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)',
    //   'noWhitespace' => 'Le nom d utilisateur ne peut pas contenir d\'espaces',
    // )
}

// Personnaliser les messages d'erreur
try {
    $usernameValidator->setName('nom d utilisateur')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Le nom d utilisateur ne peut contenir que des lettres et des chiffres',
        'noWhitespace' => 'Le nom d utilisateur ne peut pas contenir d\'espaces',
        'length' => 'length respecte la règle, donc ce message ne sera pas affiché'
    ]);
    // Affichera
    // array(
    //    'alnum' => 'Le nom d utilisateur ne peut contenir que des lettres et des chiffres',
    //    'noWhitespace' => 'Le nom d utilisateur ne peut pas contenir d\'espaces'
    // )
}

// Validation d'objet
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validation de tableau
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
    ->assert($data); // peut également utiliser check() ou validate()
  
// Validation facultative
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Règle négative
v::not(v::intVal())->validate(10); // false
```
  
### Différence entre les méthodes `validate()`, `check()` et `assert()`

`validate()` renvoie un booléen et ne lève pas d'exception

`check()` lève une exception en cas d'échec de la validation, et utilise `$exception->getMessage()` pour obtenir la première raison de l'échec de la validation

`assert()` lève une exception en cas d'échec de la validation, et utilise `$exception->getFullMessage()` pour obtenir toutes les raisons de l'échec de la validation
  
### Liste des règles de validation courantes

`Alnum()` ne contient que des lettres et des chiffres

`Alpha()` ne contient que des lettres

`ArrayType()` type de tableau

`Between(mixed $minimum, mixed $maximum)` vérifie si l'entrée se situe entre deux valeurs.

`BoolType()` vérifie s'il est de type booléen

`Contains(mixed $expectedValue)` vérifie si l'entrée contient certaines valeurs

`ContainsAny(array $needles)` vérifie si l'entrée contient au moins une valeur définie

`Digit()` vérifie si l'entrée contient uniquement des chiffres

`Domain()` vérifie s'il s'agit d'un domaine valide

`Email()` vérifie s'il s'agit d'une adresse e-mail valide

`Extension(string $extension)` vérifie l'extension

`FloatType()` vérifie s'il s'agit d'un nombre à virgule flottante

`IntType()` vérifie s'il s'agit d'un entier

`Ip()` vérifie s'il s'agit d'une adresse IP

`Json()` vérifie s'il s'agit de données JSON

`Length(int $min, int $max)` vérifie si la longueur est dans l'intervalle donné

`LessThan(mixed $compareTo)` vérifie si la longueur est inférieure à une valeur donnée

`Lowercase()` vérifie si cela contient uniquement des lettres minuscules

`MacAddress()` vérifie s'il s'agit d'une adresse MAC

`NotEmpty()` vérifie si ce n'est pas vide

`NullType()` vérifie s'il s'agit de null

`Number()` vérifie s'il s'agit d'un nombre

`ObjectType()` vérifie s'il s'agit d'un objet

`StringType()` vérifie s'il s'agit d'une chaîne de caractères

`Url()` vérifie s'il s'agit d'une URL
  
Pour plus de règles de validation, voir https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
### Plus d'informations

Visitez https://respect-validation.readthedocs.io/en/2.0/
