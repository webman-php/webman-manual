# การแพ็ค phar

phar เป็นไฟล์บีบอัดใน PHP ที่คล้ายกับ JAR คุณสามารถใช้ phar เพื่อแพ็คเว็บมันโปรเจคของคุณเป็นไฟล์ phar เดียวเพื่อง่ายต่อการใช้งาน

**ขอขอบคุณ [fuzqing](https://github.com/fuzqing) สำหรับการ PR นี้**

> **โปรดทราบ**
> ต้องปิดการตั้งค่า phar ใน `php.ini` โดยตั้งค่า `phar.readonly = 0`

## การติดตั้งเครื่องมือคำสั่ง
`composer require webman/console`

## การตั้งค่า
เปิดไฟล์ `config/plugin/webman/console/app.php` และตั้งค่า `'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'` เพื่อไม่รวมไดเรคทอรี่และไฟล์ที่ไม่จำเป็นเข้าในการแพ็ค เพื่อป้องกันไฟล์ phar ที่ใหญ่เกินไป

## การแพ็ค
ในโฟลเดอร์หลักของเว็บมัน ให้รันคำสั่ง `php webman phar:pack` จะสร้างไฟล์ `webman.phar` ในโฟลเดอร์ build

> การตั้งค่าการแพ็คอยู่ใน `config/plugin/webman/console/app.php`

## คำสั่งเริ่มระงับ
**เริ่ม**
`php webman.phar start` หรือ `php webman.phar start -d`

**หยุด**
`php webman.phar stop`

**ดูสถานะ**
`php webman.phar status`

**ดูสถานะการเชื่อมต่อ**
`php webman.phar connections`

**รีสตาร์ท**
`php webman.phar restart` หรือ `php webman.phar restart -d`

## คำอธิบาย
* เมื่อเรียกใช้ webman.phar จะสร้างโฟลเดอร์ runtime ในโฟลเดอร์ที่มี webman.phar เพื่อทำเก็บไฟล์ล็อกและไฟล์ชั่วคราวอื่น ๆ
* หากโปรเจคของคุณมีไฟล์ .env คุณต้องวางไฟล์ .env ไว้ในโฟลเดอร์ที่มี webman.phar
* หากธุรกิจของคุณต้องการที่จะอัพโหลดไฟล์ไปยังไดเร็คทอรี่ public คุณต้องทำการแยกไดเรคทอรี่ public และวางไปในโฟลเดอร์ที่มี webman.phar โดยนี้จะต้องตั้งค่า `config/app.php`
```'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',```
เพื่อให้ธุรกิจสามารถใช้ฟังก์ชันช่วยเหลือ `public_path()` เพื่อหาตำแหน่งจริงของไดเรคทอรี่ public
* webman.phar ไม่รองรับการเปิดเซิร์ฟเวอร์ขณะที่ใช้ประสิทธิภาพที่กำหนดเองใน Windows
