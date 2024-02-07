# Validador
Hay muchos validadores disponibles en Composer que se pueden utilizar directamente, como por ejemplo:

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

**Cree `app/index/validate/User.php`**

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
        'age.number'   => 'La edad debe ser un número',
        'age.between'  => 'La edad solo puede estar entre 1 y 120',
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


# Validador workerman/validation

### Descripción

Proyecto como una versión traducida de https://github.com/Respect/Validation

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
            'nickname' => v::length(1, 64)->setName('apodo'),
            'username' => v::alnum()->length(5, 64)->setName('nombre de usuario'),
            'password' => v::length(5, 64)->setName('contraseña')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Acceso a través de jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Resultado:

`{"code":500,"msg":"El nombre de usuario solo puede contener letras (a-z) y números (0-9)"}`

Explicación:

`v::input(array $input, array $rules)` se utiliza para validar y recopilar datos. Si la validación falla, se genera una excepción `Respect\Validation\Exceptions\ValidationException`, de lo contrario, devuelve los datos validados (un array). 

Si el código del negocio no captura la excepción de validación, el marco de trabajo webman la capturará automáticamente y devolverá datos en formato JSON (similar a `{"code":500, "msg":"xxx"}`) o una página de excepción común según el encabezado de la solicitud HTTP. Si el formato de retorno no cumple con las necesidades del negocio, el desarrollador puede capturar manualmente la excepción `ValidationException` y devolver los datos necesarios, como en este ejemplo:

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
                'username' => v::alnum()->length(5, 64)->setName('nombre de usuario'),
                'password' => v::length(5, 64)->setName('contraseña')
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

// Validación de una sola regla
$number = 123;
v::numericVal()->validate($number); // true

// Validación en cadena de varias reglas
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obtener la primera razón de fallo de validación
try {
    $usernameValidator->setName('nombre de usuario')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // El nombre de usuario solo puede contener letras (a-z) y números (0-9)
}

// Obtener todas las razones de fallo de validación
try {
    $usernameValidator->setName('nombre de usuario')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Imprimirá
    // -  El nombre de usuario debe cumplir con las siguientes reglas
    //    - El nombre de usuario solo puede contener letras (a-z) y números (0-9)
    //    - El nombre de usuario no puede contener espacios
  
    var_export($exception->getMessages());
    // Imprimirá
    // array (
    //   'alnum' => 'El nombre de usuario solo puede contener letras (a-z) y números (0-9)',
    //   'noWhitespace' => 'El nombre de usuario no puede contener espacios',
    // )
}

// Mensaje de error personalizado
try {
    $usernameValidator->setName('nombre de usuario')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'El nombre de usuario solo puede contener letras y números',
        'noWhitespace' => 'El nombre de usuario no puede contener espacios',
        'length' => 'length se ajusta a la regla, por lo que esta no se mostrará'
    ]);
    // Imprimirá 
    // array(
    //    'alnum' => 'El nombre de usuario solo puede contener letras y números',
    //    'noWhitespace' => 'El nombre de usuario no puede contener espacios'
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
    ->assert($data); // También se puede usar check() o validate()
  
// Validación opcional
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regla de negación
v::not(v::intVal())->validate(10); // false
```


### Diferencia entre los tres métodos del Validador `validate()`, `check()`, `assert()`

`validate()` devuelve un valor booleano y no genera una excepción

`check()` genera una excepción en caso de error de validación y devuelve el motivo del primer error a través de `$exception->getMessage()`

`assert()` genera una excepción en caso de error de validación y a través de `$exception->getFullMessage()` se pueden obtener todos los motivos de error de validación


### Lista de reglas de validación comunes

`Alnum()` contiene solo letras y números

`Alpha()` contiene solo letras

`ArrayType()` tipo array

`Between(mixed $minimum, mixed $maximum)` verifica si el valor está entre dos valores

`BoolType()` verifica si es de tipo booleano

`Contains(mixed $expectedValue)` verifica si el valor contiene ciertos valores

`ContainsAny(array $needles)` verifica si el valor contiene al menos uno de los valores definidos

`Digit()` verifica si el valor contiene solo dígitos

`Domain()` verifica si es un dominio válido

`Email()` verifica si es una dirección de correo electrónico válida

`Extension(string $extension)` verifica la extensión

`FloatType()` verifica si es de tipo flotante

`IntType()` verifica si es un entero

`Ip()` verifica si es una dirección IP

`Json()` verifica si es un dato JSON

`Length(int $min, int $max)` verifica si la longitud está en el rango dado

`LessThan(mixed $compareTo)` verifica si la longitud es menor que el valor dado

`Lowercase()` verifica si la cadena contiene solo letras minúsculas

`MacAddress()` verifica si es una dirección MAC

`NotEmpty()` verifica si no está vacío

`NullType()` verifica si es nulo

`Number()` verifica si es un número

`ObjectType()` verifica si es de tipo objeto

`StringType()` verifica si es de tipo string

`Url()` verifica si es una URL

Para más reglas de validación, consulta https://respect-validation.readthedocs.io/en/2.0/list-of-rules/


### Más información

Visita https://respect-validation.readthedocs.io/en/2.0/
