# การบีบอัดไฟล์ไบนารี

webman รองรับการบีบอัดโปรเจคเป็นไฟล์ไบนารี ซึ่งทำให้ webman สามารถทำงานบนระบบ Linux โดยไม่จำเป็นต้องมีโปรแกรม PHP

> **หมายเหตุ**
> ไฟล์หลังการบีบอัดสามารถใช้งานได้เฉพาะบนระบบ Linux ที่ใช้กลุ่มคำสั่ง x86_64 เท่านั้น และไม่สามารถใช้งานบนระบบ mac ได้
> ต้องปิดการตั้งค่า phar ใน `php.ini` โดยการตั้งค่า `phar.readonly = 0`

## ติดตั้งเครื่องมือคำสั่ง
`composer require webman/console ^1.2.24`

## การตั้งค่า
เปิดไฟล์ `config/plugin/webman/console/app.php` และตั้งค่า
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
เพื่อการบีบอัดโดยการตัดออกไดเร็กทอรีและไฟล์ที่ไม่จำเป็นออกเพื่อลดขนาดของไฟล์ที่บีบอัด

## บีบอัด
รันคำสั่ง
```
php webman build:bin
```
โดยสามารถระบุเวอร์ชั่น PHP เพื่อการบีบอัด เช่น
```
php webman build:bin 8.1
```

หลังจากการบีบอัดจะสร้างไฟล์ `webman.bin` ในไดเร็กทอรี build

## เริ่มต้น
อัพโหลด webman.bin ไปยังเซิร์ฟเวอร์ Linux แล้วรันคำสั่ง `./webman.bin start` หรือ `./webman.bin start -d` เพื่อเริ่มต้นการทำงาน

## หลักการ
* บีบอัดโปรเจค webman ให้เป็นไฟล์ phar ก่อน
* จากนั้นดาวน์โหลดไฟล์ php8.x.micro.sfx ไปยังเครื่องที่ใช้งาน
* ผนวกไฟล์ php8.x.micro.sfx และไฟล์ phar เพื่อสร้างไฟล์ไบนารี

## ข้อสังเกต
* เวอร์ชั่น PHP บนเครื่องที่ใช้งานต้องเป็น 7.2 ขึ้นไป
* แต่สามารถบีบอัดเป็นไฟล์สำหรับ PHP8 เท่านั้น
* แนะนำให้เวอร์ชั่น PHP บนเครื่องที่ใช้งานและเวอร์ชั่นที่ใช้งานเวอร์ชั่นการบีบอัดเท่ากันเพื่อป้องกันปัญหาการเข้ากันได้
* การบีบอัดจะดาวน์โหลดรหัสต้นฉบับของ PHP8 แต่ไม่ได้ทำการติดตั้งที่เครื่องที่ใช้งาน ไม่มีผลต่อโปรแกรม PHP บนเครื่องที่ใช้งาน
* ไฟล์ webman.bin เฉพาะที่ใช้งานบนระบบ Linux ที่ใช้กลุ่มคำสั่ง x86_64 เท่านั้น และไม่สามารถใช้งานบนระบบ mac ได้
* ไม่มีการบีบอัดไฟล์ env โดยค่าเริ่มต้น (`config/plugin/webman/console/app.php` ควบคุม exclude_files) ดังนั้นไฟล์ env ควรอยู่ในไดเร็กทอรีเดียวกันกับ webman.bin
* ในระหว่างการทำงาน จะสร้างไดเร็กทอรี runtime ในตำแหน่งไฟล์ webman.bin เพื่อเก็บไฟล์บันทึก
* ปัจจุบัน webman.bin ไม่ได้รับการอ่านไฟล์ php.ini ภายนอก หากต้องการกำหนด php.ini แต่งตั้งที่ไฟล์ `/config/plugin/webman/console/app.php`

## ดาวน์โหลด PHP แบบสแตติกเท่านั้น
บางครั้งคุณอาจไม่มีที่ติตั้ง PHP แค่จำเป็นที่จะใช้ไฟล์ PHP ที่สามารถใช้งานได้ คลิกที่นี่เพื่อดาวน์โหลด [งสแตติก PHP ดาวน์โหลด](https://www.workerman.net/download)

> **คำแนะนำ**
> หากต้องการกำหนดไฟล์ php.ini สำหรับ PHP แบบสแตติก ให้ใช้คำสั่งต่อไปนี้ `php -c /your/path/php.ini start.php start -d`

## การสนับสนุนของส่วนขยาย
bcmath
calendar
Core
ctype
curl
date
dom
event
exif
FFI
fileinfo
filter
gd
hash
iconv
json
libxml
mbstring
mongodb
mysqlnd
openssl
pcntl
pcre
PDO
pdo_mysql
pdo_sqlite
Phar
posix
readline
redis
Reflection
session
shmop
SimpleXML
soap
sockets
SPL
sqlite3
standard
tokenizer
xml
xmlreader
xmlwriter
zip
zlib

## แหล่งที่มาของโปรเจค
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
