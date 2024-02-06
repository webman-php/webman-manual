# যাচাইকারী
কোম্পোজারের মধ্যে অনেক যাচাইকারী সরাসরি ব্যবহার করা যায়, যেমন:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## যাচাইকারী top-think/think-validate

### বর্ণনা
থিংকপিএইচপি অফিসিয়াল ভ্যালিডেটর

### প্রকল্প ঠিকানা
https://github.com/top-think/think-validate

### ইনস্টল করুন
`composer require topthink/think-validate`

### দ্রুত শুরু

**নতুন করে তৈরি করুন `app/index/validate/User.php`**

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
        'name.require' => 'নাম প্রয়োজন',
        'name.max'     => 'নামের দৈর্ঘ্য ২৫ টির বেশি হতে পারবে না',
        'age.number'   => 'বয়স অবশ্যই সংখ্যা হতে হবে',
        'age.between'  => 'বয়স ১ থেকে ১২০ অবধির্ঘ্যে হতে হবে',
        'email'        => 'ইমেইল ফরমেট ভুল',
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
# যাচাইকারী workerman/validation

### বর্ণনা
প্রকল্প https://github.com/Respect/Validation এর বাংলা ভার্সন।

### প্রকল্প ঠিকানা

https://github.com/walkor/validation

### ইনস্টল করুন
 
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
            'password' => v::length(5, 64)->setName('পাসওয়ার্ড')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ঠিক আছে']);
    }
}  
```

**জেকুয়ারি ব্যবহার করে**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'টম', username:'টম ক্যাট', password: '123456'}
  });
  ```
  
ফলাফল:

`{"code":500,"msg":"ব্যবহারকারী নাম অবশ্যই অক্ষর a-z এবং সংখ্যা ০-৯ হতে হবে"}`

ব্যাখ্যা:

`v::input(array $input, array $rules)` ডেটা যাচাই এবং সংগ্রহ করতে ব্যবহৃত হয়, যদি ডেটা যাচাই ব্যার্থ হয়, তবে `Respect\Validation\Exceptions\ValidationException` অসামঞ্জস্য ছড়াবে, যাচাই সফল থাকলে পরিণত ডেটা দেবে (অ্যারে)।

যদি ব্যবসায়িক কোড যাচাই অসামঞ্জস্য ধরতে না পারে, তবে webman ফ্রেমওয়ার্ক স্বয়ংক্রিয়ভাবে অসামঞ্জস্য ধরে এবং HTTP অনুরোধ শিরোনাম অনুসারে জেসন ডেটা (অনুরোধ দেওয়া হলে `"code":500, "msg":"xxx"`) প্রদান বা সাধারণ সংশোধিত অসামঞ্জস্য পৃষ্ঠা প্রদান করে। যদি প্রত্যাশিত ফরম্যাট প্রদান করা না হয়, ডেভেলপার নিজেই `ValidationException` অনুসারে অসামঞ্জস্য ধরে এবং প্রয়োজনীয় ডেটা (নিচের উদাহরণের মত) প্রদান করতে পারেন:

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
                'password' => v::length(5, 64)->setName('পাসওয়ার্ড')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ঠিক আছে', 'data' => $data]);
    }
}
```

### যাচাইকারী কার্যকলাপ গাইড

```php
use Respect\Validation\Validator as v;

// একক নিয়ম যাচাই
$number = 123;
v::numericVal()->validate($number); // সত্য

// বহু নিয়ম লষ্টবাদে যাচাই
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // সত্য

// অপ্রিশোধিত ভুল কারণ জানতে
try {
    $usernameValidator->setName('ব্যবহারকারী নাম')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // ব্যবহারকারী নাম অবশ্যই অক্ষর a-z এবং সংখ্যা 0-9
}

// সমস্ত ভুল কারণ জানতে
try {
    $usernameValidator->setName('ব্যবহারকারী নাম')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // এটি প্রিন্ট করবে
    // -  ব্যবহারকারী নাম অপরিবর্তিত এই নিয়মানুযায়ী হতে হবে
    //     - ব্যবহারকারী নাম অবশ্যই অক্ষর a-z এবং সংখ্যা 0-9
    //     - ব্যবহারকারী নাম অবশ্যই ফাঁকা হবে।
  
    var_export($exception->getMessages());
    // এটি প্রিন্ট করবে
    // array (
    //   'alnum' => 'ব্যবহারকারী নাম অবশ্যই অক্ষর a-z এবং সংখ্যা 0-9',
    //   'noWhitespace' => 'ব্যবহারকারী নাম অবশ্যই ফাঁকা হবে।',
    // )
}

// কাস্টম ভুল বার্তা
try {
    $usernameValidator->setName('ব্যবহারকারী নাম')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'ব্যবহারকারী নাম অবশ্যই অক্ষর এবং সংখ্যা',
        'noWhitespace' => 'ব্যবহারকারী নাম কোনও শূন্যস্থান থাকতে পারবে না',
        'length' => 'দৈর্ঘ্য নিয়মে অনুযায়ী হতে হবে, তাই এটি কোনো ভুল বার্তা না'
    ]);
    // এটি প্রিন্ট করবে 
    // array(
    //    'alnum' => 'ব্যবহারকারী নিউয়াম অবশ্যই অক্ষর এবং সংখ্যা',
    //    'noWhitespace' => 'ব্যবহারকারী নাম কোনও শূন্যস্থান থাকতে পারবে না'
    // )
}

// সত্যাপিত বস্তু
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // সত্য

// সত্যাপিত অ্যারে
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
    ->assert($data); // এটি সত্যাপন করতে পারে যা যাচাই করা যায়।
  
// ঐচ্ছিক ভেরিফাই
v::alpha()->validate(''); // মিথ্যা 
v::alpha()->validate(null); // মিথ্যা 
v::optional(v::alpha())->validate(''); // সত্য
v::optional(v::alpha())->validate(null); // সত্য

// নির্যাতন নিয়ম
v::not(v::intVal())->validate(10); // মিথ্যা
```
  
### ভেরিফাই অবস্থান গুলি `validate()` `check()` `assert()` এর পার্থক্য

`validate()` সান্দ্বোপূর্ণ রিটার্ন করে, কোন অসামঞ্জস্য ছড়ানোর ক্ষেত্রে অসামঞ্জস্য বস্তু উঠায় না

`check()` যাচাই ব্যার্থ হলে অসামঞ্জস্য ছড়াতে, `$exception->getMessage()` সাহায্য করে প্রথম যাচাই ব্যার্থের বার্তা

`assert()` যাচাই ব্যার্থ হলে অসামঞ্জস্য ছড়াতে, `$exception->getFullMessage()` দিয়ে সকল যাচাই ব্যার্থের বার্তা পাওয়া যায়

### প্রয়োজনীয় ভেরিফাই বিধানী সূচি

`Alnum()` কেবল অক্ষর এবং সংখ্যা বিশিষ্ট

`Alpha()` কেবল অক্ষর বিশিষ্ট

`ArrayType()` অ্যারের ধরন

`Between(mixed $minimum, mixed $maximum)` যাচাই করে যে ইনপুটটি অন
