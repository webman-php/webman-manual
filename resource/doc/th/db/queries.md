# ตัวสร้างคิวรี่
## ดึงข้อมูลทั้งหมด
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function all(Request $request)
    {
        $users = Db::table('users')->get();
        return view('user/all', ['users' => $users]);
    }
}
```

## ดึงคอลัมน์ที่กำหนด
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## ดึงเพียงบรรทัดเดียว
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## ดึงคอลัมน์เดียว
```php
$titles = Db::table('roles')->pluck('title');
```
ระบุคอลัมน์ id เพื่อใช้เป็นดัชนี
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## ดึงค่าเดี่ยว (คอลัมน์)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## ลบค่าที่ซ้ำ
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## แบ่งรายการ
หากคุณต้องการจัดการกับรายการเรคคอร์ดฐานข้อมูลที่มีจำนวนมากมาย การดึงดูข้อมูลทั้งหมดในครั้งเดียวอาจจะใช้เวลานานและอาจทำให้มีความจำเจญเกินไป ในกรุณารอใช้ `chunkById` เพื่อเรียกข้อมูลทีละส่วน และส่งมันให้ฟังชัน โดยตัวอย่าง เราสามารถแบ่งข้อมูลตาราง `users` เป็นส่วนๆ โดยจะดึงส่วนละ 100 รายการ:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
คุณสามรถหยุดการดึงข้อมูลเป็นส่วนๆ โดยการส่งค่า false ในการอยุภายในฟังชัน
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // Process the records...

    return false;
});
```

> หมายเหตุ: อย่าลบข้อมูลในการดึงข้อมูลโปรด เพราะอาจทำให้บางข้อมูลไม่ได้ระบุไว้ในผลลัพธ์

## การรวม
ตัวสร้างคิวรี่ยังมีเมทอดการรวมต่างๆ เช่น `count`, `max`, `min`, `avg`, `sum` เป็นต้น
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## ตรวจสอบว่ามีรายการหรือไม่
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## นิพจน์ต้นฉบับ
Protoype
```php
selectRaw($expression, $bindings = [])
```
ในบางครั้งคุณอาจจะต้องการใช้นิพจน์ต้นฉบับในการคิวรี่ คุณสามารถใช้ `selectRaw()` เพื่อสร้างนิพจน์ต้นฉบับ:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

นอกจากนี้ยังมีเมทอดนิพจน์ต้นฉบับอื่น ๆ เช่น `whereRaw()`, `orWhereRaw()`, `havingRaw()`, `orHavingRaw()`, `orderByRaw()`, `groupByRaw()` เป็นต้น

`Db::raw($value)` ก็สามารถใช้สร้างนิพจน์ต้นฉบับได้ แต่มีความฤาะใดถึงการผูกพารามิเตอร์ ควรใช้ดีมอ่องปัญหาร์บดเกี่ยวข้อง SQL
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## สร้างคำสั่ง Join
```php
// join
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// leftJoin            
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// rightJoin
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// crossJoin    
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```

## คำสั่ง Union
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## คำสั่ง Where
Prototype
```php
where($column, $operator = null, $value = null)
```
พารามิเตอร์แรกคือชื่อคอลัมน์ พารามิเตอร์ที่สอง เป็นเครื่องหมายเปรียบเทียบที่ระบบฐานข้อมูลรองรับ หรือสามารถเป็นค่าที่ต้องการเปรียบเทียบกับคอลัมน์นั้น
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// เมื่อเครื่องหมายเปรียบเทียบเป็นเท่ากัน สามารถละได้เช่นเดียวกัน
$users = Db::table('users')->where('votes', 100)->get();

$users = Db::table('users')
                ->where('votes', '>=', 100)
                ->get();

$users = Db::table('users')
                ->where('votes', '<>', 100)
                ->get();

$users = Db::table('users')
                ->where('name', 'like', 'T%')
                ->get();
```

คุณยังสามารถส่งอาร์เรย์เงื่อนไขให้กับฟังก์ชั่น where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

เมื่อินกับ where และ orWhere โดยการส่งพารามิเตอร์เป็นคล้ายกัน
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

คุณสามรถส่งคลูเจอร์ให้กับ orWhere เป็นพารามิเตอร์แรก:
```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$users = Db::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();

```

whereBetween / orWhereBetween เช็คว่าค่าในคอลัมน์อยู่ระหว่างค่าที่ระบุ:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween เช็คว่าค่าในคอลัมน์ไม่อยู่ระหว่างค่าที่ระบุ:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn เปรียบเทียบค่าในคอลัมน์กับค่าในอาร์เรย์ที่ระบุ:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull เปรียบเทียบค่าในคอลัมน์กับ NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull เปรียบเทียบค่าในคอลัมน์กับ NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime เปรียบเทียบค่าในคอลัมน์กับวันที่ที่ได้รับ:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn เปรียบเทียบค่าในคอลัมน์กับค่าในคอลัมน์อื่น:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// คุณสามารถส่งตัวสร้างเปรียบเทียบค่าเข้าไปด้วย
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// เมทอด whereColumn ยังสามารถรับอาร์เรย์ได้
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

การจัดกลุ่มพารามิเตอร์
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
               $query->where('votes', '>', 100)
                     ->orWhere('title', '=', 'Admin');
           })
           ->get();
```

whereExists
```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$users = Db::table('users')
           ->whereExists(function ($query) {
               $query->select(Db::raw(1))
                     ->from('orders')
                     ->whereRaw('orders.user_id = users.id');
           })
           ->get();
```

## เรียงลำดับตาม
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```

## สุ่มการเรียงลำดับ
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> การสุ่มระเบียงมีผลกระทบต่อประสิทธิภาพของเซิร์ฟเวอร์มาก ไม่แนะนำให้ใช้

## การจัดกลุ่ม / ตรวง
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// คุณสามารถส่งพารามิเตอร์หลายตัวไปให้กับเมทอด groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## ชนิด / ขีดกลุ่ม
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```
## การแทรก
แทรกข้อมูลเพียงรายการเดียว
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
แทรกข้อมูลหลายรายการ
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## รับ ID ที่เพิ่มขึ้น
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> โปรดทราบ: เมื่อใช้ PostgreSQL  method insertGetId  จะเป็นค่าเริ่มต้นที่มอง id  ให้เป็นชื่อฟิลด์ที่เพิ่มขึ้นอัตโนมัติ  หากคุณต้องการรับ ID  จาก "ลำดับ"  อื่น ๆ  คุณสามารถส่งชื่อฟิลด์เป็นพารามิเตอร์ที่สองไปยัง method insertGetId 

## การอัปเดต
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## อัปเดตหรือแทรก
บางครั้งคุณอาจหวังจะอัปเดตรายการที่มีอยู่ในฐานข้อมูลหรือสร้างใหม่หากไม่มีรายการที่ตรงกัน:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
Method updateOrInsert  จะพยายามที่ใช้คีย์และค่าของพารามิเตอร์ที่หนึ่งในการค้นหารายการในฐานข้อมูลที่ตรงกัน หากมีรายการอยู่แล้ว จะใช้ค่าของพารามิเตอร์ที่สองในการอัปเดตรายการ หากไม่พบรายการ จะแทรกรายการใหม่ที่ข้อมูลของทั้งสองอาร์เรย์

## เพิ่ม & ลด
method เหล่านี้รับพารามิเตอร์ที่แสดงถึงคอลัมน์ที่ต้องการเปลี่ยนแปลง พารามิเตอร์ที่สองเป็นไม่บังคับ ใช้เพื่อควบคุมปริมาณการเพิ่มหรือลด:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
คุณยังสามารถระบุฟิลด์ที่ต้องการอัปเดตในขณะที่ดำเนินการ:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## การลบ
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
หากคุณต้องการลบข้อมูลในตารางคุณสามารถใช้เมธอด truncate  ซึ่งจะลบแถวทั้งหมดและรีเซ็ต ID เพิ่มขึ้นเป็นศูนย์:
```php
Db::table('users')->truncate();
```

## ล็อกสิทธิ์
Query Builder  ยังรวมเมทอดที่ช่วยให้คุณสามารถทำ "ล็อกเพื่อรอ"  ในการเข้าถึงข้อมูลด้วยภาษาคำ"เลือก"  ถ้าคุณต้องการทำ "ล็อกเพื่อรอ"  ในการเข้าถึงข้อมูล คุณสามารถใช้เมธอด sharedLock  ข้อมูลที่ถูกล็อก "เพื่อรอ" ไม่สามารถถูกเปลี่ยนแปลงจนกว่าธรรมขัยจะถูกส่ง:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
หรือคุณสามารถใช้เมธอด lockForUpdate  คุณสามารถหลบ "ล็อกเพื่อรอ" โดยใช้ทิศทาง "อัปเดต"  คุณก็จะได้ข้อมูลที่ถูกล็อก "เพื่อรอ" ไม่ถูกเปลี่ยนแปลงหรือถูกเลือก:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## การแก้ไข
คุณสามารถใช้ dd ขณะหรือ dump method  ที่จะแสดงผลลัพธ์ในการค้นหาหรือคำสั่ง SQL  การใช้ dd  จะแสดงข้อมูลในโหมดดีบักและหยุดการทำร้องขอ การใช้ dump  ยังสามารถแสดงข้อมูลการแก้ไข  แต่จะไม่หยุดการทำงานของการร้องขอ:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **โปรดทราบ**
> การแก้ไขต้องการการติดตั้ง `symfony/var-dumper`  คำสั่งคือ `composer require symfony/var-dumper`
