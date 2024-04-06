# 验证器
composer有很多验证器可以直接在使用，例如：
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
# 验证器 top-think/think-validate

## 说明
ThinkPHP官方验证器

## 项目地址
https://github.com/top-think/think-validate

## 安装
`composer require topthink/think-validate`

## 快速开始

**新建 `app/index/validate/User.php`**

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
        'name.require' => '名称必须',
        'name.max'     => '名称最多不能超过25个字符',
        'age.number'   => '年龄必须是数字',
        'age.between'  => '年龄只能在1-120之间',
        'email'        => '邮箱格式错误',    
    ];

}
```
  
**使用**
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

> **注意**
> webman里不支持think-validate的`Validate::rule()`方法

<a name="respect-validation"></a>
# 验证器 workerman/validation

## 说明

项目为 https://github.com/Respect/Validation 的汉化版本

## 项目地址

https://github.com/walkor/validation
  
  
## 安装
 
```php
composer require workerman/validation
```

## 快速开始

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
            'nickname' => v::length(1, 64)->setName('昵称'),
            'username' => v::alnum()->length(5, 64)->setName('用户名'),
            'password' => v::length(5, 64)->setName('密码')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**通过jquery访问**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'汤姆', username:'tom cat', password: '123456'}
  });
  ```
  
得到结果：

`{"code":500,"msg":"用户名 只能包含字母（a-z）和数字（0-9）"}`

说明：

`v::input(array $input, array $rules)` 用来验证并收集数据，如果数据验证失败，则抛出`Respect\Validation\Exceptions\ValidationException`异常，验证成功则将返回验证后的数据(数组)。

如果业务代码未捕获验证异常，则webman框架将自动捕获并根据HTTP请求头选择返回json数据(类似`{"code":500, "msg":"xxx"}`)或者普通的异常页面。如返回格式不符合业务需求，开发者可自行捕获`ValidationException`异常并返回需要的数据，类似下面的例子：

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
                'username' => v::alnum()->length(5, 64)->setName('用户名'),
                'password' => v::length(5, 64)->setName('密码')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

## Validator 功能指南

```php
use Respect\Validation\Validator as v;

// 单个规则验证
$number = 123;
v::numericVal()->validate($number); // true

// 多个规则链式验证
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 获得第一个验证失败原因
try {
    $usernameValidator->setName('用户名')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // 用户名 只能包含字母（a-z）和数字（0-9）
}

// 获得所有验证失败的原因
try {
    $usernameValidator->setName('用户名')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 将会打印
    // -  用户名 必须符合以下规则
    //     - 用户名 只能包含字母（a-z）和数字（0-9）
    //     - 用户名 不能包含空格
  
    var_export($exception->getMessages());
    // 将会打印
    // array (
    //   'alnum' => '用户名 只能包含字母（a-z）和数字（0-9）',
    //   'noWhitespace' => '用户名 不能包含空格',
    // )
}

// 自定义错误提示信息
try {
    $usernameValidator->setName('用户名')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => '用户名只能包含字母和数字',
        'noWhitespace' => '用户名不能有空格',
        'length' => 'length符合规则，所以这条将不会显示'
    ]);
    // 将会打印 
    // array(
    //    'alnum' => '用户名只能包含字母和数字',
    //    'noWhitespace' => '用户名不能有空格'
    // )
}

// 验证对象
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// 验证数组
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
    ->assert($data); // 也可以用 check() 或 validate()
  
// 可选验证
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 否定规则
v::not(v::intVal())->validate(10); // false
```
  
## Validator 三个方法 `validate()` `check()` `assert()` 区别

`validate()`返回布尔型，不会抛出异常

`check()`验证失败时抛出异常，通过`$exception->getMessage()`第一条验证失败的原因

`assert()`验证失败时抛出异常，通过`$exception->getFullMessage()`可以获得所有验证失败的原因
  
  
## 常用验证规则列表

`Alnum()` 只包含字母和数字

`Alpha()` 只包含字母

`ArrayType()` 数组类型

`Between(mixed $minimum, mixed $maximum)` 验证输入是否在其他两个值之间。

`BoolType()` 验证是否是布尔型

`Contains(mixed $expectedValue)` 验证输入是否包含某些值

`ContainsAny(array $needles)` 验证输入是否至少包含一个定义的值

`Digit()` 验证输入是否只包含数字

`Domain()` 验证是否是合法的域名

`Email()` 验证是否是合法的邮件地址

`Extension(string $extension)` 验证后缀名

`FloatType()` 验证是否是浮点型

`IntType()` 验证是否是整数

`Ip()` 验证是否是ip地址

`Json()` 验证是否是json数据

`Length(int $min, int $max)` 验证长度是否在给定区间

`LessThan(mixed $compareTo)` 验证长度是否小于给定值

`Lowercase()` 验证是否是小写字母

`MacAddress()` 验证是否是mac地址

`NotEmpty()` 验证是否为空

`NullType()` 验证是否为null

`Number()` 验证是否为数字

`ObjectType()` 验证是否为对象

`StringType()` 验证是否为字符串类型

`Url()` 验证是否为url
  
更多验证规则参见 https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
## 更多内容

访问 https://respect-validation.readthedocs.io/en/2.0/
  

