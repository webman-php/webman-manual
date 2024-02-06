# การบีบอัดด้วย phar

phar เป็นไฟล์บีบอัดใน PHP ที่คล้ายกับ JAR คุณสามารถใช้ phar เพื่อบีบอัดโปรเจ็กต์ webman ของคุณเป็นไฟล์ phar เดียวเพื่อความสะดวกในการติดตั้ง

**ขอขอบคุณ [fuzqing](https://github.com/fuzqing) สำหรับการ PR ครับ.**

> **โปรดทราบ**
> คุณต้องปิดการตั้งค่า phar ใน `php.ini` โดยตั้งค่า `phar.readonly = 0`

## การติดตั้งเครื่องมือคำสั่ง
`composer require webman/console`

## การตั้งค่า
เปิดไฟล์ `config/plugin/webman/console/app.php` และตั้งค่า `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` เพื่อยกเว้นไดเร็กทอรีและไฟล์ที่ไม่จำเป็นออกจากการบีบอัด เพื่อลดขนาดของไฟล์ที่บีบอัด

## การบีบอัด
ในไดเร็กทอรีหลักของโปรเจ็กต์ webman ให้รันคำสั่ง `php webman phar:pack`
จะสร้างไฟล์ `webman.phar` ที่ไดเร็กทอรี build

> การตั้งค่าที่เกี่ยวข้องกับการบีบอัดอยู่ในไฟล์ `config/plugin/webman/console/app.php`

## คำสั่งเริ่มต้นและหยุด
**เริ่มต้น**
`php webman.phar start` หรือ `php webman.phar start -d`

**หยุด**
`php webman.phar stop`

**ตรวจสอบสถานะ**
`php webman.phar status`

**ตรวจสอบสถานะการเชื่อมต่อ**
`php webman.phar connections`

**รีสตาร์ท**
`php webman.phar restart` หรือ `php webman.phar restart -d`

## คำอธิบาย
* เมื่อเรียกใช้ webman.phar จะสร้างไดเร็กทอรี runtime ในตำแหน่งที่ webman.phar อยู่ เพื่อเก็บไฟล์ล็อกและไฟล์ชั่วคราว

* หากโปรเจ็กต์ของคุณใช้ไฟล์ .env คุณต้องตั้ง .env ไว้ในตำแหน่งที่ webman.phar อยู่

* หากงานของคุณต้องการอัปโหลดไฟล์ไปยังไดเร็กทอรี public คุณต้องตั้งค่า public path ให้แยกไว้ในตำแหน่งที่ webman.phar อยู่ ในที่นี้คุณต้องตั้งค่า `config/app.php` ดังนี้
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
งานสามารถใช้ฟังก์ชันช่วยเหลือ `public_path()` เพื่อหาตำแหน่งที่ตั้งของไดเร็กทอรี public จริง

* webman.phar ไม่สนับสนุนการเรียกใช้กระบวนการที่กำหนดเองใน Windows
