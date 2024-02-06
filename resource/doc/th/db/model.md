# เริ่มต้นอย่างรวดเร็ว

โมเดลของ webman มีพื้นฐานตาม [Eloquent ORM](https://laravel.com/docs/7.x/eloquent) ทุกรายการในฐานข้อมูลจะมี "โมเดล" ที่สอดคล้องกับตารางนั้น ๆ เพื่อทำงานร่วมกันกับตารางดังกล่าว คุณสามารถค้นข้อมูลในตารางฐานข้อมูลผ่านโมเดล และแทรกบันทึกใหม่ในตารางดังกล่าว

ก่อนที่จะเริ่มต้น โปรดตรวจสอบว่าได้กำหนดค่าการเชื่อมต่อฐานข้อมูลใน `config/database.php` อย่างเหมาะสม

> หมายเหตุ: Eloquent ORM ต้องการการนำเข้าเสริมเติมเพื่อรองรับผู้สังเกตของโมเดล `composer require "illuminate/events"` [ตัวอย่าง](#model-observers)

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
     * กำหนด primary key ใหม่ ๆ
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * บ่งชี้ว่าควรบำรุง timestamp โดยอัตโนมัติหรือไม่
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## ชื่อตาราง
คุณสามารถระบุตารางข้อมูลที่กำหนดเองในโมเดลโดยการกำหนดคุณสมบัติ table ดังนี้:
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
Eloquent มักถือว่าทุกตารางข้อมูลมีคอลัมนีมีนามว่า id คุณสามารถกำหนดคุณสมบัติ $primaryKey ที่มีการป้องกันไว้เพื่อเขียนทับข้อตกลงนี้:
```php
class User extends Model
{
    /**
     * กำหนดคีย์หลัก โดยค่าเริ่มต้นคือ id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent มักถือว่าคีย์หลักเป็นค่าจำนวนเติมเพิ่มโดยปริยายนี้หมายความว่าคีย์หลักจะถูกแปลงอัตโนมัติเป็นชนิด int หากคุณต้องการใช้คีย์ที่ไม่เพิ่มขึ้นหรือไม่มีนามว่าหรือไม่คุณจำเป็นต้องกำหนดค่าค่ำงกต $incrementing เป็นเท็จ
```php
class User extends Model
{
    /**
     * ระบุว่าคีย์หลักของโมเดลเพิ่งเพิ่มขึ้นอัตโนมัติหรือไม่
     *
     * @var bool
     */
    public $incrementing = false;
}
```

หากคีย์หลักของคุณไม่ใช่จำนวนเติมเพิ่มขึ้นคุณต้องกำหนดค่าคุณสมบัติการป้องกัน $keyType เป็น string:
```php
class User extends Model
{
    /**
     * ประเภท ID ที่เพิ่มขึ้นอัตโนมัติ
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## ปรับแต่ง Timestamp
โดยค่าเริ่มต้น Eloquent คาดหวังว่าตารางข้อมูลของคุณจะมี created_at และ updated_at หากคุณไม่ต้องการให้ Eloquent จัดการคอลัมนีมีนามด้านล่างสอดคล้องกัน โปรดกำนหนดค่าคุณสมบัติ $timestamps ในโมเดลเป็นเท็จ:
```php
class User extends Model
{
    /**
     * บ่งชี้ว่าควรบำรุง timestamp โดยอัตโนมัติหรือไม่
     *
     * @var bool
     */
    public $timestamps = false;
}
```
หากคุณต้องการปรับแต่งรูปแบบการเก็บ timestamp ในโมเดลของคุณ คุณสามารถตั้งค่าคุณแสมบัติ $dateFormat ในโมเดล เพียงนี้คุณสามารถกำหนดวิธีการเก็บข้อมูลของวันที่ในฐานข้อมูลและรูปแบบ JSON การแปลงโมเดลเป็นอาร์เรย์:
```php
class User extends Model
{
    /**
     * วิธีการเก็บ timestamp
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

หากคุณต้องการปรับแต่งชื่อฟิลด์ที่เก็บ timestamp คุณสามารถกำหนดค่าคงที่ CREATED_AT และ UPDATED_AT ในโมเดล:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## การเชื่อมต่อฐานข้อมูล
โดยค่าเริ่มต้น โมเดล Eloquent จะใช้การเชื่อมต่อฐานข้อมูลที่ต้องการสำหรับแอปพลิเคชันของคุณ หากคุณต้องการให้โมเดลเชื่อมต่อกับการเชื่อมต่อที่แตกต่างกัน โปรดกำหนดค่าที่คุณสมบัติการเชื่อมต่อ $connection:
```php
class User extends Model
{
    /**
     * ชื่อการเชื่อมต่อของโมเดล
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## ค่าเริ่มต้นของคุณสมบัติ
หากคุณต้องการกำหนดค่าเริ่มต้นสำหรับคุณสมบัติบางอย่างของโมเดล คุณสามารถกำหนดค่าคุณสมบัติ $attributes ในโมเดล:
```php
class User extends Model
{
    /**
     * ค่าเริ่มต้นของโมเดล
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## การเรียกค้น
หลังจากคุณสร้างโมเดลและตารางฐานข้อมูลที่เกี่ยวข้อง คุณสามารถค้นข้อมูลจากฐานข้อมูลได้โดยใช้ Eloquent โมเดลเป็นเหมือน query builder ที่มีประสิทธิภาพสามารถใช้ในการค้นหาข้อมูลที่เกี่ยวข้องกัน ตัวอย่าง:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> เกริ่นัป: เนื่องจากโมเดล Eloquent ยังเป็น query builder  ด้วย คุณควรอ่าน [query builder](queries.md) ในการใช้วิธีทุกวิธี คุณสามารถใช้วิธีเหล่านี้ในการค้นหา Eloquent

## ผูกเพิ่มเงื่อนไข
เมื่อใช้เมทอด all ของ Eloquent จะให้ผลลัพธ์ที่เกี่ยวข้องกับทั้งหมด เนื่้อจากทุกโมเดล Eloquent ยังคือ query builder ดังนั้นคุณสามารถเพิ่มเงื่อนไขการค้นหาและใช้เมทอด get เพื่อรับผลการค้นหา:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## รีโหลดโมเดล
คุณสามารถใช้เมทอด fresh และ refresh เพื่อรีโหลดโมเดล การใช้ fresh จะทำการค้นข้อมูลโมเดลใหม่จากฐานข้อมูล โมเดลที่มีอยู่แล้วจะไม่ได้รับผลกระทบ:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

การใช้ refresh จะนำข้อมูลใหม่จากฐานข้อมูลมาผูกกับโมเดลที่มีอยู่ นอกจากนี้ความสัมพันธ์ที่โหลดไปแล้วจะถูกรีโหลด:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## คอลเล็กชัน
เมทอด all และ get ของ Eloquent สามารถค้นหาผลลัพธ์มากมายและรีเทินเป็น `Illuminate\Database\Eloquent\Collection` นั่นหมายความว่า `Collection` จะมีเมทอดช่วยเหลือมากมายที่ให้เราจัดการผลลัพธ์ Eloquent
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## ใช้ Cursor
เมทอด cursor ช่วยให้คุณสามารถใช้กับการหาข้อมูลในฐานข้อมูล โดยมันจะกระทำต่อการค้นหาเพียงครั้งเดียว ขณะที่ด้านล่างจะป้อนข้อมูลขนาดใหญ่ มันสามารถลดการใช้หน่วยความจำลงอย่างมาก:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

cursor จะรีเทิน `Illuminate\Support\LazyCollection` instance [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) ทำให้คุณสามารถใช้วิธีเชรียัโคลเล็กชันใน Laravel มากมาย โดยที่มันจะโหลดโมเดลหนึ่งในหน่สไหม่ในหน่สโหนน:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## ชุดค้นข้อมูลย่อย
Eloquent มีการสนับสนุนการค้นข้อมูลย่อยระดับสูง คุณสามารถใช้กับคำสั่งตั้งค่าการเลือก จากตารางที่เกี่ยวข้อง ตัวอย่างเช่น กำหนดว่าเราต้องการค้นข้อมูลทั้งหมดจากตารางปลายทาง destinations และหาชื่อเที่ยวบินล่าสุดที่มีเส้นทางไปถึงแต่ละจุดหมาย:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## เรียงลำดับตามการค้นข้อมูลย่อย
เช่นเดียวกับนั้น ฟังก์ชันเรียงลำดับ orderBy ของ query builder ยังสนับสนุนการค้นข้อมูลย่อย และสามารถใช้งานได้ดานฐานข้อมูลเพียงครั้งเดียว:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## ค้นหาโมเดล/ชุดค้นข้อมูล
นอกเหนือจากการค้นหาบันทึกทั้งหมดจากตารางที่ระบุแล้ว คุณยังสามารถใช้ find, first หรือ firstWhere เพื่อ
## ค้นหาคอลเลกชัน
คุณยังสามารถใช้เมธอด count、sum และ max จากตัวกำหนดคำสั่ง เพื่อดำเนินการกับคอลเลกชันได้ การใช้เมธอดเหล่านี้จะส่งค่าสกาลาร์ที่เหมาะสมและไม่ใช่อินสแทนซ์ของโมเดล:
```php
$count = app\model\Flight::where('active', 1)->count();

$max = app\model\Flight::where('active', 1)->max('price');
```

## การเพิ่มข้อมูล
เพื่อเพิ่มบันทึกใหม่ในฐานข้อมูล คุณจำเป็นต้องสร้างอินสแทนซ์ของโมเดลใหม่ก่อน กำหนดค่าให้กับอินสแทนซ์ แล้วเรียกใช้เมธอด save:
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
        // ตรวจสอบคำขอ

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

created_at และ updated_at จะถูกตั้งค่าโดยอัตโนมัติ (เมื่อเล็กมาส์โดลเป็นจริง) และไม่จำเป็นต้องกำหนดค่าด้วยตนเอง

## การปรับปรุง
เมธอด save ยังสามารถใช้ในการปรับปรุงโมเดลที่มีอยู่ในฐานข้อมูลได้อีกด้วย โดยเพื่อปรับปรุงโมเดล คุณต้องค้นหาก่อน กำหนดค่าสำหรับที่ต้องการปรับปรุง แล้วเรียกใช้เมธอด save อีกครั้ง นอกจากนี้ updated_at จะได้รับการปรับปรุงโดยอัตโนมัติด้วย จึงไม่จำเป็นต้องกำหนดค่าด้วยตนเอง:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## การปรับปรุงเป็นกลุ่ม
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## การตรวจสอบการเปลี่ยนแปลงของคุณสมบัติ
Eloquent มี isDirty, isClean และ wasChanged เมธอด เพื่อตรวจสอบสถานะภายในของโมเดล และกำหนดให้คุณทราบว่าคุณสมบัติมีการเปลี่ยนแปลงไปจากการโหลดเบื้องต้นหรือไม่ isDirty เมธอด จะกำหนดว่าคุณสมบัติได้รับการเปลี่ยนแปลงหรือไม่ คุณสามารถส่งชื่อที่คุณสมบัติเพื่อให้กำหนดได้ก็ได้ isClean เมธอด จะสลับกับ isDirty มันยังสามารถตั้งค่าคุณสมบัติกลุ่มหรือไม่ได้:
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
wasChanged เมธอด จะกำหนดว่าในรอบคำขอล่าสุดได้รับการเปลี่ยนกับคุณสมบัติหรือไม่ คุณยังสามารถส่งชื่อที่คุณสมบัติเพื่อให้กำหนดได้ด้วย:
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

## การมอบหมายแบบกลุ่ม
คุณยังสามารถใช้เมธอด create ในการบันทึกโมเดลใหม่ วิธีการนี้จะส่งคืนอินสแทนซ์ของโมเดล อย่างไรก็ตาม ก่อนที่คุณจะใช้ คุณต้องระบุ fillable หรือ guarded โดยใช้ fillable หรือ guarded เพราะโมเดล Eloquent ทั้งหมดจะไม่สามารถมอบหมายแบบกลุ่มได้โดยค่าเริ่มต้น
เมื่อผู้ใช้อาจจะส่งค่าพารามิเตอร์ HTTP ที่ไม่ได้คาดหวังเข้ามาและพารามิเตอร์นี้จะเปลี่ยนแปลงฟิลด์ในฐานข้อมูลที่คุณไม่ต้องการเปลี่ยน ช่องว่างแบบกลุ่มจะเกิดขึ้น เช่น: ผู้ใช้ที่ไม่ดีอาจจะส่งพารามิเตอร์ is_admin ผลลัพธ์ที่ตามมาก็จะเป็นการนำพารามิเตอร์นี้ไปให้เมธอด create ซึ่งทำให้ผู้ใช้สามารถยกระดับตัวเองเป็นผู้ดูแลระบบ
ดังนั้นก่อนที่คุณจะเริ่ม คุณควรระบุว่าฟิลด์ใดบนโมเดลสามารถมอบหมายแบบกลุ่มได้ คุณสามารถทำได้โดยการใช้ $fillable ที่อยู่ในโมเดล เช่น: ให้ใช้ Flight โมเดลเพื่อที่จะทำคุณสมบัติ name ที่อยู่บนโมเดลให้สามารถมอบหมายแบบกลุ่มได้:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * คุณสมบัติที่สามารถมอบหมายแบบกลุ่มได้
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```
หลังจากที่เรากำหนดค่าให้กับคุณสมบัติที่สามารถมอบหมายแบบกลุ่มได้แล้ว เราสามารถใช้เมธอด create เพื่อแทรกข้อมูลใหม่ลงในฐานข้อมูลได้ เมธอด create หนังย์คืนอินสแทนซ์ของโมเดลที่บันทึกไว้:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```
หากคุณมีอินสแทนซ์ของโมเดลแล้ว คุณสามารถส่งอาร์เรย์ไปให้กับเมธอด fill เพื่อมอบหมายค่า:
```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable สามารถถูกชมเป็น "รายการที่สามารถมอบหมายแบบกลุ่มได้" คุณสมบัติ $guarded สามารถถูกระบุได้ด้วย $guarded มันจะมาเรียกใช้ดูด้วยว่า "รายการที่ไม่อนุญาตให้มอบหมายแบบกลุ่ม" โปรดทราบ คุณสามารถใช้ $fillable หรือ $guarded เท่านั้นและไม่สามารถใช้พร้อมกับกันไปพร้อมกันได้ ตัวอย่างเช่นในตัวอย่างนี้ price จะมีคุณสมบัติทั้งหมดที่ไม่สามารถมอบหมายแบบกลุ่มกันได้:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * คุณสมบัติที่ไม่สามารถมอบหมายแบบกลุ่มได้
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

หากคุณต้องการที่จะทำให้สมบัติทั้งหมดสามารถมอบหมายแบบกลุ่มได้ คุณสามารถกำหนด $guarded ให้เป็นอาร์เรย์ที่ไม่ได้รับการกำหนดค่า:
```php
/**
 * คุณสมบัติที่ไม่สามารถมอบหมายแบบกลุ่มได้
 *
 * @var array
 */
protected $guarded = [];
```

## เมธอดสร้างอื่น ๆ
firstOrCreate / firstOrNew
ที่นี่มีสองเมธอดที่อาจจะใช้ในการทำการมอบหมายแบบกลุ่มคือ firstOrCreate และ firstOrNew ที่ firstOrCreate จะโดยการค้นหาข้อมูลในฐานข้อมูลจากคีย์ / ค่าที่กำหนด หากไม่พบโมเดลในฐานข้อมูลจะถูกแทรกตารางบันทึกหนึ่ง โดยครอบครองคีย์ชิ้นแรกลำดับและอาจแถบบันทึกที่สองได้
และถ้าไม่พบที่จอดโมเดลของ firstOrNew จะส่งคืนอินสแทนซ์โมเดลใหม่ได้ โปรดทราบว่า firstOrNew ที่ส่งคืนมอเช้าภายในตารางข้อมูลและคุณจำเป็นต้องเรียกใช้ก็เองทำการ save:

```php
// ค้นหาที่จอดโมเดลด้วยชื่อ ถ้าไม่พบให้สร้างใหม่...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// ค้นหาที่จอดโมเดลด้วยชื่อและ delayed และ arrival_time ถ้าไม่พบให้สร้างที่จอดใหม่...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// ค้นหาที่จอดโมเดลด้วยชื่อ ถ้าไม่พบให้สร้างอินสแทนซ์...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// ค้นหาที่จอดโมเดลด้วยชื่อและ delayed และ arrival_time ถ้าไม่พบให้สร้างอินสแทนซ์โมเดลใหม่...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

คุณอาจเจอสถานการณ์ที่ต้องการปรับปรุงโมเดลที่มีอยู่หรือสร้างโมเดลใหม่ที่ไม่มีอยู่ คุณสามารถใช้เมธอด updateOrCreate ในการทำซะหนึ่งเดียว คล้ายกับเมธอด firstOrCreate updateOrCreate จะถูกเก็บรอยลงในฐานข้อมูลดังนั้นไม่จำเป็นต้องร้องใช้ save:
```php
// ถ้ามีเที่ยวบินจาก โอ๊คแลนด์ ไปสานดีเอโก จะเหมือนรัฐ ราคาเป็น 99 ดอลลาร์
// ถ้าไม่ตรงตามโมเดลทราบพลิกฉลาดไปพื้นที่ใหม่
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego
