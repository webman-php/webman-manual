# Validador

O composer possui muitos validadores que podem ser usados diretamente, como por exemplo:

#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validador top-think/think-validate

### Descrição
Validador oficial do ThinkPHP

### Endereço do projeto
https://github.com/top-think/think-validate

### Instalação
`composer require topthink/think-validate`

### Início rápido

**Crie `app/index/validate/User.php`**

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
        'name.require' => 'Nome necessário',
        'name.max'     => 'Nome não pode exceder 25 caracteres',
        'age.number'   => 'Idade deve ser um número',
        'age.between'  => 'Idade deve estar entre 1 e 120',
        'email'        => 'Formato de e-mail incorreto',    
    ];

}
```
  
**Utilização**
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

### Descrição

Projeto como a versão em mandarim do https://github.com/Respect/Validation

### Endereço do projeto

https://github.com/walkor/validation
  
  
### Instalação
 
```php
composer require workerman/validation
```

### Início rápido

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
            'nickname' => v::length(1, 64)->setName('Apelido'),
            'username' => v::alnum()->length(5, 64)->setName('Nome de usuário'),
            'password' => v::length(5, 64)->setName('Senha')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Acessado via jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Resultado:

`{"code":500,"msg":"Nome de usuário deve conter somente letras (a-z) e números (0-9)"}`

Descrição:

`v::input(array $input, array $rules)` é usado para validar e coletar dados. Se a validação falhar, lançará uma exceção `Respect\Validation\Exceptions\ValidationException`. Se for bem-sucedido, retornará os dados validados (array).

Se o código de negócios não captura a exceção de validação, o framework webman capturará automaticamente e retornará dados json (semelhante a `{"code":500, "msg":"xxx"}`) ou uma página de exceção normal, com base no cabeçalho da solicitação HTTP. Se o formato retornado não atender aos requisitos do negócio, o desenvolvedor pode capturar manualmente a exceção `ValidationException` e retornar os dados necessários, semelhante ao exemplo a seguir:

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
                'username' => v::alnum()->length(5, 64)->setName('Nome de usuário'),
                'password' => v::length(5, 64)->setName('Senha')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'dados' => $data]);
    }
}
```

### Guia de funcionalidades do Validador

```php
use Respect\Validation\Validator as v;

// Validação de regra única
$number = 123;
v::numericVal()->validate($number); // true

// Validação encadeada de várias regras
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obter a primeira razão de falha de validação
try {
    $usernameValidator->setName('Nome de usuário')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Nome de usuário deve conter somente letras (a-z) e números (0-9)
}

// Obter todas as razões de falha de validação
try {
    $usernameValidator->setName('Nome de usuário')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Vai imprimir
    // -  Nome de usuário deve atender às seguintes regras
    //     - Nome de usuário deve conter somente letras (a-z) e números (0-9)
    //     - Nome de usuário não pode conter espaços
  
    var_export($exception->getMessages());
    // Vai imprimir
    // array (
    //   'alnum' => 'Nome de usuário deve conter somente letras (a-z) e números (0-9)',
    //   'noWhitespace' => 'Nome de usuário não pode conter espaços',
    // )
}

// Mensagens de erro personalizadas
try {
    $usernameValidator->setName('Nome de usuário')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Nome de usuário deve conter somente letras e números',
        'noWhitespace' => 'Nome de usuário não pode conter espaços',
        'length' => 'length atende a regra, então esta mensagem não será exibida'
    ]);
    // Vai imprimir 
    // array(
    //    'alnum' => 'Nome de usuário deve conter somente letras e números',
    //    'noWhitespace' => 'Nome de usuário não pode conter espaços'
    // )
}

// Validação de objeto
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validação de array
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
    ->assert($data); // Também pode ser usado check() ou validate()
  
// Validação opcional
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regra de negação
v::not(v::intVal())->validate(10); // false
```
  
### Diferença entre os três métodos do Validador `validate()` `check()` `assert()`

`validate()` retorna um valor booleano e não lança exceções

`check()` lança uma exceção quando a validação falha, obtendo a primeira razão de falha através de `$exception->getMessage()`

`assert()` lança uma exceção quando a validação falha, obtendo todas as razões de falha através de `$exception->getFullMessage()`
  
### Lista de regras de validação comuns

`Alnum()` contém apenas letras e números

`Alpha()` contém apenas letras

`ArrayType()` tipo de array

`Between(mixed $minimum, mixed $maximum)` valida se a entrada está entre dois valores

`BoolType()` valida se é do tipo booleano

`Contains(mixed $expectedValue)` valida se a entrada contém alguns valores

`ContainsAny(array $needles)` valida se a entrada contém pelo menos um dos valores definidos

`Digit()` valida se a entrada contém apenas dígitos

`Domain()` valida se é um domínio válido

`Email()` valida se é um endereço de e-mail válido

`Extension(string $extension)` valida a extensão

`FloatType()` valida se é do tipo float

`IntType()` valida se é um número inteiro

`Ip()` valida se é um endereço IP

`Json()` valida se é um dado json

`Length(int $min, int $max)` valida se o comprimento está dentro do intervalo especificado

`LessThan(mixed $compareTo)` valida se o comprimento é menor que o valor especificado

`Lowercase()` valida se é uma letra minúscula

`MacAddress()` valida se é um endereço MAC

`NotEmpty()` valida se não está vazio

`NullType()` valida se é nulo

`Number()` valida se é um número

`ObjectType()` valida se é um objeto

`StringType()` valida se é do tipo string

`Url()` valida se é uma URL
  
Para mais regras de validação, consulte https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
### Mais conteúdo

Visite https://respect-validation.readthedocs.io/en/2.0/
