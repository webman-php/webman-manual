# المدقق
يمكنك استخدام العديد من المدققين المتاحة مباشرة من خلال composer، على سبيل المثال:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## المدقق top-think/think-validate

### الوصف
مدقق رسمي لـ ThinkPHP

### عنوان المشروع
https://github.com/top-think/think-validate

### التثبيت
`composer require topthink/think-validate`

### البدء السريع

**أنشئ `app/index/validate/User.php`**

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
        'name.require' => 'الاسم مطلوب',
        'name.max'     => 'يجب ألا يتجاوز الاسم 25 حرفًا',
        'age.number'   => 'يجب أن يكون العمر رقمًا',
        'age.between'  => 'يجب أن يكون العمر بين 1 و 120',
        'email'        => 'صيغة البريد الإلكتروني غير صحيحة',    
    ];

}
```
  
**الاستخدام**
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
# المدقق workerman/validation

### الوصف

هذا المشروع هو نسخة مترجمة لـ https://github.com/Respect/Validation

### عنوان المشروع

https://github.com/walkor/validation
  
  
### التثبيت
 
```php
composer require workerman/validation
```

### البدء السريع

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
            'nickname' => v::length(1, 64)->setName('اللقب'),
            'username' => v::alnum()->length(5, 64)->setName('اسم المستخدم'),
            'password' => v::length(5, 64)->setName('كلمة المرور')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'جيد']);
    }
}  
```
  
**من خلال jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'توم', username:'توم كات', password: '123456'}
  });
  ```
  
النتيجة:

`{"code":500,"msg":"اسم المستخدم يجب أن يحتوي فقط على حروف (a-z) وأرقام (0-9)"}`

الملاحظات:

`v::input(array $input, array $rules)` يُستخدم للتحقق وجمع البيانات، إذا فشل التحقق، سيثير استثناء `Respect\Validation\Exceptions\ValidationException`، في حال نجاح التحقق، سيتم إرجاع البيانات المحققة (كمصفوفة).

إذا لم يتم التقاط استثناء التحقق بواسطة الكود التجاري، ستقوم الهيكل الأساسي لـ webman بالتقاطه تلقائيًا ويختار إما إرجاع بيانات json (مثل `{"code":500, "msg":"xxx"}`) أو صفحة استثناء عادية اعتمادًا على رأس الطلب HTTP. في حال عدم تطابق العملية مع متطلبات العمل، يمكن للمطورين التقاط استثناء `ValidationException` يدويًا وإرجاع البيانات المطلوبة، مثل الاستثناء التالي:

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
                'username' => v::alnum()->length(5, 64)->setName('اسم المستخدم'),
                'password' => v::length(5, 64)->setName('كلمة المرور')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'جيد', 'data' => $data]);
    }
}
```

### دليل ميزات المدقق

```php
use Respect\Validation\Validator as v;

// التحقق من قاعدة واحدة
$number = 123;
v::numericVal()->validate($number); // true

// التحقق من سلسلة من القواعد
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// الحصول على سبب فشل التحقق الأول
try {
    $usernameValidator->setName('اسم المستخدم')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // اسم المستخدم يجب أن يحتوي فقط على حروف (a-z) وأرقام (0-9)
}

// الحصول على جميع أسباب فشل التحقق
try {
    $usernameValidator->setName('اسم المستخدم')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // سيطبع
    // -  اسم المستخدم يجب أن يتوافق مع القواعد التالية
    //     - اسم المستخدم يجب أن يحتوي فقط على حروف (a-z) وأرقام (0-9)
    //     - اسم المستخدم لا ينبغي أن يحتوي على مسافات

    var_export($exception->getMessages());
    // سيطبع
    // array (
    //   'alnum' => 'اسم المستخدم يجب أن يحتوي فقط على حروف (a-z) وأرقام (0-9)',
    //   'noWhitespace' => 'اسم المستخدم لا ينبغي أن يحتوي على مسافات',
    // )
}

// تخصيص رسالة الخطأ
try {
    $usernameValidator->setName('اسم المستخدم')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'اسم المستخدم يجب أن يحتوي فقط على الحروف والأرقام',
        'noWhitespace' => 'الاسم لا ينبغي أن يحتوي على مسافات',
        'length' => 'حسنًا'
    ]);
    // سيطبع 
    // array(
    //     'alnum' => 'اسم المستخدم يجب أن يحتوي فقط على الحروف والأرقام',
    //     'noWhitespace' => 'الاسم لا ينبغي أن يحتوي على مسافات'
    // )
}

// التحقق من الكائن
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// التحقق من المصفوفة
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
    ->assert($data); // يمكن أيضا استخدام check() أو validate()
  
// التحقق الاختياري
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// قاعدة سلبية
v::not(v::intVal())->validate(10); // false
```
  
### الفرق بين الأساليب `validate()` `check()` `assert()` للمدقق

`validate()` تعيد قيمة بولية، ولا تثير استثناءات

`check()` تثير استثناء عند فشل التحقق، ويمكن الحصول على السبب الأول للفشل من خلال `$exception->getMessage()`

`assert()` تثير استثناء عند فشل التحقق، ويمكن الحصول على جميع أسباب الفشل التحقق من خلال `$exception->getFullMessage()`

  
### قائمة شائعة الاستخدام لقواعد التحقق

`Alnum()` فقط حروف وأرقام

`Alpha()` فقط حروف

`ArrayType()` نوع المصفوفة

`Between(mixed $minimum, mixed $maximum)` التحقق من أن الإدخال بين قيمتين معينتين.

`BoolType()` التحقق من أنها من النوع البولي

`Contains(mixed $expectedValue)` التحقق من أن الإدخال يحتوي على قيم معينة

`ContainsAny(array $needles)` التحقق من أن الإدخال يحتوي على واحد على الأقل من القيم المحددة

`Digit()` التحقق من أن الإدخال يحتوي على أرقام فقط

`Domain()` التحقق من صحة اسم النطاق

`Email()` التحقق من أنها بريد إلكتروني صحيح

`Extension(string $extension)` التحقق من امتداد الملف

`FloatType()` التحقق من أنها من النوع عشري

`IntType()` التحقق من أنها عدد صحيح

`Ip()` التحقق من أنها عنوان IP

`Json()` التحقق من أنها بيانات json

`Length(int $min, int $max)` التحقق من أن الطول في النطاق المحدد

`LessThan(mixed $compareTo)` التحقق من أن الطول أقل من القيمة المحددة

`Lowercase()` التحقق من أنها حروف صغيرة

`MacAddress()` التحقق من أنها عنوان MAC

`NotEmpty()` التحقق من أن الإدخال ليس فارغًا

`NullType()` التحقق من أن الإدخال هو فارغ

`Number()` التحقق من أن الإدخال عدد

`ObjectType()` التحقق من أن الإدخال من نوع كائن

`StringType()` التحقق من أن الإدخال من نوع سلسلة

`Url()` التحقق من أن الإدخال عنوان URL

لمزيد من قواعد التحقق يرجى زيارة https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### المزيد

لمزيد من المعلومات يُرجى زيارة https://respect-validation.readthedocs.io/en/2.0/
