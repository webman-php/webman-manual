# เริ่มต้นอย่างรวดเร็ว

โมเดล webman นี้ขึ้นอยู่กับ [Eloquent ORM](https://laravel.com/docs/7.x/eloquent) ทุกรายการในฐานข้อมูลมี "โมเดล" ที่เกี่ยวข้องใช้สื่อสารกับตารางนั้น ๆ คุณสามารถค้นหาข้อมูลในตารางและแทรกบันทึกใหม่ในตารางด้วยโมเดล

ก่อนที่จะเริ่มต้น โปรดตรวจสอบว่าคุณตั้งค่าการเชื่อมต่อฐานข้อมูลใน `config/database.php`

> หมายเหตุ: ในการสนับสนุนตัวกระตุ้นโมเดลคุณต้องนำเข้าเพิ่มเติม `composer require "illuminate/events"` [ตัวอย่าง](#โมเดลตัวกระตุ้น)

## ตัวอย่าง 
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * ชื่อตารางที่เกี่ยวข้องกับโมเดล
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * กำหนดค่าหลักเริ่มต้น ซึ่งเป็น id โดยค่าหลักคือ 'uid'
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * ระบุว่าต้องการบำรุงรักษาประวัติเวลาโดยอัตโนมัติหรือไม่
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## ชื่อตาราง
คุณสามารถระบุตารางข้อมูลที่กำหนดเองโดยการกำหนดค่าสำหรับสร้างตารางในโมเดล:
```php
class User extends Model
{
    /**
     * ชื่อตารางที่เกี่ยวข้องกับโมเดล
     *
     * @var string
     */
    protected $table = 'user';
}
```

## คีย์หลัก
Eloquent ยังสมมติว่าทุกรายการในตารางข้อมูลมีคอลัมน์หลักชื่อ id คุณสามารถกำหนดค่าสำหรับคีย์หลักโดยใช้ $primaryKey ที่เป็น protected ได้:
```php
class User extends Model
{
    /**
     * กำหนดค่าหลักเริ่มต้น ซึ่งเป็น id โดยค่าหลักคือ 'uid'
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent สมมติว่าคีย์หลักเป็นค่าจำนวนเติมที่เพิ่มขึ้นโดยอัตโนมัติ ซึ่งหมายถึงโดยค่าดีฟอลต์คีย์หลักจะถูกแปลงเป็นชนิด int ถ้าคุณต้องการใช้คีย์หลักที่ไม่ได้เพิ่มขึ้นโดยอัตโนมัติหรือไม่ใช่ข้อมูลตัวเลข คุณต้องตั้งค่าของ $incrementing เป็น false ได้ในส่วนของคีย์หลักที่เป็น public:
```php
class User extends Model
{
    /**
     * ระบุว่าคีย์หลักของโมเดลเป็นค่าเพิ่มขึ้นโดยอัตโนมัติหรือไม่
     *
     * @var bool
     */
    public $incrementing = false;
}
```

ถ้าคีย์หลักของคุณไม่ใช่ค่าจำนวนเติม คุณต้องตั้งค่าของ $keyType ที่ใช้กับโมเดลเป็น string:
```php
class User extends Model
{
    /**
     * ประเภทการเพิ่มขึ้นอัตโนมัติของ ID
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## ประวัติเวลา
โดยค่าดีฟอลต์ Eloquent คาดหวังว่าตารางของคุณจะมี created_at และ updated_at ถ้าคุณไม่ต้องการให้ Eloquent บำรุงรักษาคอลัมน์เหล่านี้โปรดตั้งค่าของ $timestamps ในโมเดลเป็น false:
```php
class User extends Model
{
    /**
     * ระบุว่าต้องบำรุงรักษาประวัติเวลาโดยอัตโนมัติหรือไม่
     *
     * @var bool
     */
    public $timestamps = false;
}
```

ถ้าคุณต้องการกำหนดรูปแบบเก็บรักษาประวัติเวลา โปรดตั้งค่าของ $dateFormat ในโมเดลของคุณ คุณสามารถกำหนดวิธีการเก็บข้อมูลวันที่ในฐานข้อมูลและรูปแบบ JSON ของโมเดล
```php
class User extends Model
{
    /**
     * รูปแบบเก็บรักษาประวัติเวลา
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

ถ้าคุณต้องการกำหนดชื่อคอลัมน์ที่เก็บรักษาประวัติเวลา คุณสามารถตั้งค่าค่าคงที่ CREATED_AT และ UPDATED_AT ของโมเดลเพื่อทำโดย:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## การเชื่อมต่อฐานข้อมูล
โดยค่าดีฟอลต์ โมเดล Eloquent จะใช้การเชื่อมต่อฐานข้อมูลเริ่มต้นที่คุณกำหนดในแอปของคุณ ถ้าคุณต้องการกำหนดการเชื่อมต่อที่แตกต่างกันสำหรับโมเดล โปรดตั้งค่าของ $connection ในโมเดล:
```php
class User extends Model
{
    /**
     * ชื่อการเชื่อมต่อของโมเดล
     *
     * @var string
     */
    protected $connection = 'ชื่อการเชื่อมต่อ';
}
```

## ค่าเริ่มต้นที่กำหนด
ถ้าคุณต้องการกำหนดค่าเริ่มต้นของคุณสำหรับคอลัมน์บางส่วนในโมเดล คุณสามารถกำหนด $attributes ในโมเดล:
```php
class User extends Model
{
    /**
     * ค่าเริ่มต้นของโมเดล
     *
     * @var array
     */
    protected $attributes = [
        'การเลื่อน' => false,
    ];
}
```

## การเรียกค้นหาโมเดล
หลังจากการสร้างโมเดลและตารางฐานข้อมูลที่เกี่ยวข้องกัน คุณสามารถค้นหาข้อมูลจากฐานข้อมูลได้ คิดว่าแต่ละโมเดลใน Eloquent คือ Query Builder ที่มีสมรรถนะเข้าถึงข้อมูลที่เกี่ยวข้องได้เร็วขึ้น ตัวอย่างเช่น:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> ข้อแนะนำ: เนื่องจากโมเดล Eloquent ยังเป็น Query Builder คุณควรทราบทุกเมธอดที่สามารถใช้ใน [Query Builder](queries.md) คุณสามารถใช้เมธอดเหล่านั้นในการค้นหา Eloquent

## เงื่อนไขเพิ่มเติม
เมื่อใช้เมธอด all ของ Eloquent จะทำการส่งคืนผลลัพธ์ทั้งหมดของโมเดล โดยส่วนในแต่ละโมเดล Eloquent สามารถเพิ่มเงื่อนไขในการค้นหาและใช้เมธอด get ในการพบผลลัพธ์จากการค้นหา:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## การโหลดโมเดลใหม่
คุณสามารถใช้วิธี fresh และ refresh เพื่อโหลดโมเดลใหม่ fresh จะโหลดโมเดลใหม่จากฐานข้อมูลโมเดลปัจจุบันจะไม่ได้รับผลกระทบ:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh จะใช้ข้อมูลใหม่จากฐานข้อมูลในการปรับค่าแก้ไขโมเดลปัจจุบันในการโหลดซึ่งนอกจากนั้นมีการโหลดโมเดลซื่ออีกครั้ง:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## คอลเลกชัน
เมธอด all และ get ของ Eloquent สามารถค้นหาผลลัพธ์มากกว่า 1 รายการและส่งคืนอินสแตนสฉบับ `Illuminate\Database\Eloquent\Collection` เอกสาร `Collection` นี้มีเมธอดในการจัดการจำนวนมากที่ให้บริการสำหรับผลลัพธ์ Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## ใช้การอ่าน
เมธอด cursor ให้คุณใช้การอ่านข้อมูลในฐานข้อมูล มันทำการค้นหาข้อมูลแค่ 1 ครั้ง การใช้การอ่านจะช่วยให้ประหยัดทรัพยากรแรมเมื่อทำงานกับข้อมูลจำนวนมาก:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

การใช้ cursor จะส่งคืน `Illuminate\Support\LazyCollection` เอกสาร [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) ช่วยให้คุณใช้เมธอดในคลัส Laravel ที่เกี่ยวข้องมากที่สุดและจะโหลดโมเดลตัวเดียวขึ้นมาในหน่วยความจำทุกครั้ง:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```
## คำสั่ง Select ที่เกี่ยวข้อง
Eloquent มีการรองรับ Subquery ที่ขั้นสูง ซึ่งให้คุณสามารถดึงข้อมูลจากตารางที่เกี่ยวข้องด้วยคำสั่ง Query เดียว ตัวอย่างเช่น ถ้าเรามีตารางปลายทาง destinations และตารางเที่ยวบิน flights โดยตาราง flights มีฟิลด์ arrival_at ซึ่งแสดงว่าเที่ยวบินมาถึงปลายทางเมือไหร่

โดยใช้ฟังก์ชัน select และ addSelect จาก Subquery  เราสามารถดึงข้อมูลทั้งหมดจากปลายทาง destinations และชื่อเที่ยวบินสุดท้ายที่มาถึงทุกปลายทางได้ดังนี้
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```
## เรียงลำดับโดย Subquery
นอกจากนี้ คำสั่ง orderBy ของ query builder ยังรองรับ Subquery โดยคุณสามารถใช้ฟังก์ชันนี้เพื่อเรียงลำดับปลายทางต่าง ๆ ตามเวลาของเที่ยวบินสุดท้ายที่มาถึง อีกทั้งนี้เราสามารถทำ Subquery ให้ query builder ทำงานด้วยคำสั่งเดียวเท่านั้น:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## ค้นหาโมเดล / คอลเล็กชันเดียว
นอกเหนือจากการดึงข้อมูลทั้งหมดจากตารางที่ระบุไว้แล้ว คุณยังสามารถใช้ find, first หรือ firstWhere ในการค้นหาเรคคอร์ดเดี่ยว ซึ่งจะคืน instance ของโมเดลเดี่ยว ไม่ใช่คอลเล็กชันของโมเดล:
```php
// ค้นหาโมเดลที่มีคีย์หลัก...
$flight = app\model\Flight::find(1);

// ค้นหาโมเดลแรกที่ตรงเงื่อนไขการค้นหา...
$flight = app\model\Flight::where('active', 1)->first();

// ค้นหาโมเดลแรกที่ตรงเงื่อนไขการค้นหาด้วย firstWhere...
$flight = app\model\Flight::firstWhere('active', 1);
```

คุณยังสามารถใช้อาร์เรย์ของคีย์หลักเป็นพารามิเตอร์ที่จะใช้ในการเรียกใช้ find ซึ่งจะคืนคอลเล็กชันของเรคคอร์ดที่ตรงกัน:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

บางครั้งคุณอาจหวังที่จะดำเนินการอื่นเมื่อไม่พบผลลัพธ์เมื่อค้นหาเรคคอร์ดแรก ฟังก์ชัน firstOr จะคืนค่าเรคคอร์ดแรกเมื่อพบผลลัพธ์ หากไม่พบผลลพธ์ จะดำเนินการ callback ที่กำหนดไว้ ค่าที่ return จาก callback จะคืนค่าของ firstOr:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
ฟังก์ชัน firstOr ยังรับที่อาร์เรย์ของคอลัมน์เป็นคำสั่งที่จะใช้ใน query ได้เช่นกัน:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## ข้อยกเว้น “ไม่พบ” 
บางครั้งคุณอาจหวังที่จะทำการยกเว้นเมื่อไม่พบโมเดล เช่นในภายในคอนโทรลเลอร์และเส้นทาง  findOrFail และ firstOrFail จะดึงเรคคอร์ดแรกของค้นหาและหากไม่พบ จะเกิดข้อยกเว้น Illuminate\Database\Eloquent\ModelNotFoundException:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## ดึงคอลเล็กชัน
คุณยังสามารถใช้ คำสั่ง count, sum และ max จาก query builder และฟังก์ชันอื่น ๆ ที่เกี่ยวกับคอลเล็กชันเพื่อดำเนินการกับคอลเล็กชัน ฟังก์ชันเหล่านี้จะคืนค่าสกัล่าลต์ที่ถูกต้องแทนที่จะเป็น istance ของ model:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## เพิ่มข้อมูล
เพื่อเพิ่มข้อมูลเข้าฐานข้อมูล คุณต้องสร้าง instance ของโมเดลใหม่ก่อน ตั้งค่าคุณสมบัติของ instance นั้น ๆ และเรียกใช้ save:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * เพิ่มบันทึกใหม่ในตารางผู้ใช้
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // ยืนยันการร้องขอ

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

Time ที่่ created_at และ updated_at จะถูกตั้งค่าโดยอัตโนมัติ (when the property $timestamps is true for the model) และไม่จำเป็นต้องตั้งค่าด้วยมือ

## การปรับปรุง
ฟังก์ชัน save ยังสามารถใช้ในการปรับปรุงข้อมูลของโมเดลที่มีอยู่ในฐานข้อมูล โดยการอัพเดทค่าที่ต้องการและเรียกใช้ save อีกครั้ง เช่นเดียวกับ  updated_at ที่จะถูกอัพเดทโดยอัตโนมัติน ทำให้ไม่จำเป็นต้องตั้งค่าด้วยมือ:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## การปรับปรุงแบบเป็นกลุ่ม
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## ตรวจสอบการเปลี่ยนแปลงของคุณสมบัติ
Eloquent มี isDirty, isClean และ wasChanged เพื่อตรวจสอบสถานะภายในของโมเดลและกำหนดค่าว่าคุณสมบัติเปลี่ยนไปจากตอนโหลดตั้งแต่ใด ๆ  isDirty กำหนดค่าที่กำหนดไว้เมื่อเรคคอร์ดโหลดมาแล้วมีการเปลี่ยนแปลงใด ๆ คุณยังสามารถเรียกใช้พารามิเตอร์พิเศษซึ่งเป็นคุณสมบัติที่จะกำหนดว่าคุณสมบัติใดมีค่าที่เปลี่ยน:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
ฟังก์ชัน wasChanged กำหนดให้ตรวจสอบว่าค่าของคุณสมบัติที่เก็บขี้นการร้องขอล่าสุดไปลิเวล ถึงข่องที่คุณยังสามารถเรียกใช้หรือไม่ก็ได้ไปลิเวลมติ เพื่อดูคุณสมบัติที่เปลี่ยนไป:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```
## การกำหนดค่าเป็นกลุ่ม (Batch Assigning)

คุณสามารถใช้เมธอด create เพื่อบันทึกโมเดลใหม่ได้เช่นกัน วิธีนี้จะคืนค่าอินสแตนซ์ของโมเดล อย่างไรก็ตามก่อนที่จะใช้งาน คุณจำเป็นต้องระบุพร็อพเพอร์ตี้ fillable หรือ guarded ในโมเดล เนื่องจากโมเดล Eloquent ทั้งหมดมีค่าเริ่มต้นที่ไม่อนุญาตให้กำหนดค่าเป็นกลุ่ม

เมื่อผู้ใช้งานส่งพารามิเตอร์ HTTP ที่ไม่คาดหวังเข้ามา และพารามิเตอร์นั้นเปลี่ยนแปลงคอลัมน์ในฐานข้อมูลที่คุณไม่ต้องการที่จะเปลี่ยน การโจมตีด้วยการกำหนดค่าเป็นกลุ่มก็จะเกิดขึ้น ตัวอย่างเช่น: ผู้ใช้ที่ไม่ดีอารมณ์อาจส่งพารามิเตอร์ is_admin ผ่านคำขอ HTTP และนำมาให้เมธอด create ดำเนินการ การดำเนินการนี้จะทำให้ผู้ใช้สามารถยกระดับตนเองเป็นผู้ดูแลระบบ

ดังนั้นก่อนที่คุณจะเริ่มต้น คุณควรกำหนดว่าพร็อพเพอร์ตี้ใดบนโมเดลที่สามารถถูกกำหนดค่าเป็นกลุ่ม คุณสามารถทำได้โดยใช้ $fillable บนโมเดล ตัวอย่างเช่น: กำหนดให้พร็อพเพอร์ตี้ name ของโมเดล Flight สามารถถูกกำหนดค่าเป็นกลุ่มได้

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * พร็อพเพอร์ตี้ที่สามารถถูกกำหนดค่าเป็นกลุ่ม
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```

เมื่อเรากำหนดค่าเป็นกลุ่มไว้แล้ว คุณสามารถใช้เมธอด create เพื่อแทรกข้อมูลใหม่ลงในฐานข้อมูลได้  เมธอด create จะคืนอินสแตนซ์ของโมเดลที่บันทึกไว้:

```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```

หากคุณมีอินสแตนซ์ของโมเดลอยู่แล้ว คุณสามารถส่งอาร์เรย์โดยใช้เมธอด fill เพื่อกำหนดค่า:

```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable สามารถถือเป็น "รายชื่อที่สามารถถูกกำหนดค่าเป็นกลุ่ม" คุณยังสามารถใช้ $guarded เพื่อดำเนินการเช่นกัน $guarded ประกอบด้วยอาร์เรย์ของพร็อพเพอร์ตี้ที่ไม่อนุญาตให้ถูกกำหนดค่าเป็นกลุ่ม กล่าวคือ $guarded จะทำหน้าที่เป็น "รายชื่อดำ" โปรดทราบ: คุณสามารถใช้ $fillable หรือ $guarded ได้เพียงหนึ่งตัวเท่านั้น ไม่สามารถใช้พร้อมกัน. ในตัวอย่างด้านล่าง ทุกๆ คุณลักษณะภายนอก price สามารถถูกกำหนดค่าเป็นกลุ่ม:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * คุณสมบัติที่ไม่อนุญาตให้กำหนดค่าเป็นกลุ่ม
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

หากคุณต้องการที่จะให้ทุกคุณลักษณะสามารถถูกกำหนดค่าเป็นกลุ่ม คุณสามารถกำหนด $guarded เป็นอาร์เรย์ว่างได้:

```php
/**
* คุณสมบัติที่ไม่อนุญาตให้กำหนดค่าเป็นกลุ่ม
*
* @var array
*/
protected $guarded = [];
```

## วิธีการสร้างโมเดลอื่นๆ

firstOrCreate/ firstOrNew

ที่นี่มีสองวิธีที่คุณอาจใช้เพื่อกำหนดค่าเป็นกลุ่ม: firstOrCreate และ firstOrNew วิธี firstOrCreate จะพยายามจะจับคู่ข้อมูลในฐานข้อมูลโดยใช้คีย์/ค่าที่ระบุ หากไม่พบโมเดลในฐานข้อมูล จะสร้างบันทึกหนึ่งที่รวมถึงคุณลักษณะที่ระบุในพารามิเตอร์แรก และคุณลักษณะทางเลือกที่สองที่ระบุ

วิธี firstOrNew ทำการค้นหาในฐานข้อมูลโดยใช้คุณลักษณะที่ระบุ อย่างไรก็ก็ตาย not ถ้าวิธี firstOrNew ค้นหาไม่พบโมเดลที่ตองกับ ก็จะคืนอินสแตนซ์ของโมเดลใหม่ ๆ โดยไม่ได้บันทึกไว้ในฐานข้อมูล คุณต้องเรียกเมธอด save เองเพื่อบันทึก:

```php
// ค้นหาเที่ยวบินด้วยชื่อ ถ้าไม่พบก็สร้าง...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// ค้นหาเที่ยวบินด้วยชื่อ หรือสร้างพร้อมคุณลักษณะ delayed และ arrival_time
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// ค้นหาเที่ยวบินด้วยชื่อ ถ้าไม่พบก็สร้างอินสแตนซ์หนึ่ง
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// ค้นหาเที่ยวบินด้วยชื่อ หรือสร้างอินสแตนซ์ด้วยคุณลักษณะ name, delayed และ arrival_time
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

คุณอาจพบว่าต้องการที่จะปรับปรุงโมเดลที่มีอยู่หรือสร้างโมเดลใหม่ในกรณีที่ไม่มีอยู่ ได้รับการสนับสนุนโดยเมธอด updateOrCreate เทียบเทียบกับ firstOrCreate เมธอด updateOrCreate ทำการสืบค้นโมเดลและบันทึกโมเดลจึงไม่จำเป็นต้องเรียก save():

```php
// หากมีเที่ยวบินจากโอคแลนด์ไปมาสันดีเอโก้ ให้ราคาเท่ากับ 99 ดอลลาร์
// ถ้าไม่เจอโมเดลที่ตรงกับ จะสร้างอันใหม่
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## ลบโมเดล
คุณสามารถเรียกใช้เมธอด delete เพื่อลบอินสแตนท์ของโมเดล:

```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## ลบโมเดลโดยใช้คีย์หลัก
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));

```

## ลบโมเดลโดยใช้การสืบค้น
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## คัดลอกโมเดล
คุณสามารถใช้เมธอด replicate เพื่อคัดลอกอินสแตนท์ที่ยังไม่ได้บันทึกลงในฐานข้อมูล วิธี replicate มีความสะดวกในกรณีที่อินสแตนท์ของโมเดลมีคุณลักษณะที่หลายคุณมีสิ่งสมัช โดยไม่ต้องลงแรงเพื่อสร้างค่าภายใน:

```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();

```

## การเปรียบเทียบโมเดล
บางครั้งอาจจำเป็นต้องตรวจสอบว่าสองโมเดล "เหมือนกัน" หรือไม่ การใช้เมธอด is สามารถใช้เพื่อตรวจสอบโมเดลสองอันว่ามีคีย์หลักเหมือนกัน โต๊ะเดียวกันและเชื่อมต่อฐานข้อมูลเดียวกันหรือไม่:

```php
if ($post->is($anotherPost)) {
    //
}
```

## ดูการดูแลโมเดล
ใช้โดยอ้างอิง [ตัวดูแลของโมเดลและเหตุการณ์ใน Laravel](https://learnku.com/articles/6657/model-events-and-observer-in-laravel)

โปรดทราบ: Eloquent ORM ต้องการที่จะสนับสนุนตัวดูแลโมเดลจะต้องนำ compose เพิ่มเติม

```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```
