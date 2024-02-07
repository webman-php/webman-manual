# ভেরিফায়ার
কম্পোজারে অনেক ভেরিফায়ার প্যাকেজ রয়েছে যা সরাসরি ব্যবহার করা যায়, উদাহরণ:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## ভেরিফায়ার top-think/think-validate

### বিবরণ
থিংকপিএইচপি অফিশিয়াল ভেরিফায়ার

### প্রকল্পের ঠিকানা
https://github.com/top-think/think-validate

### ইনস্টলেশন
`composer require topthink/think-validate`

### দ্রুত শুরু

**নতুন তৈরি করুন `app/index/validate/User.php`**

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
        'name.require' => 'নাম প্রয়োজন',
        'name.max'     => 'নামের দৈর্ঘ্য 25 অক্ষরের বেশি হতে পারে না',
        'age.number'   => 'বয়স হতে হবে সংখ্যা',
        'age.between'  => 'বয়স 1 থেকে 120 এর মধ্যে হতে হবে',
        'email'        => 'ইমেইল ফর্ম্যাট ভুল',    
    ];

}
``` 

**ব্যবহার**
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
# ভেরিফায়ার workerman/validation

### বিবরণ
প্রকল্পটি https://github.com/Respect/Validation এর বাংলা সংস্করণ।

### প্রকল্পের ঠিকানা
https://github.com/walkor/validation

### ইনস্টলেশন
 
```php
composer require workerman/validation
``` 

### দ্রুত শুরু

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
            'nickname' => v::length(1, 64)->setName('নাম'),
            'username' => v::alnum()->length(5, 64)->setName('ব্যবহারকারী নাম'),
            'password' => v::length(5, 64)->setName('পাসওয়ার্ড')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ঠিক আছে']);
    }
}  
``` 

**যেমন jquery দ্বারা অ্যাক্সেস**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'টম', username:'টম ক্যাট', password: '123456'}
  });
  ``` 

রেজাল্ট:

`{"code":500,"msg":"ব্যবহারকারী নাম কেবল অক্ষর (a-z) এবং সংখ্যা (0-9) থাকতে পারে"}`

ব্যাখ্যা:
`v::input(array $input, array $rules)` তথ্য যাচাই এবং সংগ্রহের জন্য ব্যবহার করা হয়, যদি ডেটা যাচাই ব্যর্থ হয় তাহলে `Respect\Validation\Exceptions\ValidationException` এক্সপশন নিক্ষেপ করবে, ভেরিফাই সফল হলে যাচাইযোগ্য তথ্য (অ্যারে) রিটার্ন করবে।

যদি ব্যবসায়িক কোড যাচাইর এক্সপশন না ধরে তাহলে webman ফ্রেমওয়ার্ক স্বয়ংক্রিয়ভাবে এক্সপশন ধরে নিবে এবং এইচটিটিপি অনুরোধ শিরোনাম অনুযায়ী জেসন ডেটা (মতামত `{"code":500, "msg":"xxx"}`) বা সাধারণ এক্সপশন পেইজ ফিরে পাঠাবে। যদি ফিরে পাঠানো ফরম্যাট ব্যবসায়িক প্রয়োজনীয়তা অনুযায়ী না থাকে তাহলে ডেভেলপার প্রয়োজনীয় তথ্য ধরে নিতে পারেন, উপরে উল্লিখিত উদাহরণের মতো।

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
                'username' => v::alnum()->length(5, 64)->setName('ব্যবহারকারী নাম'),
                'password' => v::length(5, 64)->setName('পাসওয়ার্ড')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ঠিক আছে', 'data' => $data]);
    }
}
``` 

### ভেরিফায়ার ফাংশন গাইড

```php
use Respect\Validation\Validator as v;

// একটি বিধ যাচাই
$number = 123;
v::numericVal()->validate($number); // সত্য

// একাধিক বিধ লিংকল যাচাই
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // সত্য

// প্রথম নামবেশের যাচাই অর্থাৎ যাচাই ব্যর্থ হলে ফলাফল
try {
    $usernameValidator->setName('ব্যবহারকারী নাম')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // ব্যবহারকারী নাম কেবল অক্ষর (a-z) এবং সংখ্যা (0-9) থাকতে পারে
}

try {
    $usernameValidator->setName('ব্যবহারকারী নাম')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // এটা প্রিন্ট হবে
    // -  ব্যবহারকারী নাম অবশ্যই সামঞ্জস্য এর পালন করতে হবে
    //     - ব্যবহারকারী নাম কেবল অক্ষর (a-z) এবং সংখ্যা (0-9) থাকতে পারে
    //     - ব্যবহারকারী নাম ফাঁকা রাখা যাবে না।
  
    var_export($exception->getMessages());
    // এটা প্রিন্ট হবে
    // array (
    //   'alnum' => 'ব্যবহারকারী নাম কেবল অক্ষর (a-z) এবং সংখ্যা (0-9) থাকতে পারে',
    //   'noWhitespace' => 'ব্যবহারকারী নাম ফাঁকা রাখা যাবে না।',
    // )
}
```

### ভেরিফাইয়ার তিনটি মেথড `validate()` `check()` `assert()` এর পার্থক্য
`validate()` বুলিয়ান প্রকার ফেরত দেয়, এক্সপশন না নিয়ে আউট করে।

`check()` যখন যাচাই ব্যর্থ হবে তখন এক্সপশন নিয়ে আউট করে, `exception->getMessage()` দিয়ে প্রথম বিধ অনুশীলনের কারণ পাওয়া যায়।

`assert()` যখন যাচাই ব্যর্থ হবে তখন এক্সপশন নিয়ে আউট করে, `exception->getFullMessage()` দিয়ে সমস্ত যাচাই ব্যর্থ হওয়ার কারণগুলি পাওয়া যায়।

### প্রয়োজনীয় যাচাই প্রায়শচিক্তি তালিকা

`Alnum()` কেবল অক্ষর এবং সংখ্যা থাকতে পারে।

`Alpha()` কেবল অক্ষর থাকতে পারে।

`ArrayType()` অ্যারের ধরন

`Between(mixed $minimum, mixed $maximum)`  যাচাই করুন আনুভূত মানগুলির মধ্যে।

`BoolType()` বুলিয়ান ধরণ

`Contains(mixed $expectedValue)` আনুভূতটি কি নিয়ে যাচাই করে

`ContainsAny(array $needles)` যাচাই করুন আনুভূতে কমপ্লেক্স করা মানগুলি

`Digit()` যাচাই করুন সংখ্যা কেবল

`Domain()` সঠিক ডোমেন নাম কি না

`Email()` সঠিক ইমেইল ঠিকানা কি না

`Extension(string $extension)` সাফটওয়্যার নাম যাচাই করুন

`FloatType()` ম্যান ধরণ

`IntType()` পূর্ণ সংখ্যা ধরণ

`Ip()` আইপি ঠিকান কি না

`Json()` জেসন তথ্য কি না

`Length(int $min, int $max)` দৈর্ঘ্য যাচাই করুন

`LessThan(mixed $compareTo)` প্রতিরূপ নির্ধারণ করুন

`Lowercase()` ছোট হরফের ধরণ

`MacAddress()` ম্যাক ঠিকানা যাচাই করুন

`NotEmpty()` খালি না কি না

`NullType()` নাল ধরণ

`Number()` সংখ্যা যাচাই করুন

`ObjectType()` অবজেক্ট ধরণ

`StringType()` স্ট্রিং ধরণ

`Url()` ইউআরএল যাচাই করুন

অধিক যাচাই নিয়মের তালিকা দেখতে https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ বিজ্ঞাপন করুন।

### অধিক তথ্যের জন্য

দেখুন https://respect-validation.readthedocs.io/en/2.0/
