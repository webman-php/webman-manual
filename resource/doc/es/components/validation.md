# Validador

Composer tiene una gran cantidad de validadores que se pueden utilizar directamente, como por ejemplo:

#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validador top-think/think-validate

### Descripción
Validador oficial de ThinkPHP

### Dirección del proyecto
https://github.com/top-think/think-validate

### Instalación
`composer require topthink/think-validate`

### Comienzo rápido

**Crear `app/index/validate/User.php`**

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
        'name.require' => 'El nombre es obligatorio',
        'name.max'     => 'El nombre no puede tener más de 25 caracteres',
        'age.number'   => 'La edad debe ser numérica',
        'age.between'  => 'La edad debe estar entre 1 y 120',
        'email'        => 'Formato de correo electrónico incorrecto',    
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
# Validador workerman/validation

### Descripción

Proyecto para la versión en chino simplificado de https://github.com/Respect/Validation

### Dirección del proyecto

https://github.com/walkor/validation
  
  
### Instalación
 
```php
composer require workerman/validation
```

### Comienzo rápido

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
            'nickname' => v::length(1, 64)->setName('Apodo'),
            'username' => v::alnum()->length(5, 64)->setName('Nombre de usuario'),
            'password' => v::length(5, 64)->setName('Contraseña')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Acceso a través de jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Obtención de resultado:

`{"code":500,"msg":"El nombre de usuario solo puede contener letras (a-z) y números (0-9)"}`

Descripción:

`v::input(array $input, array $rules)` se utiliza para validar y recopilar datos. Si la validación de los datos falla, se lanzará la excepción `Respect\Validation\Exceptions\ValidationException`, en caso de éxito, se devolverán los datos validados (array).

Si el código de negocio no captura la excepción de validación, el marco webman la capturará automáticamente y, según la cabecera de la solicitud HTTP, elegirá devolver los datos en formato JSON (similar a `{"code":500, "msg":"xxx"}`) o una página de excepción normal. Si el formato de devolución no cumple con los requisitos del negocio, el desarrollador puede capturar manualmente la excepción `ValidationException` y devolver los datos necesarios, similar al siguiente ejemplo:

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
                'username' => v::alnum()->length(5, 64)->setName('Nombre de usuario'),
                'password' => v::length(5, 64)->setName('Contraseña')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Guía de funciones del Validador

```php
use Respect\Validation\Validator as v;

// Validación de una regla única
$number = 123;
v::numericVal()->validate($number); // true

// Validación encadenada de múltiples reglas
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obtener la primera razón de validación fallida
try {
    $usernameValidator->setName('Nombre de usuario')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // El nombre de usuario solo puede contener letras (a-z) y números (0-9)
}

// Obtener todas las razones de validación fallida
try {
    $usernameValidator->setName('Nombre de usuario')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Imprimirá
    // -  Nombre de usuario debe cumplir con las siguientes reglas
    //     - El nombre de usuario solo puede contener letras (a-z) y números (0-9)
    //     - El nombre de usuario no puede contener espacios en blanco
  
    var_export($exception->getMessages());
    // Imprimirá
    // array (
    //   'alnum' => 'El nombre de usuario solo puede contener letras (a-z) y números (0-9)',
    //   'noWhitespace' => 'El nombre de usuario no puede contener espacios en blanco',
    // )
}

// Mensajes de error personalizados
try {
    $usernameValidator->setName('Nombre de usuario')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'El nombre de usuario solo puede contener letras y números',
        'noWhitespace' => 'El nombre de usuario no puede contener espacios en blanco',
        'length' => 'La longitud cumple con las reglas, por lo que esta no se mostrará'
    ]);
    // Imprimirá 
    // array(
    //    'alnum' => 'El nombre de usuario solo puede contener letras y números',
    //    'noWhitespace' => 'El nombre de usuario no puede contener espacios en blanco'
    // )
}

// Validación de objeto
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validación de array
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
    ->assert($data); // también se puede usar check() o validate()
  
// Validación opcional
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Reglas negadas
v::not(v::intVal())->validate(10); // false
```
  
### Diferencias entre los métodos `validate()`, `check()` y `assert()` del Validador

`validate()` devuelve un booleano y no lanza una excepción

`check()` lanza una excepción al fallar la validación, mostrando la primera razón de validación fallida a través de `$exception->getMessage()`

`assert()` lanza una excepción al fallar la validación, mostrando todas las razones de validación fallida a través de `$exception->getFullMessage()`
  
### Lista de reglas de validación comunes

`Alnum()` sólo contiene letras y números

`Alpha()` sólo contiene letras

`ArrayType()` tipo de array

`Between(mixed $minimum, mixed $maximum)` valida si la entrada está entre dos valores

`BoolType()` valida si es un booleano

`Contains(mixed $expectedValue)` valida si la entrada contiene ciertos valores

`ContainsAny(array $needles)` valida si la entrada contiene al menos uno de los valores definidos

`Digit()` valida si la entrada sólo contiene dígitos

`Domain()` valida si es un dominio válido

`Email()` valida si es una dirección de correo electrónico válida

`Extension(string $extension)` valida la extensión del nombre de archivo

`FloatType()` valida si es un número decimal

`IntType()` valida si es un número entero

`Ip()` valida si es una dirección IP

`Json()` valida si es un dato en formato JSON

`Length(int $min, int $max)` valida si la longitud está en el intervalo dado

`LessThan(mixed $compareTo)` valida si la longitud es menor que un valor dado

`Lowercase()` valida si son letras minúsculas

`MacAddress()` valida si es una dirección MAC

`NotEmpty()` valida si no está vacío

`NullType()` valida si es nulo

`Number()` valida si es un número

`ObjectType()` valida si es un objeto

`StringType()` valida si es una cadena de texto

`Url()` valida si es una URL
  
Para más reglas de validación, consultar https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
### Más información

Visitar https://respect-validation.readthedocs.io/en/2.0/
