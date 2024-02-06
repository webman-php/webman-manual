# สคริปต์ที่กำหนดเอง

บางครั้งเราต้องเขียนสคริปต์ชั่วคราวบางอย่างที่สามารถเรียกใช้คลาสหรืออินเทอร์เฟซต่าง ๆ เช่นเดียวกับ webman เพื่อทำงานเช่นการนำเข้าข้อมูล อัพเดตข้อมูล หรือการสถิติต่าง ๆ นั่นทำได้โดยง่ายใน webman ตัวอย่างเช่น:

**สร้าง `scripts/update.php`** (หากโฟลเดอร์ไม่มีให้สร้างเอง)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

แน่นอนเรายังสามารถใช้`webman/console` เพื่อสร้างคำสั่งที่กำหนดเองเพื่อทำการดังกล่าว ดูตัวอย่างเพิ่มเติมได้ที่ [Command Line](../plugin/console.md)
