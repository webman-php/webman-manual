# Validator
There are many validators available in Composer that can be used directly, such as:
#### <a href="#think-validate">top-think/think-validate</a>
#### <a href="#respect-validation">respect/validation</a>

<a name="think-validate"></a>
## Validator top-think/think-validate
### Description
ThinkPHP official validator
### Project Address
https://github.com/top-think/think-validate
### Install
`composer require topthink/think-validate`
### Getting Started
**Create `app/index/validate/User.php`**
```php
<?php
namespace app\index\validate;
use think\Validate;
class User extends Validate {
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];
    protected $message  =   [
        'name.require' => 'Name is required',
        'name.max'     => 'Name cannot exceed 25 characters',
        'age.number'   => 'Age must be a number',
        'age.between'  => 'Age must be between 1 and 120',
        'email'        => 'Invalid email format',    
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
## Validator workerman/validation
### Description
Project for the translated version of https://github.com/Respect/Validation
### Project Address
https://github.com/walkor/validation
### Install
```php
composer require workerman/validation
```
### Getting Started
```php
<?php
namespace app\controller;
use support\Request;
use Respect\Validation\Validator as v;
use support\Db;
class IndexController {
    public function index(Request $request) {
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
**Access via jQuery**
```js
$.ajax({
    url : 'http://127.0.0.1:8787',
    type : "post",
    dataType:'json',
    data : {nickname:'Tom', username:'tom cat', password: '123456'}
});
```
Result:
`{"code":500,"msg":"Username must only contain letters (a-z) and numbers (0-9)"}`

Explanation:
`v::input(array $input, array $rules)` is used to validate and collect data. If data validation fails, it throws a `Respect\Validation\Exceptions\ValidationException` exception. If validation is successful, it returns the validated data (array). If the business logic does not capture the validation exception, the webman framework will automatically catch it and return json data (similar to `{"code":500, "msg":"xxx"}`) or a normal exception page based on the HTTP request header. If the returned format does not meet the business requirements, developers can capture the `ValidationException` exception and return the required data, similar to the example below:
```php
<?php
namespace app\controller;
use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;
class IndexController {
    public function index(Request $request) {
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
// Multiple rule chaining validation
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true
// Get the first validation failure reason
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username must only contain letters (a-z) and numbers (0-9)
}
// Get all validation failure reasons
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Will print
    // -  Username must comply with the following rules
    //   - Username must only contain letters (a-z) and numbers (0-9)
    //   - Username cannot contain spaces
    var_export($exception->getMessages());
    // Will print
    // array (
    // 'alnum' => 'Username must only contain letters (a-z) and numbers (0-9)',
    // 'noWhitespace' => 'Username cannot contain spaces',
    // )
}
// Custom error messages
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username must only contain letters and numbers',
        'noWhitespace' => 'Username cannot have spaces',
        'length' => 'Length conforms to the rule, so this will not be displayed'
    ]);
    // Will print
    // array(
    // 'alnum' => 'Username must only contain letters and numbers',
    // 'noWhitespace' => 'Username cannot have spaces'
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
    ->assert($data); // can also use check() or validate()
// Optional validation
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true
// Negative rules
v::not(v::intVal())->validate(10); // false
```
### Difference between the `validate()`, `check()`, and `assert()` methods
`validate()` returns a boolean without throwing an exception
`check()` throws an exception when validation fails, with the reason obtainable through `$exception->getMessage()`
`assert()` throws an exception when validation fails, and provides all validation failure reasons through `$exception->getFullMessage()`
### Common Validation Rule List
`Alnum()` contains only letters and numbers
`Alpha()` contains only letters
`ArrayType()` array type
`Between(mixed $minimum, mixed $maximum)` validates whether the input is between two other values
`BoolType()` validates whether it is a boolean type
`Contains(mixed $expectedValue)` validates whether the input contains certain values
`ContainsAny(array $needles)` validates whether the input contains at least one defined value
`Digit()` validates whether the input contains only digits
`Domain()` validates whether it is a valid domain
`Email()` validates whether it is a valid email address
`Extension(string $extension)` validates the file extension
`FloatType()` validates whether it is a float
`IntType()` validates whether it is an integer
`Ip()` validates whether it is an IP address
`Json()` validates whether it is JSON data
`Length(int $min, int $max)` validates the length is within the given range
`LessThan(mixed $compareTo)` validates whether the length is less than the given value
`Lowercase()` validates whether it is a lowercase letter
`MacAddress()` validates whether it is a MAC address
`NotEmpty()` validates whether it is not empty
`NullType()` validates whether it is null
`Number()` validates whether it is a number
`ObjectType()` validates whether it is an object
`StringType()` validates whether it is a string type
`Url()` validates whether it is a URL
For more validation rules, visit [List of Rules](https://respect-validation.readthedocs.io/en/2.0/list-of-rules/)
### More
Visit [Respect Validation Documentation](https://respect-validation.readthedocs.io/en/2.0/) for more information