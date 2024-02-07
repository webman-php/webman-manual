## ThinkORM

### การติดตั้ง ThinkORM

`composer require -W webman/think-orm`

หลังจากติดตั้งเสร็จ จะต้อง restart หรือ รีโหลด(reload ไม่มีผล)

> **คำแนะนำ**
> หากติดตั้งไม่สำเร็จ อาจเป็นเพราะคุณใช้代理ของ composer ลองรัน `composer config -g --unset repos.packagist` เพื่อยกเลิกการใช้งาน代理ของ composer 

> [webman/think-orm](https://www.workerman.net/plugin/14) ที่จริงๆแล้วเป็นปลั๊กอินสำหรับการติดตั้งโมเดล `toptink/think-orm` ถ้าเว็บแมนของคุณเวอร์ชั่นต่ำกว่า `1.2` ไม่สามารถใช้ปลั๊กอินได้ โปรดอ่านบทความ [การติดตั้งและกำหนดค่า think-orm ด้วยตัวเอง](https://www.workerman.net/a/1289) สำหรับข้อมูลเพิ่มเติม

### ไฟล์การกำหนดค่า
ปรับแต่งไฟล์การกำหนดค่าตามสถานการณ์จริง ที่ `config/thinkorm.php`

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

โมเดลของ ThinkOrm สืบทอดมาจาก `think\Model` เช่นดังต่อไปนี้
```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * ตารางที่เกี่ยวข้องกับโมเดลนี้
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * คีย์หลักที่เกี่ยวข้องกับตาราง
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

คุณยังสามารถใช้คำสั่งต่อไปนี้เพื่อสร้างโมเดลที่ใช้กับ thinkorm
```sh
php webman make:model ชื่อตาราง
```

> **คำแนะนำ**
> คำสั่งนี้ต้องการการติดตั้ง `webman/console` คำสั่งการติดตั้งคือ `composer require webman/console ^1.2.13`

> **ข้อควรระวัง**
> ถ้าคำสั่ง `make:model` ตรวจพบว่าโปรเจ็กต์หลักใช้ `illuminate/database` จะสร้างไฟล์โมเดลที่ใช้กับ `illuminate/database` แทนที่จะใช้กับ thinkorm ในกรณีนี้ คุณสามารถใช้พารามิเตอร์เพิ่มเติม `tp` เพื่อสร้างโมเดลที่ใช้กับ think-orm โดยใช้คำสั่งเช่น `php webman make:model ชื่อตาราง tp` (หากไม่ทำงานโปรดอัปเกรด `webman/console`)

