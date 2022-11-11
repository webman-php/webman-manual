# Validator
composerThere are many validators that can be used directly in, for example：
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## Validator top-think/think-validate

### Description
ThinkPHPOfficial Validator

### Project address
https://github.com/top-think/think-validate

### Install
`composer require topthink/think-validate`

### Quick Start

**New `app/index/validate/User.php`**

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
        'name.require' => 'Name must',
        'name.max'     => 'Names should not exceed a maximum of 25 characters',
        'age.number'   => 'Age must be numeric',
        'age.between'  => 'Age can only be between 1-120',
        'email'        => 'Incorrect email format',    
    ];

}
```
  
**Usage**
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

### Description

The project is a Chinese version of https://github.com/Respect/Validation

### Project address

https://github.com/walkor/validation
  
  
### Install
 
```php
composer require workerman/validation
```

### Quick Start

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
            'username' => v::alnum()->length(5, 64)->setName('username'),
            'password' => v::length(5, 64)->setName('password')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Access via jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Get results：

`{"code":500,"msg":"Username can only contain letters (a-z) and numbers（0-9）"}`

Description：

`v::input(array $input, array $rules)` Used to validate and collect data，If data validation fails，then throw`Respect\Validation\Exceptions\ValidationException`exception，A successful validation will return the validated data(array)。

If the business code does not capture the validation exception, the webman framework will automatically capture and return json data (similar to `{"code":500, "msg":"xxx"}`) or a normal exception page based on the HTTP request header option. If the return format does not meet the business requirements, developers can catch `ValidationException` exception and return the required data by themselves, similar to the following example ：

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
                'username' => v::alnum()->length(5, 64)->setName('username'),
                'password' => v::length(5, 64)->setName('password')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### Validator Feature Guide

```php
use Respect\Validation\Validator as v;

// Single rule validation
$number = 123;
v::numericVal()->validate($number); // true

// Multiple Rules Chain Validation
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Get the first reason for authentication failure
try {
    $usernameValidator->setName('username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username can only contain letters (a-z) and numbers（0-9）
}

// Get all validation failure reasons
try {
    $usernameValidator->setName('username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Will print
    // -  Username must conform to the following rules
    //     - Username can only contain letters (a-z) and numbers（0-9）
    //     - Username cannot contain spaces
  
    var_export($exception->getMessages());
    // Will print
    // array (
    //   'alnum' => 'Username can only contain letters (a-z) and numbers（0-9）',
    //   'noWhitespace' => 'Username cannot contain spaces',
    // )
}

// Customize error message
try {
    $usernameValidator->setName('username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username can only contain letters and numbers',
        'noWhitespace' => 'Username cannot have spaces',
        'length' => 'lengthComplies with the rule, so this entry will not be displayed'
    ]);
    // Will print 
    // array(
    //    'alnum' => 'Username can only contain letters and numbers',
    //    'noWhitespace' => 'Username cannot have spaces'
    // )
}

// Validate Object
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// ValidationArray
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
    ->assert($data); // You can also use check() or validate()
  
// Optional validation
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negate Rules
v::not(v::intVal())->validate(10); // false
```
  
### Validator The three methods `validate()` `check()` `assert()` are different

`validate()`Return boolean, no exceptions are thrown

`check()`Exception thrown on validation failure, first reason for validation failure via `$exception->getMessage()`

`assert()`Exceptions are thrown on validation failure, and all reasons for validation failure can be obtained via `$exception->getFullMessage()`
  
  
### Common authentication rules list

`Alnum()` Contains only letters and numbers

`Alpha()` Include only letters

`ArrayType()` Array Type

`Between(mixed $minimum, mixed $maximum)` Validate that the input is between two other values。

`BoolType()` Verify if it is a boolean

`Contains(mixed $expectedValue)` Verify that the input contains certain values

`ContainsAny(array $needles)` Verify that the input contains at least one defined value

`Digit()` Validate that the input contains only numbers

`Domain()` Verify that it is a legitimate domain

`Email()` Verify that it is a legitimate email address

`Extension(string $extension)` Validate Suffix Name

`FloatType()` Validate if it is floating point

`IntType()` Verify if it is an integer

`Ip()` Verify that it is an ip address

`Json()` Validate if it is json data

`Length(int $min, int $max)` Validate that the length is in the given interval

`LessThan(mixed $compareTo)` Validate that the length is less than the given value

`Lowercase()` Verify that it is a lowercase letter

`MacAddress()` verify if it is a mac address

`NotEmpty()` Validate for null

`NullType()` Verify ifnull

`Number()` verify if it is a number

`ObjectType()` Validate if object

`StringType()` Validate if it is a string type

`Url()` Verify ifurl
  
For more validation rules see  https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
### More content

Access https://respect-validation.readthedocs.io/en/2.0/
  

