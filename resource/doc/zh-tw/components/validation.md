# 驗證器
Composer提供了許多驗證器可以直接使用，例如：
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
# 驗證器 top-think/think-validate

## 說明
ThinkPHP官方驗證器

## 專案地址
https://github.com/top-think/think-validate

## 安裝
`composer require topthink/think-validate`

## 快速開始

**創建 `app/index/validate/User.php`**

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
        'name.require' => '名稱必須',
        'name.max' => '名稱最多不能超過25個字元',
        'age.number' => '年齡必須是數字',
        'age.between' => '年齡只能在1-120之間',
        'email' => '郵箱格式錯誤',
    ];
}
```

**使用**
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
# 驗證器 workerman/validation

## 說明

該專案為 https://github.com/Respect/Validation 的中文化版本

## 專案地址

https://github.com/walkor/validation


## 安裝
 
```php
composer require workerman/validation
```

## 快速開始

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
            'nickname' => v::length(1, 64)->setName('暱稱'),
            'username' => v::alnum()->length(5, 64)->setName('使用者名稱'),
            'password' => v::length(5, 64)->setName('密碼')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```

**透過jquery訪問**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'湯姆', username:'tom cat', password: '123456'}
  });
  ```

獲得結果：

`{"code":500,"msg":"使用者名稱 只能包含字母（a-z）和數字（0-9）"}`

說明：

`v::input(array $input, array $rules)` 用來驗證並收集數據，如果數據驗證失敗，則拋出`Respect\Validation\Exceptions\ValidationException`異常，驗證成功則將返回驗證後的數據(陣列)。

如果業務代碼未捕獲驗證異常，則webman框架將自動捕獲並根據HTTP請求頭選擇返回json數據(類似`{"code":500, "msg":"xxx"}`)或者普通的異常頁面。如返回格式不符合業務需求，開發者可自行捕獲`ValidationException`異常並返回需要的數據，類似下面的例子：

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
                'username' => v::alnum()->length(5, 64)->setName('使用者名稱'),
                'password' => v::length(5, 64)->setName('密碼')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```
## 驗證器功能指南

```php
use Respect\Validation\Validator as v;

// 單個規則驗證
$number = 123;
v::numericVal()->validate($number); // true

// 多個規則鍊式驗證
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 獲得第一個驗證失敗原因
try {
    $usernameValidator->setName('使用者名稱')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // 使用者名稱 只能包含字母（a-z）和數字（0-9）
}

// 獲得所有驗證失敗的原因
try {
    $usernameValidator->setName('使用者名稱')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 將會列印
    // -  使用者名稱 必須符合以下規則
    //     - 使用者名稱 只能包含字母（a-z）和數字（0-9）
    //     - 使用者名稱 不能包含空格
  
    var_export($exception->getMessages());
    // 將會列印
    // array (
    //   'alnum' => '使用者名稱 只能包含字母（a-z）和數字（0-9）',
    //   'noWhitespace' => '使用者名稱 不能包含空格',
    // )
}

// 自定義錯誤提示訊息
try {
    $usernameValidator->setName('使用者名稱')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => '使用者名稱只能包含字母和數字',
        'noWhitespace' => '使用者名稱不能有空格',
        'length' => 'length符合規則，所以這條將不會顯示'
    ]));
    // 將會列印 
    // array(
    //    'alnum' => '使用者名稱只能包含字母和數字',
    //    'noWhitespace' => '使用者名稱不能有空格'
    // )
}

// 驗證物件
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// 驗證陣列
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
  
// 可選驗證
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 否定規則
v::not(v::intVal())->validate(10); // false
```
  
## 驗證器三個方法 `validate()` `check()` `assert()` 區別

`validate()`返回布爾型，不會拋出異常

`check()`驗證失敗時拋出異常，通過`$exception->getMessage()`第一條驗證失敗的原因

`assert()`驗證失敗時拋出異常，通過`$exception->getFullMessage()`可以獲得所有驗證失敗的原因
  
  
## 常用驗證規則列表

`Alnum()` 只包含字母和數字

`Alpha()` 只包含字母字元

`ArrayType()` 陣列類型

`Between(mixed $minimum, mixed $maximum)` 驗證輸入是否在其他兩個值之間。

`BoolType()` 驗證是否是布林型別

`Contains(mixed $expectedValue)` 驗證輸入是否包含某些值

`ContainsAny(array $needles)` 驗證輸入是否至少包含一個已定義的值

`Digit()` 驗證輸入是否僅包含數字

`Domain()` 驗證是否合法的網域名稱

`Email()` 驗證是否合法的郵件地址

`Extension(string $extension)` 驗證後綴名

`FloatType()` 驗證是否是浮點型別

`IntType()` 驗證是否是整數

`Ip()` 驗證是否是IP位址

`Json()` 驗證是否是JSON資料

`Length(int $min, int $max)` 驗證長度是否在給定區間內

`LessThan(mixed $compareTo)` 驗證長度是否小於給定值

`Lowercase()` 驗證是否是小寫字母

`MacAddress()` 驗證是否是MAC位址

`NotEmpty()` 驗證是否為非空值

`NullType()` 驗證是否為null

`Number()` 驗證是否為數字

`ObjectType()` 驗證是否為對象

`StringType()` 驗證是否為字串型別

`Url()` 驗證是否為URL
  
更多驗證規則請參見 https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ 
  
## 更多內容

請訪問 https://respect-validation.readthedocs.io/en/2.0/
