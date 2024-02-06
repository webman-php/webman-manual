## ThinkORM

### ติดตั้ง ThinkORM

`composer require -W webman/think-orm`

หลังจากติดตั้งเสร็จ จะต้องrestart (การโหลดใหม่ไม่ถูกต้อง)

> **คำแนะนำ**
> หากการติดตั้งล้มเหลว อาจเป็นเพราะคุณใช้โปรกุมเมอร์แทน ลองใช้คำสั่ง `composer config -g --unset repos.packagist` เพื่อยกเลิกโปรกุมเมอร์แล้วลองใหม่

> [webman/think-orm](https://www.workerman.net/plugin/14)  ส่วนใหญ่มีการติดตั้ง`toptink/think-orm` อัตโนมัติ หากเวอร์ชันของ webman ต่ำกว่า `1.2` ไม่สามารถใช้ปลั๊กอิน กรุณาอ่านบทความ [การติดตั้งและกำหนดค่า think-orm ด้วยตัวคุณเอง](https://www.workerman.net/a/1289) สำหรับรายละเอียดเพิ่มเติม

### แก้ไขไฟล์การกำหนดค่า
แก้ไขไฟล์การกำหนดค่าตามสถานการณ์จริง `config/thinkorm.php`

### การใช้งาน

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### สร้างโมเดล

โมเดลของ ThinkOrm สืบทอดจาก `think\Model` เช่นดังนี้
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

คุณก็สามารถสร้างโมเดลที่ใช้ thinkorm ดังนี้
```
php webman make:model ชื่อตาราง
```
> **คำแนะนำ**
> คำสั่งนี้ต้องการการติดตั้ง `webman/console` สำหรับคำสั่งการติดตั้งคือ `composer require webman/console ^1.2.13`

> **ข้อควรระวัง**
> ถ้าคำสั่ง make:model ตรวจพบว่าโปรเจคหลักใช้ `illuminate/database` จะสร้างไฟล์โมเดลที่ใช้ `illuminate/database` แทนที่จะใช้ thinkorm และสามารถใช้พารามิเตอร์เพิ่มเพื่อสร้างโมเดลที่ใช้ think-orm ด้วย คำสั่งจะมีลักษณะ `php webman make:model ชื่อตาราง tp` (หากไม่มีผลกรุณาอัพเกรด `webman/console`)
