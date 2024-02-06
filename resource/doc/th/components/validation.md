# ตัวตรวจสอบ
คุณสามารถใช้ composer เพื่อใช้งานตัวตรวจสอบหลายรูปแบบ เช่น:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## ตัวตรวจสอบ top-think/think-validate

### คำอธิบาย
ตัวตรวจสอบอย่างเป็นทางการของ ThinkPHP

### ที่อยู่โปรเจกต์
https://github.com/top-think/think-validate

### วิธีการติดตั้ง
`composer require topthink/think-validate`

### การเริ่มต้น
**สร้าง `app/index/validate/User.php`**

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
        'name.require' => 'ต้องกรอกชื่อ',
        'name.max'     => 'ชื่อต้องไม่เกิน 25 ตัวอักษร',
        'age.number'   => 'อายุต้องเป็นตัวเลข',
        'age.between'  => 'อายุต้องอยู่ระหว่าง 1-120',
        'email'        => 'รูปแบบอีเมลไม่ถูกต้อง'   
    ];

}
```
  
**การใช้**
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
# ตัวตรวจสอบ workerman/validation

### คำอธิบาย
โปรเจกต์นี้เป็นเวอร์ชันที่ถูกแปลเป็นภาษาจีนของ https://github.com/Respect/Validation

### ที่อยู่โปรเจกต์
https://github.com/walkor/validation
  
  
### วิธีการติดตั้ง
 
```php
composer require workerman/validation
```

### การเริ่มต้น

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
            'nickname' => v::length(1, 64)->setName('ชื่อเล่น'),
            'username' => v::alnum()->length(5, 64)->setName('ชื่อผู้ใช้'),
            'password' => v::length(5, 64)->setName('รหัสผ่าน')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'สำเร็จ']);
    }
}  
```
  
**เข้าถึงผ่าน jquery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'แท็ค', username:'tom cat', password: '123456'}
  });
  ```
  
ผลลัพธ์ที่ได้:

`{"code":500,"msg":"ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น"}`

คำอธิบาย:

`v::input(array $input, array $rules)` เป็นการตรวจสอบและรวบรวมข้อมูล หากการตรวจสอบล้มเหลวจะโยนข้อยกเว้น `Respect\Validation\Exceptions\ValidationException` ถ้าตรวจสอบผ่านจะคืนข้อมูลที่ตรวจสอบแล้ว (เป็นอาร์เรย์)

หากโค้ดธุรกิจไม่จับข้อยกเว้นการตรวจสอบ เฟรมเวิร์ก webman จะจับข้อยกเว้นโดยอัตโนมัติและสามารถเลือกคืนข้อมูลเช่น json หรือหน้าข้อยกเว้นปกติตามหัวข้อ HTTP หากผลเรทที่คืนไม่ตรงตามความต้องการของธุรกิจ ผู้พัฒนาสามารถจับข้อยกเว้น `ValidationException` ด้วยตนเองและคืนข้อมูลที่ต้องการ เช่นตัวอย่างด้านล่าง:

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
                'username' => v::alnum()->length(5, 64)->setName('ชื่อผู้ใช้'),
                'password' => v::length(5, 64)->setName('รหัสผ่าน')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'สำเร็จ', 'data' => $data]);
    }
}
```

### คำแนะนำสำหรับฟังก์ชัน Validator

```php
use Respect\Validation\Validator as v;

// ตรวจสอบกฎเดียว
$number = 123;
v::numericVal()->validate($number); // true

// ตรวจสอบลูกโซ่ของกฎหลายอันต่อเนื่อง
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// รับเหตุผลที่ตรวจสอบล้มเหลวครั้งแรก
try {
    $usernameValidator->setName('ชื่อผู้ใช้')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น
}

// รับเหตุผลทดลองทั้งหมดที่ตรวจสอบล้มเหลว
try {
    $usernameValidator->setName('ชื่อผู้ใช้')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // คำสั่งจะพิมพ์
    // - ชื่อผู้ใช้ ต้องประกอบด้วยกฎต่อไปนี้
    //     - ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น
    //     - ชื่อผู้ใช้ ต้องไม่มีช่องว่าง
  
    var_export($exception->getMessages());
    // คำสั่งจะพิมพ์
    // array (
    //   'alnum' => 'ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น',
    //   'noWhitespace' => 'ชื่อผู้ใช้ ต้องไม่มีช่องว่าง',
    // )
}

// การกำหนดข้อความแสดงผลที่กำหนดเอง
try {
    $usernameValidator->setName('ชื่อผู้ใช้')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น',
        'noWhitespace' => 'ชื่อผู้ใช้ต้องไม่มีช่องว่าง',
        'length' => 'อันนี้ความเท่าตามกฎ ดังนั้นจะไม่แสดง'
    ]);
    // คำสั่งจะพิมพ์ 
    // array(
    //    'alnum' => 'ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น',
    //    'noWhitespace' => 'ชื่อผู้ใช้ต้องไม่มีช่องว่าง'
    // )
}

// ตรวจสอบอ็อबเจ็กต์
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// ตรวจสอบอาร์เรย์
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
    ->assert($data); // สามารถใช้ check() หรือ validate() ได้เช่นกัน
  
// ตรวจสอบที่มีตัวเลือก
v::alpha()->validate(''); // เท็จ 
v::alpha()->validate(null); // เท็จ 
v::optional(v::alpha())->validate(''); // จริง
v::optional(v::alpha())->validate(null); // จริง

// กฎทำให้เป็นประกาศ
v::not(v::intVal())->validate(10); // เท็จ
```
  
### ฟังก์ชัน Validator 3 ฟังก์ชัน `validate()` `check()` `assert()` แตกต่างกันที่ไหน

`validate()` คืนค่าเป็นบูลีน และไม่โยนข้อยกเว้น

`check()` โยนข้อยกเว้นเมื่อตรวจสอบล้มเหลว และสามารถใช้ `$exception->getMessage()` เพื่อรับเหตุผลที่ตรวจสอบล้มเหลวครั้งแรก

`assert()` โยนข้อยกเว้นเมื่อตรวจสอบล้มเหลว และสามารถใช้ `$exception->getFullMessage()` เพื่อรับเหตุผลที่ตรวจสอบล้มเหลวทั้งหมด
  
  
### รายการกฎที่ใช้ตรวจสอบประจำ

`Alnum()` ประกอบด้วยตัวอักษรและตัวเลขเท่านั้น

`Alpha()` ประกอบด้วยตัวอักษรเท่านั้น

`ArrayType()` ชนิดของอาร์เรย์

`Between(mixed $minimum, mixed $maximum)` ตรวจสอบว่าได้รับข้อมูลอยู่ระหว่างค่าอื่น ๆ

`BoolType()` ตรวจสอบว่าเป็นชนิดบูล

`Contains(mixed $expectedValue)` ตรวจสอบว่าได้รับข้อมูลที่มีค่าอยู่ภายใน

`ContainsAny(array $needles)` ตรวจสอบว่าได้รับข้อมูลที่มีอย่างน้อยหนึ่งค่าที่กำหนด

`Digit()` ตรวจสอบว่าได้รับข้อมูลที่มีตัวเลขเท่านั้น

`Domain()` ตรวจสอบว่าเป็นโดเมนที่ถูกต้อง

`Email()` ตรวจสอบว่าเป็นที่อยู่อีเมลที่ถูกต้อง

`Extension(string $extension)` ตรวจสอบนามสกุลไฟล์

`FloatType()` ตรวจสอบว่าเป็นชนิดทศนิยม

`IntType()` ตรวจสอบว่าเป็นจำนวนเต็ม

`Ip()` ตรวจสอบว่าเป็นที่อยู่ IP

`Json()` ตรวจสอบว่าเป็นข้อมูลที่เป็น JSON

`Length(int $min, int $max)` ตรวจสอบความยาวว่าอยู่ในช่วงที่กำหนด

`LessThan(mixed $compareTo)` ตรวจสอบความยาวน้อยกว่าค่าที่กำหนด

`Lowercase()` ตรวจสอบว่าเป็นตัวอักษรพิมพ์เล็ก

`MacAddress()` ตรวจสอบว่าเป็นที่อยู่ MAC

`NotEmpty()` ตรวจสอบว่าไม่ว่างเปล่า

`NullType()` ตรวจสอบว่าเป็นค่าว่าง

`Number()` ตรวจสอบว่าเป็นตัวเลข

`ObjectType()` ตรวจสอบว่าเป็นอ๊อบเจ็กต์

`StringType()` ตรวจสอบว่าเป็นชนิดสตริง

`Url()` ตรวจสอบว่าเป็น URL
  
สำหรับรายการกฎที่ใช้ตรวจสอบเพิ่มเติม โปรดดูที่ https://respect-validation.readthedocs.io/en/2.0/list-of-rules/
  
### เนื้อหาเพิ่มเติม

เยี่ยมชม https://respect-validation.readthedocs.io/en/2.0/
