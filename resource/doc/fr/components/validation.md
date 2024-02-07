# Validateur
Il existe de nombreux validateurs disponibles directement pour une utilisation dans Composer, tels que :
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

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
        'age.number'   => 'L\'âge doit être un nombre',
        'age.between'  => 'L\'âge doit être compris entre 1 et 120',
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
# Validateur workerman/validation

### Description
Projet chinois basé sur https://github.com/Respect/Validation

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
            'nickname' => v::length(1, 64)->setName('Surnom'),
            'username' => v::alnum()->length(5, 64)->setName('Nom d\'utilisateur'),
            'password' => v::length(5, 64)->setName('Mot de passe')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```  

**Accès via jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'汤姆', username:'tom cat', password: '123456'}
  });
  ```  

Résultat obtenu :

`{"code":500,"msg":"Le nom d'utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)"}`

Remarque :

`v::input(array $input, array $rules)` est utilisé pour valider et collecter des données. En cas d'échec de la validation, une exception `Respect\Validation\Exceptions\ValidationException` est levée, et en cas de succès, les données validées (sous forme d'un tableau) sont renvoyées.

Si le code métier ne capture pas l'exception de validation, le cadre webman la capturera automatiquement et renverra des données au format JSON (similaire à `{"code":500, "msg":"xxx"}`) ou une page d'exception normale en fonction de l'en-tête de la requête HTTP. Si le format de retour ne correspond pas aux besoins métier, le développeur peut capturer l'exception `ValidationException` et renvoyer les données nécessaires, comme dans l'exemple suivant :

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
                'username' => v::alnum()->length(5, 64)->setName('Nom d\'utilisateur'),
                'password' => v::length(5, 64)->setName('Mot de passe')
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
    $usernameValidator->setName('Nom d\'utilisateur')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Le nom d'utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)
}

// Obtenir toutes les raisons de l'échec de la validation
try {
    $usernameValidator->setName('Nom d\'utilisateur')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Cela affichera
    // -  Le nom d'utilisateur doit être conforme aux règles suivantes
    //     - Le nom d'utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)
    //     - Le nom d'utilisateur ne peut pas contenir d'espaces
   
    var_export($exception->getMessages());
    // Cela affichera
    // array (
    //   'alnum' => 'Le nom d'utilisateur ne peut contenir que des lettres (a-z) et des chiffres (0-9)',
    //   'noWhitespace' => 'Le nom d'utilisateur ne peut pas contenir d\'espaces',
    // )
}

// Personnaliser les messages d'erreur
try {
    $usernameValidator->setName('Nom d\'utilisateur')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Le nom d\'utilisateur ne peut contenir que des lettres et des chiffres',
        'noWhitespace' => 'Le nom d\'utilisateur ne peut pas contenir d\'espaces',
        'length' => 'length respecte la règle, donc ce message ne sera pas affiché'
    ]);
    // Cela affichera 
    // array(
    //    'alnum' => 'Le nom d\'utilisateur ne peut contenir que des lettres et des chiffres',
    //    'noWhitespace' => 'Le nom d\'utilisateur ne peut pas contenir d\'espaces'
    // )
}

// Validation d'un objet
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validation d'un tableau
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
    ->assert($data); // Vous pouvez également utiliser check() ou validate()
  
// Validation facultative
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Règle de négation
v::not(v::intVal())->validate(10); // false
```  

### Différence entre les méthodes `validate()`, `check()` et `assert()` du validateur

`validate()` renvoie un booléen et ne lance pas d'exception

`check()` lève une exception en cas d'échec de la validation, avec la raison du premier échec de validation via `$exception->getMessage()`

`assert()` lève une exception en cas d'échec de la validation, avec la liste complète des raisons d'échec de validation via `$exception->getFullMessage()`
  
### Liste des règles de validation couramment utilisées

`Alnum()` contient uniquement des lettres et des chiffres

`Alpha()` contient uniquement des lettres

`ArrayType()` type tableau

`Between(mixed $minimum, mixed $maximum)` valider si l'entrée est entre deux valeurs. 

`BoolType()` valider si c'est un booléen

`Contains(mixed $expectedValue)` valider si l'entrée contient certaines valeurs

`ContainsAny(array $needles)` valider si l'entrée contient au moins une valeur définie

`Digit()` valider si l'entrée ne contient que des chiffres

`Domain()` valider si c'est un domaine valide

`Email()` valider si c'est une adresse e-mail valide

`Extension(string $extension)` valider l'extension de fichier

`FloatType()` valider si c'est un nombre à virgule flottante

`IntType()` valider si c'est un entier

`Ip()` valider si c'est une adresse IP

`Json()` valider si c'est des données au format JSON

`Length(int $min, int $max)` valider si la longueur est dans l'intervalle donné

`LessThan(mixed $compareTo)` valider si la longueur est inférieure à une valeur donnée

`Lowercase()` valider si les lettres sont en minuscule

`MacAddress()` valider si c'est une adresse MAC

`NotEmpty()` valider si ce n'est pas vide

`NullType()` valider si c'est nul

`Number()` valider si c'est un nombre

`ObjectType()` valider si c'est un objet

`StringType()` valider si c'est une chaîne de caractères

`Url()` valider si c'est une URL
  
Pour plus de règles de validation, voir https://respect-validation.readthedocs.io/en/2.0/list-of-rules/

### Plus de contenu

Visitez https://respect-validation.readthedocs.io/en/2.0/
