## คิว Stomp

Stomp เป็นโปรโตคอลข้อความที่เป็นข้อความแบบข้อความที่เชื่อมต่อโดยแบบโครงสร้างข้อมูลเรียบง่าย (text-oriented messaging protocol) ซึ่งมีรูปแบบการเชื่อมต่อที่สามารถปฏิบัติงานร่วมกับการเชื่อมต่อ ทำให้ไคลเอ็นต์ STOMP สามารถโต้ตอบกับโบรกเกอร์ข้อความของ STOMP ได้ทุกรูปแบบ [workerman/stomp](https://github.com/walkor/stomp) ใช้สำหรับไคลเอนต์ STOMP ซึ่งใช้สำหรับทำงานกับคิวข้อความเช่น RabbitMQ, Apollo, ActiveMQ และอื่น ๆ

## การติดตั้ง
`composer require webman/stomp`

## การกำหนดค่า
ไฟล์กำหนดค่าจะอยู่ที่ `config/plugin/webman/stomp`

## ส่งข้อความ
```php
<?php
namespace app\controller;

use support\Request;
use Webman\Stomp\Client;

class Index
{
    public function queue(Request $request)
    {
        // คิว
        $queue = 'examples';
        // ข้อมูล (หากต้องการส่งข้อมูลแบบอาร์เรย์จะต้องทำการซีเรียไลซ์เอง เช่น json_encode, serialize เป็นต้น)
        $data = json_encode(['to' => 'tom@gmail.com', 'content' => 'hello']);
        // ส่งข้อมูล
        Client::send($queue, $data);

        return response('redis queue test');
    }

}
```
> เพื่อให้เข้ากันได้กับโปรเจ็กต์อื่น ๆ  Componemt Stomp ไม่ได้มีการซีเรียไลซ์และดีซีเรียไลซ์แบบอัตโนมัติ หากต้องการส่งข้อมูลแบบอาร์เรย์จะต้องทำการซีเรียไลซ์เอง และ ในขณะที่กินจะต้องทำการดีซีเรียไลซ์เองด้วย

## การบริโภคข้อความ
สร้าง `app/queue/stomp/MyMailSend.php` (ชื่อคลาสสามารถเลือกได้ตามต้องการ ตรงตามพร้อมพัทค่า PSR4 คือได้)。
```php
<?php
namespace app\queue\stomp;

use Workerman\Stomp\AckResolver;
use Webman\Stomp\Consumer;

class MyMailSend implements Consumer
{
    // ชื่อคิว
    public $queue = 'examples';

    // ชื่อการเชื่อมต่อ ที่สอดคล้องกับการเชื่อมต่อใน stomp.php`
    public $connection = 'default';

    // หากเป็น client จำเป็นต้องเรียก $ack_resolver->ack() เพื่อแจ้งให้เซิฟเวอร์ทราบว่าการบริโภคสำเร็จ
    // หากเป็น auto ไม่จำเป็นต้องเรียก $ack_resolver->ack()
    public $ack = 'auto';

    // บริโภค
    public function consume($data, AckResolver $ack_resolver = null)
    {
        // หากข้อมูลเป็นอาร์เรย์จำเป็นต้องทำการดึงเอาเซียไรและดีซีเรียไลซ์เอง
        var_export(json_decode($data, true)); // ผลลัพธ์ ['to' => 'tom@gmail.com', 'content' => 'hello']
        // แจ้งเซิฟเวอร์ ที่บริโภคสำเร็จ
        $ack_resolver->ack(); // อ็คเป็น auto สามารถละเลยการเรียกใช้
    }
}
```

# เปิดใช้งานโปรโตคอล stomp ของ rabbitmq
rabbitmq ไม่ได้เปิดใช้งานโปรโตคอล stomp แบบค่าเริ่มต้น จึงต้องทำการเปิดด้วยคำสั่งต่อไปนี้
```
rabbitmq-plugins enable rabbitmq_stomp
```
หลังจากเปิดใช้งาน พอร์ตของ stomp จะเป็น 61613 โดยค่าเริ่มต้น
