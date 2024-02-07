## webman/push

`webman/push` เป็นปลัูกอินระบบการป้อนข้อมูลที่ฟรี ซึ่งมีลูกค้าที่ใช้ระบบการสมัครสมาชิก สามารถทำงานร่วมกับ [pusher](https://pusher.com) และมีลูกค้าที่มีหลายภาษาเช่น JS, แอนดรอยด์ (Java), iOS (Swift), iOS (Obj-C), uniapp, .NET, Unity, Flutter, AngularJS และอื่น ๆ โดยต่อมากับ SDK การป้อนข้อมูลทางหลังรับรอง PHP, Node, Ruby, Asp, Java, Python, Go, Swift และอื่น ๆ ลูกค้ามากมายที่เข้าถึงหน้าจอเดียวกัน และต่อใช้ง่ายและมีคุณภาพสูง สามารถนำไปใช้สำหรับการป้อนข้อมูล, พูดคุยและอื่น ๆ ในสถานะเรียลไทม์หลายสถานการณ์

อีกทั้ง ปลัูกอินมีลูกค้า js ที่มาพร้อมเองชื่อ push.js เพื่อ uniapp มีลูกค้า uniapp-push.js, ลูกค้าทั้งหมดอื่น ๆ สามารถดาวน์โหลดได้ที่ https://pusher.com/docs/channels/channels_libraries/libraries/

> ปลัูกอินต้องการ webman-framework>=1.2.0

## การติดตั้ง

```sh
composer require webman/push
```
## ไคลเอนต์ (จาวาสคริปต์)

**นำเข้าไคลเอนต์จาวาสคริปต์**
```js
<script src="/plugin/webman/push/push.js"> </script>
```

**การใช้งานไคลเอนต์ (ช่องส่วนทุกคน)**
```js
// สร้างการเชื่อมต่อ
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // ที่อยู่ของเว็บเซิร์ฟเวอร์
    app_key: '<คีย์แอปพลิเคชัน ในไฟล์ config/plugin/webman/push/app.php>', 
    auth: '/plugin/webman/push/auth' // การตรวจสอบสิทธิ์การสมัครสมาชิก (สำหรับช่องส่วนบุคคลเท่านั้น)
});
// สมมติว่า รหัสผู้ใช้คือ 1
var uid = 1;
// เบราว์เซอร์รับฟังก์ชันของข้อมูลในช่องที่ชื่อ user-1, หมายความว่าข้อความของผู้ใช้ที่มี uid เป็น 1
var user_channel = connection.subscribe('user-' + uid);

// เมื่อมีเหตุการณ์ของข้อความในช่อง user-1
user_channel.on('message', function(data) {
    // ข้อมูลที่ได้เป็นเนื้อหาข้อความ
    console.log(data);
});
// เมื่อมีเหตุการณ์ friendApply ในช่อง user-1
user_channel.on('friendApply', function (data) {
    // ข้อมูลที่ได้เป็นข้อมูลที่เกี่ยวกับการสมัครเป็นเพื่อน
    console.log(data);
});

// สมมติว่า รหัสของกลุ่ม คือ 2
var group_id = 2;
// เบราว์เซอร์รับฟังก์ชันของข้อมูลในช่อง group-2, หมายความว่ารับข้อความของกลุ่ม 2
var group_channel = connection.subscribe('group-' + group_id);
// เมื่อกลุ่ม 2 มีเหตุการณ์ของข้อความ
group_channel.on('message', function(data) {
    // ข้อมูลที่ได้เป็นเนื้อหาข้อความ
    console.log(data);
});
```

> **เคล็ดลับ**
> ในตัวอย่างข้างต้น subscribe ใช้ในการสมัครช่อง, `message` `friendApply` คือเหตุการณ์ในช่อง. ช่องและเหตุการณ์เป็นสตริงอย่างอิสระ, ไม่จำเป็นต้องกำหนดล่วงหน้าในเซิร์ฟเวอร์.

## การแพร่ส่งทางเซิร์ฟเวอร์ (PHP)
```php
use Webman\Push\Api;
$api = new Api(
    // สามารถใช้ config เพื่อรับการตั้งค่าใน webman โดยตรง, สำหรับสภาพแวดล้อมนอก webman จะต้องระบุการตั้งค่าเอง
    'http://127.0.0.1:3232',
    config('plugin.webman.push.app.app_key'),
    config('plugin.webman.push.app.app_secret')
);
// ส่งเหตุการณ์ข้อความไปยังลูกค้าที่สมัครช่อง user-1 ทั้งหมด
$api->trigger('user-1', 'message', [
    'from_uid' => 2,
    'content'  => 'สวัสดี, นี่คือเนื้อหาข้อความ'
]);
```
## ช่องส่วนตัว
ในตัวอย่างข้างต้นนี้ผู้ใช้ทุกคนสามารถสมัครสมาชิกข้อมูลผ่าน Push.js ถ้าข้อมูลเป็นข้อมูลที่อ่อนไหว การทำเช่นนี้จะไม่ปลอดภัย

`webman/push` รองรับการสมัครสมาชิกช่องส่วนตัว ซึ่งช่องส่วนตัวจะเริ่มต้นด้วย `private-` ตัวอย่างเช่น
```js
var connection = new Push({
    url: 'ws://127.0.0.1:3131', // ที่อยู่เว็บส็อค
    app_key: '<app_key>',
    auth: '/plugin/webman/push/auth' // การตรวจสอบสมัครสมาชิก (เฉพาะช่องส่วนตัวเท่านั้น)
});

// สมมติว่าผู้ใช้มี uid เป็น 1
var uid = 1;
// เบราว์เซอร์ติดตามข้อความในช่องส่วนตัว private-user-1
var user_channel = connection.subscribe('private-user-' + uid);
```

เมื่อลูกค้าสมัครสมาชิกช่องส่วนตัว (`ช่องที่ขึ้นต้นด้วย private-`) เบราว์เซอร์จะดำเนินการส่งคำขอยืนยันผ่าน ajax (ที่อยู่ ajax คือการตรวจสอบสมัครสมาชิกที่กำหนดไว้ในพารามิเตอร์ auth ในการสร้าง Push) ผู้พัฒนาสามารถตรวจสอบว่าผู้ใช้ปัจจุบันมีสิทธิ์ในการติดตามช่องนี้หรือไม่ จึงทำให้การสมัครสมาชิกเป็นที่ปลอดภัย

> เรื่องการยืนยันผ่านดูที่ `config/plugin/webman/push/route.php`

## การส่งข้อมูลจากลูกค้า
ตัวอย่างข้างต้นคือการสมัครสมาชิกช่องใดช่องหนึ่งของลูกค้า และการเข้าถึง API จากเซิร์ฟเวอร์  webman/push ยังรองรับการส่งข้อมูลโดยตรงจากลูกค้า

> **โปรดทราบ**
> การส่งโดยตรงรองรับเฉพาะช่องส่วนตัว (ช่องที่ขึ้นต้นด้วย `private-`) และลูกค้าสามารถเรียกร้องเหตุการณ์ที่ขึ้นต้นด้วย `client-` เท่านั้น

ตัวอย่างการเรียกร้องเหตุการณ์จากลูกค้า
```js
var user_channel = connection.subscribe('private-user-1');
user_channel.on('client-message', function (data) {
    // 
});
user_channel.trigger('client-message', {form_uid:2, content:"hello"});
```

> **โปรดทราบ**
> โค้ดข้างต้นจะส่งข้อมูลการเรียกเหตุการณ์ `client-message` ให้กับลูกค้าที่สมัครสมาชิก `private-user-1` ทุกคนโดยไม่รวมลูกค้าปัจจุบัน (ลูกค้าที่ส่งข้อมูลจะไม่ได้รับข้อมูลที่ส่ง)
webhooks

webhook ใช้สำหรับรับเหตุการณ์บางอย่างในช่อง

**ขณะนี้มีเหตุการณ์หลัก 2 ประการ :**

- 1 channel_added เมื่อเกิดเหตุการณ์ที่มีผู้ใช้ไม่ได้ออนไลน์ไปยังการเป็นผู้ใช้ออนไลน์ หรือเรียกว่าเหตุการณ์ออนไลน์

- 2 channel_removed เมื่อทุกผู้ใช้ในช่องหนึ่งปิดออฟไลน์เป็นเหตุการณ์หรือออฟไลน์  เหตุการณ์

> **เคล็ดลับ**
> เหตุการณ์เหล่านี้เป็นประโยชน์มากในการดูแลสถานะผู้ใช้ออนไลน์

> **การที่เป็นสําคัญ**
> ที่อยู่ของ webhook จะถูกกําหนดใน `config/plugin/webman/push/app.php`
> รหัสจัดการเหตุการณ์ webhook อ้างอิงจากโค้ดใน `config/plugin/webman/push/route.php`
> เนื่องจากการรีเฟรชหน้าจอทําให้ผู้ใช้ออฟไลน์ชั่วคราวไม่ควรถือเป็นออฟไลน์  webman/push จะใช้การตรวจสอบดืลาดือ เหตุการณ์ออนไลน์/ออฟไลน์จะมีการล่าช้า 1-3 วินาที

## พร็อกซี่ wss (SSL)
ไม่สามารถใช้การเชื่อมต่อ ws ใน https จึงจำเป็นต้องใช้การเชื่อมต่อ wss ช่วงเหตุการณ์นี้สามารถใช้ nginx เป็นพร็อกซี่ของ wss ดังตัวอย่างที่ตามนี้:

``` ini
server {
    # .... การตั้งค่าอื่น ๆ ที่ไม่ได้แสดงค่าที่นี่ ...
    
    location /app/<app_key>
    {
        proxy_pass http://127.0.0.1:3131;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";
        proxy_set_header X-Real-IP $remote_addr;
    }
}
```

**โปรดทราบ** config ที่อยู่ในตัวกำหนดไว้ตั้งแต่ `config/plugin/webman/push/app.php`

เมื่อ restart nginx ต่อ server ดังกล่าว:

``` ini
var connection = new Push({
    url: 'wss://example.com',
    app_key: '<app_key ตามที่ระบุไว้ใน config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // ตรวจสอบสิทธิ์การสมัคร (สำหรับช่องส่วนตัวเท่านั้น)
});
```

> **โปรดระวัง**
> 1. URL จะเริ่มต้นด้วย wss
> 2. ไม่ต้องระบุพอร์ต
> 3. ต้องใช้การเชื่อมต่อโดยใช้**ชื่อโดเมนที่มีรับรองด้วยใบรับรอง SSL**
## คำแนะนำการใช้งาน push-vue.js

1. คัดลอกไฟล์ push-vue.js ไปยังไดเรกทอรีโปรเจค เช่น src/utils/push-vue.js

2. นำไฟล์ที่ได้มานำเข้าในหน้า Vue
```js

<script lang="ts" setup>
import {  onMounted } from 'vue'
import { Push } from '../utils/push-vue'

onMounted(() => {
  console.log('คอมโพเน้นที่ถูกติดตั้ง') 

  // สร้างอินสแตนซ์ของ webman-push

  // สร้างการเชื่อมต่อ
  var connection = new Push({
    url: 'ws://127.0.0.1:3131', // ที่อยู่ของเว็บเซิร์ฟเวอร์
    app_key: '<app_key ที่ได้รับจาก config/plugin/webman/push/app.php>',
    auth: '/plugin/webman/push/auth' // การตรวจสอบสิทธิ์สำหรับการสมัครสมาชิก (สำหรับช่องที่เป็นส่วนตัวเท่านั้น)
  });

  // สมมติว่า uid ของผู้ใช้คือ 1
  var uid = 1;
  // บราวเซอร์บันทึกข้อความในช่องชื่อ user-1 นั้นคือข้อความของผู้ใช้ที่ uid เป็น 1
  var user_channel = connection.subscribe('user-' + uid);

  // เมื่อช่อง user-1 มีการเกิดเหตุการณ์ message
  user_channel.on('message', function (data) {
    // ข้อมูลในนั้นคือเนื้อหาของข้อความ
    console.log(data);
  });
  // เมื่อช่อง user-1 มีเหตุการณ์ friendApply
  user_channel.on('friendApply', function (data) {
    // ข้อมูลในนั้นคือข้อมูลที่เกี่ยวกับการสมัครเป็นเพื่อน
    console.log(data);
  });

  // สมมติว่า id ของกลุ่มคือ 2
  var group_id = 2;
  // บราวเซอร์บันทึกข้อความในช่องชื่อ group-2 นั้นคือข้อความของกลุ่มที่มี id เป็น 2
  var group_channel = connection.subscribe('group-' + group_id);
  // เมื่อกลุ่ม 2 มีเหตุการณ์ message
  group_channel.on('message', function (data) {
    // ข้อมูลในนั้นคือเนื้อหาของข้อความ
    console.log(data);
  });
})

</script>
```

## ที่อยู่ของไคลเอนต์อื่น ๆ
`webman/push` เข้ากันได้กับ pusher, ไคลเอนต์ภาษาอื่น (Java Swift .NET Objective-C Unity Flutter Android  IOS AngularJS ฯลฯ) สามารถดาวน์โหลดได้ที่: https://pusher.com/docs/channels/channels_libraries/libraries/
