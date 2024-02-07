# การแพ็คข้อมูลเป็นไฟล์ทวินารี่

webman สนับสนุนการแพ็คโปรเจกต์เป็นไฟล์ทวินารี่ ซึ่งทำให้ webman สามารถทำงานบนระบบ Linux โดยไม่จำเป็นต้องมีโปรแกรม PHP

> **หมายเหตุ**
> ไฟล์ที่แพ็คจะสามารถทำงานได้เฉพาะบนระบบ Linux ที่ใช้โครงสร้าง x86_64 เท่านั้น และไม่สนับสนุนระบบ Mac
> ต้องปิดการตั้งค่าของ `php.ini` โดยตั้งค่า `phar.readonly = 0`

## ติดตั้งเครื่องมือคอมมานด์ไลน์
`composer require webman/console ^1.2.24`

## การตั้งค่า
เปิดไฟล์ `config/plugin/webman/console/app.php` แล้วตั้งค่า
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
เพื่อแยกรายการที่ไม่จำเป็นออกไปเมื่อแพ็คเพื่อป้องกันขนาดของไฟล์ทวินารี่ที่สร้างขึ้นมากเกินไป

## การแพ็ค
รันคำสั่ง
```shell
php webman build:bin
```
สามารถกำหนดเวอร์ชัน PHP ที่จะใช้ในการแพ็คได้เช่น
```shell
php webman build:bin 8.1
```

หลังจากแพ็คเสร็จ จะสร้างไฟล์ `webman.bin` ในไดเรกทอรี build

## เริ่มทำงาน
อัปโหลด webman.bin ไปยังเซิรฟเวอร์ Linux แล้วรัน `./webman.bin start` หรือ `./webman.bin start -d` เพื่อเริ่มต้นการทำงาน

## หลักการทำงาน
* แรกทำการแพ็คโปรเจกต์ webman ในเครื่องที่พัฒนาให้เป็นไฟล์ phar
* จากนั้นดาวน์โหลด php8.x.micro.sfx จากระยะทางไกลมายังเครื่องที่พัฒนา
* ต่อมานำ php8.x.micro.sfx และไฟล์ phar มารวมกันเป็นไฟล์ทวินารี่เดียวกัน

## ข้อควรระวัง
* เครื่องที่พัฒนาควรมีเวอร์ชัน PHP ไม่ต่ำกว่า 7.2 เพื่อทำงานคำสั่งแพ็ค
* แต่จะสามารถแพ็คไฟล์เป็นเวอร์ชัน PHP 8 เท่านั้น
* ขอแนะนำอย่างมากให้เวอร์ชัน PHP ที่ใช้ทำงานคำสั่งแพ็คเหมือนกันกับเวอร์ชันที่ใช้รันเพื่อป้องกันปัญหาเกี่ยวกับความเข้ากันได้
* การแพ็คจะดาวน์โหลดโค้ดต้นฉบับของ PHP 8 มาแต่ตัว และจะไม่มีการติดตั้งไว้ในเครื่องที่พัฒนาโปรแกรม
* webman.bin ในปัจจุบันสามารถทำงานได้เฉพาะบนระบบ Linux ที่ใช้โครงสร้าง x86_64 เท่านั้น และไม่สนับสนุนระบบ Mac
* ค่าเริ่มต้นไม่แพ็คไฟล์ env (`config/plugin/webman/console/app.php` ควบคุมไฟล์ที่ไม่ถนัด) ดังนั้นให้วางไฟล์ env ไว้ในไดเรกทอรีเดียวกับ webman.bin
* ในระหว่างการทำงาน จะมีการสร้างไดเรกทอรี runtime ที่ใช้เก็บไฟล์บันทึก
* webman.bin ในปัจจุบันไม่ได้อ่านไฟล์ php.ini จากภายนอก หากต้องการปรับแต่ง php.ini ให้ตั้งค่าที่ไฟล์ `/config/plugin/webman/console/app.php` custom_ini

## ดาวน์โหลด PHP แบบสแตติกเท่านั้น
บางทีคุณอาจจะไม่ต้องการติดตั้งโปรแกรม PHP แต่ต้องการเพียงไฟล์โปรแกรม PHP ที่ใช้ได้ เหมือนชาร์จ คลิกที่นี่เพื่อ[ดาวน์โหลด PHP แบบสแตติก](https://www.workerman.net/download)

> **เคล็ดลับ**
> หากต้องการกำหนดไฟล์ php.ini สำหรับ PHP แบบสแตติก ให้ใช้คำสั่งต่อไปนี้ `php -c /your/path/php.ini start.php start -d`

## สนับสนุนส่วนขยาย
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

## ที่มาของโปรเจกต์
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
