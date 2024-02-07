# Validators

There are many validators available in the composer that can be used directly, such as:

#### [top-think/think-validate](#think-validate)

#### [respect/validation](#respect-validation)

<a name="think-validate"></a>
## Validator top-think/think-validate

### Description
Official validator for ThinkPHP

### Project URL
https://github.com/top-think/think-validate

### Installation
`composer require topthink/think-validate`

### Quick Start

**Create `app/index/validate/User.php`**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule = [
        'name' => 'require|max:25',
        'age' => 'number|between:1,120',
        'email' => 'email',
    ];

    protected $message = [
        'name.require' => 'Name is required',
        'name.max' => 'Name cannot exceed 25 characters',
        'age.number' => 'Age must be a number',
        'age.between' => 'Age must be between 1 and 120',
        'email' => 'Invalid email',
    ];

}
```
  
**Usage**
```php
$data = [
    'name' => 'thinkphp',
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

This project is a Chinese version of https://github.com/Respect/Validation

### Project URL

https://github.com/walkor/validation
  
  
### Installation
 
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
            'username' => v::alnum()->length(5, 64)->setName('Username'),
            'password' => v::length(5, 64)->setName('Password')
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
      data : {nickname:'Tom', username:'tomcat', password: '123456'}
  });
  ```
  
Result:

`{"code":500,"msg":"Username can only contain letters (a-z) and numbers (0-9)"}`

Explanation:

`v::input(array $input, array $rules)` is used to validate and collect data. If the data validation fails, it throws a `Respect\Validation\Exceptions\ValidationException` exception; if successful, it returns the validated data (array).

If the validation exception is not caught by the business code, the webman framework will automatically catch it and return json data (similar to `{"code":500, "msg":"xxx"}`) or a normal exception page based on the HTTP request header. If the returned format does not meet the business requirements, developers can catch the `ValidationException` exception themselves and return the required data, similar to the example below:

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

### Validator Function Guide

```php
use Respect\Validation\Validator as v;

// Single rule validation
$number = 123;
v::numericVal()->validate($number); // true

// Multiple rules chained validation
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Get the first validation failure reason
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username can only contain letters (a-z) and numbers (0-9)
}

// Get all validation failure reasons
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Will print
    // -  Username must follow these rules
    //    - Username can only contain letters (a-z) and numbers (0-9)
    //    - Username cannot contain spaces
  
    var_export($exception->getMessages());
    // Will print
    // array (
    //   'alnum' => 'Username can only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username cannot contain spaces',
    // )
}

// Custom error messages
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username can only contain letters and numbers',
        'noWhitespace' => 'Username cannot have spaces',
        'length' => 'This message won't show, as it satisfies the length rule'
    ]);
    // Will print 
    // array(
    //    'alnum' => 'Username can only contain letters and numbers',
    //    'noWhitespace' => 'Username cannot have spaces'
    // )
}

// Object validation
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Array validation
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
    ->assert($data); // Also can use check() or validate()
  
// Optional validation
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Negated rule
v::not(v::intVal())->validate(10); // false
```
  
### Differences between the `validate()`, `check()`, and `assert()` methods

`validate()` returns a boolean and does not throw an exception

`check()` throws an exception when the validation fails, with the first validation failure reason accessible via `$exception->getMessage()`

`assert()` throws an exception when the validation fails, with all validation failure reasons accessible via `$exception->getFullMessage()`
  
  
### List of Common Validation Rules

`Alnum()` - Only contains letters and numbers

`Alpha()` - Only contains letters

`ArrayType()` - Array type

`Between(mixed $minimum, mixed $maximum)` - Validates whether the input is between two other values.

`BoolType()` - Validates whether it is a Boolean

`Contains(mixed $expectedValue)` - Validates whether the input contains certain values

`ContainsAny(array $needles)` - Validates whether the input contains at least one of the defined values

`Digit()` - Validates whether the input only contains numbers

`Domain()` - Validates whether it is a valid domain name

`Email()` - Validates whether it is a valid email address

`Extension(string $extension)` - Validates the file extension

`FloatType()` - Validates whether it is a floating point number

`IntType()` - Validates whether it is an integer

`Ip()` - Validates whether it is an IP address

`Json()` - Validates whether it is JSON data

`Length(int $min, int $max)` - Validates whether the length is within a given range

`LessThan(mixed $compareTo)` - Validates whether the length is less than a given value

`Lowercase()` - Validates whether it is in lowercase

`MacAddress()` - Validates whether it is a MAC address

`NotEmpty()` - Validates whether it is not empty

`NullType()` - Validates whether it is null

`Number()` - Validates whether it is a number

`ObjectType()` - Validates whether it is an object

`StringType()` - Validates whether it is a string

`Url()` - Validates whether it is a URL
  
For more validation rules, please refer to https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### More Information

Please visit https://respect-validation.readthedocs.io/en/2.0/
