# ตัวตรวจสอบ
Composer มีตัวตรวจสอบหลายตัวที่สามารถใช้งานได้โดยตรง เช่น:
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## ตัวตรวจสอบ top-think/think-validate

### คำอธิบาย
ตัวตรวจสอบหลักของ ThinkPHP

### ที่อยู่โปรเจกต์
https://github.com/top-think/think-validate

### การติดตั้ง
`composer require topthink/think-validate`

### เริ่มต้น

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
        'name.require' => 'ต้องระบุชื่อ',
        'name.max'     => 'ชื่อต้องมีความยาวไม่เกิน 25 ตัวอักษร',
        'age.number'   => 'อายุต้องเป็นตัวเลข',
        'age.between'  => 'อายุต้องอยู่ระหว่าง 1-120',
        'email'        => 'รูปแบบอีเมลไม่ถูกต้อง',    
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

โปรเจกต์เป็นรุ่นภาษาจีนของ https://github.com/Respect/Validation 

### ที่อยู่โปรเจกต์
https://github.com/walkor/validation
  
### การติดตั้ง

```php
composer require workerman/validation
```

### เริ่มต้น

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
        return json(['code' => 0, 'msg' => 'ดีแล้ว']);
    }
}  
```

**ผ่าน jquery**

```js
$.ajax({
    url : 'http://127.0.0.1:8787',
    type : "post",
    dataType:'json',
    data : {nickname:'ส้มตำ', username:'tom cat', password: '123456'}
});
```
ผลลัพธ์:

`{"code":500,"msg":"ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น"}`

คำอธิบาย:

`v::input(array $input, array $rules)` ใช้ในการตรวจสอบและเก็บข้อมูล หากการตรวจสอบล้มเหลว จะโยนข้อยกเว้น`Respect\Validation\Exceptions\ValidationException` ผ่าน ถ้าตรวจสอบสำเร็จจะได้รับข้อมูลที่ตรวจสอบแล้ว (มีลิสต์แอร์เรย์) 

หากโค้ดธุรกิจไม่จับข้อยกเว้นการตรวจสอบได้ webman framework จะจับข้อยกเว้นโดยอัตโนมัติและเลือกที่จะส่งข้อมูล json (เช่น `{"code":500, "msg":"xxx"}`) หรือหน้าข้อยกเว้นทั่วไปตามข้อกำหนดการร้องขอ HTTP หากรูปแบบที่ส่งไม่ตรงตามต้องการของธุรกิจ เจ้าของธุรกิจสามารถจับข้อยกเว้น`ValidationException`เองและส่งข้อมูลที่ต้องการ ตามตัวอย่างด้านล่าง:

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
        return json(['code' => 0, 'msg' => 'ดีแล้ว', 'data' => $data]);
    }
}
```

### แนะนำฟังก์ชัน Validator 

```php
use Respect\Validation\Validator as v;

// ตรวจสอบกฎเดียว
$number = 123;
v::numericVal()->validate($number); // จริง

// ตรวจสอบลูกโซ่ของกฎหลายๆอัน
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // จริง

// รับเหตุผลที่ตรวจสอบล้มเหลวครั้งแรก
try {
    $usernameValidator->setName('ชื่อผู้ใช้')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น
}

// รับเหตุผลทั้งหมดที่ตรวจสอบล้มเหลว
try {
    $usernameValidator->setName('ชื่อผู้ใช้')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // จะพิมพ์
    // -  ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น
    //     - ชื่อผู้ใช้ ไม่สามารถมีช่องว่างได้
  
    var_export($exception->getMessages());
    // จะพิมพ์
    // array (
    //   'alnum' => 'ชื่อผู้ใช้ ต้องประกอบด้วยตัวอักษร (a-z) และตัวเลข (0-9) เท่านั้น',
    //   'noWhitespace' => 'ชื่อผู้ใช้ ไม่สามารถมีช่องว่างได้',
    // )
}

// ปรับเปลี่ยนข้อผิดพลาดที่กำหนดเอง
try {
    $usernameValidator->setName('ชื่อผู้ใช้')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น',
        'noWhitespace' => 'ชื่อผู้ใช้ไม่สามารถมีช่องว่างได้',
        'length' => 'length ตรงกับกฎ ดังนั้น อันนี้จะไม่แสดง'
    ]);
    // จะพิมพ์ 
    // array(
    //    'alnum' => 'ชื่อผู้ใช้ต้องประกอบด้วยตัวอักษรและตัวเลขเท่านั้น',
    //    'noWhitespace' => 'ชื่อผู้ใช้ไม่สามารถมีช่องว่างได้'
    // )
}

// ตรวจสอบอ็อบเจ็กต์
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // จริง

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
  
// ตรวจสอบออฟชันอย่างเลือกได้
v::alpha()->validate(''); // เท็จ
v::alpha()->validate(null); // เท็จ
v::optional(v::alpha())->validate(''); // จริง
v::optional(v::alpha())->validate(null); // จริง

// กฎที่เป็นตรงกันข้มูล
v::not(v::intVal())->validate(10); // เท็จ
```

### ฟังก์ชัน Validator 3 ฟังก์ชัน `validate()` `check()` `assert()` แตกต่างกันอย่างไร

`validate()` จะคืนค่าบูลีนภายนอกเบ้าไดร์เมื่อตรวจสอบล้มเหลว

`check()` เมื่อตรวจสอบล้มเหลวจะโยนข้อยกเว้นและสามารถใช้ `$exception->getMessage()` สำหรับเหตุผลล้มเหลวครั้งแรก

`assert()` เมื่อตรวจสอบล้มเหลวจะโยนข้อยกเว้นและสามารถใช้ `$exception->getFullMessage()` สำหรับเหตุผลล้มเหลวทั้งหมด

### รายการกฎอยู่สม่ำเสมอ

`Alnum()` อยู่เฉพาะตัวอักษรและตัวเลข

`Alpha()` อยู่เฉพาะตัวอักษร

`ArrayType()` ประเภทอาร์เรย์

`Between(mixed $minimum, mixed $maximum)` ตรวจสอบยืดหยุ่นว่าข้อมูลอยู่ระหว่างค่าอื่นๆ

`BoolType()` ตรวจสอบว่าเป็นบูลีนหรือไม่

`Contains(mixed $expectedValue)` ตรวจสอบว่าข้อมูลมีโดยสารของค่าไหน

`ContainsAny(array $needles)` ตรวจสอบว่าข้อมูลมีอย่างน้อยหนึ่งค่าที่ถูกกำหนดไว้

`Digit()` ตรวจสอบว่าข้อมูลมีเฉพาะตัวเลขเท่านั้น

`Domain()` ตรวจสอบว่าเป็นโดเมนที่ถูกต้องหรือไม่

`Email()` ตรวจสอบว่าเป็นอีเมลที่ถูกต้องหรือไม่

`Extension(string $extension)` ตรวจสอบส่วนขยาย

`FloatType()` ตรวจสอบว่าเป็นทศนิยมหรือไม่

`IntType()` ตรวจสอบว่าเป็นจำนวนเต็มหรือไม่

`Ip()` ตรวจสอบว่าเป็นที่อยู่ไอพีหรือไม่

`Json()` ตรวจสอบว่าเป็นข้อมูล json

`Length(int $min, int $max)` ตรวจสอบความยาวของข้อมูลอยู่ในช่วงที่กำหนด

`LessThan(mixed $compareTo)` ตรวจสอบว่าความยาวมีค่าน้อยกว่าค่าที่กำหนด

`Lowercase()` ตรวจสอบว่าเป็นตัวอักษรพิมพ์เล็กหรือไม่

`MacAddress()` ตรวจสอบว่าเป็นแอดเดรสแมคหรือไม่

`NotEmpty()` ตรวจสอบว่าไม่เป็นค่าว่าง

`NullType()` ตรวจสอบว่าเป็นค่า null

`Number()` ตรวจสอบว่าเป็นตัวเลข

`ObjectType()` ตรวจสอบว่าเป็นอ็อบเจ็กต์

`StringType()` ตรวจสอบว่าเป็นประเภทของสตริง

`Url()` ตรวจสอบว่าเป็น url

สำหรับรายการกฎการตรวจสอบอื่นๆ ดูได้ที่ https://respect-validation.readthedocs.io/en/2.0/list-of-rules/

### เนื้อหาเพิ่มเติม

เยี่ยมชม https://respect-validation.readthedocs.io/en/2.0/
