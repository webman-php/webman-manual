# Validador
O Composer possui diversos validadores que podem ser utilizados diretamente, por exemplo:
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

**Criar `app/index/validate/User.php`**

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
        'name.require' => 'Nome é obrigatório',
        'name.max'     => 'O nome não pode ter mais de 25 caracteres',
        'age.number'   => 'A idade deve ser um número',
        'age.between'  => 'Idade deve estar entre 1 e 120',
        'email'        => 'Formato de e-mail incorreto',    
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

### Descrição

Projeto para a versão em português do https://github.com/Respect/Validation

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
 
**Acesso através do jQuery**
  
 ```js
 $.ajax({
      url: 'http://127.0.0.1:8787',
      type: "post",
      dataType: 'json',
      data: {nickname:'汤姆', username:'tom cat', password: '123456'}
 });
  ```
  
Obter resultado:

`{"code":500,"msg":"O nome de usuário só pode conter letras (a-z) e números (0-9)"}`

Explicação:

`v::input(array $input, array $rules)` é usado para validar e coletar dados. Se a validação dos dados falhar, uma exceção `Respect\Validation\Exceptions\ValidationException` será lançada, caso contrário, os dados validados serão retornados (em forma de array). 

Se o código de negócios não capturar a exceção de validação, o framework webman capturará automaticamente e, com base no cabeçalho da solicitação HTTP, escolherá se deve retornar os dados em formato JSON (semelhante a `{"code":500, "msg":"xxx"}`) ou uma página de exceção comum. Se o formato de retorno não atender aos requisitos do negócio, o desenvolvedor pode capturar manualmente a exceção `ValidationException` e retornar os dados necessários, semelhante ao exemplo a seguir:

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
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Guia de funcionalidades do Validador

```php
use Respect\Validation\Validator as v;

// Validar regras individuais
$number = 123;
v::numericVal()->validate($number); // true

// Validar cadeias de regras múltiplas
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obter a primeira causa de falha de validação
try {
    $usernameValidator->setName('Nome de usuário')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Nome de usuário só pode conter letras (a-z) e números (0-9)
}

// Obter todas as causas de falha de validação
try {
    $usernameValidator->setName('Nome de usuário')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Vai imprimir
    // -  Nome de usuário deve atender aos seguintes critérios
    //     - Nome de usuário só pode conter letras (a-z) e números (0-9)
    //     - Nome de usuário não pode conter espaços
  
    var_export($exception->getMessages());
    // Vai imprimir
    // array (
    //   'alnum' => 'Nome de usuário só pode conter letras (a-z) e números (0-9)',
    //   'noWhitespace' => 'Nome de usuário não pode conter espaços',
    // )
}

// Mensagem de erro personalizada
try {
    $usernameValidator->setName('Nome de usuário')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Nome de usuário só pode conter letras e números',
        'noWhitespace' => 'Nome de usuário não pode conter espaços',
        'length' => 'O comprimento está em conformidade com a regra, portanto, esta mensagem não será exibida'
    ]);
    // Vai imprimir 
    // array(
    //    'alnum' => 'Nome de usuário só pode conter letras e números',
    //    'noWhitespace' => 'Nome de usuário não pode conter espaços'
    // )
}

// Validar objeto
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validar array
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
v::alpha()->validate(''); // falso 
v::alpha()->validate(null); // falso 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regra negativa
v::not(v::intVal())->validate(10); // falso
```

### Diferenças entre os métodos `validate()` `check()` `assert()` do Validador

`validate()` retorna um valor booleano, não lança exceções

`check()` lança uma exceção em caso de falha de validação, utilizando `$exception->getMessage()` para obter a primeira causa de falha de validação

`assert()` lança uma exceção em caso de falha de validação, podendo-se utilizar `$exception->getFullMessage()` para obter todas as causas de falha de validação
 

### Lista de regras de validação comuns

`Alnum()` contém apenas letras e números

`Alpha()` contém apenas letras

`ArrayType()` tipo de array

`Between(mista $mínimo, mista $máximo)` valida se a entrada está entre dois valores

`BoolType()` valida se é um booleano

`Contains(mista $valorEsperado)` valida se a entrada contém valores específicos

`ContainsAny(array $agulhas)` valida se a entrada contém pelo menos um valor definido

`Digit()` valida se a entrada contém apenas dígitos

`Domain()` valida se é um domínio válido

`Email()` valida se é um endereço de e-mail válido

`Extension(string $extensão)` valida a extensão

`FloatType()` valida se é um número decimal

`IntType()` valida se é um número inteiro

`Ip()` valida se é um endereço IP

`Json()` valida se é um dado JSON

`Length(int $mínimo, int $máximo)` valida se o comprimento está dentro do intervalo dado

`LessThan(misto $compararPara)` valida se o comprimento é menor que o valor fornecido

`Lowercase()` valida se está em minúsculas

`MacAddress()` valida se é um endereço MAC

`NotEmpty()` valida se não está vazio

`NullType()` valida se é nulo

`Number()` valida se é um número

`ObjectType()` valida se é um objeto

`StringType()` valida se é uma string

`Url()` valida se é um URL
  
Para mais regras de validação, acesse https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
 ### Mais conteúdos

Acesse https://respect-validation.readthedocs.io/en/2.0/
