# การแพ็คเป็นไฟล์ไบนารี

webman รองรับการแพ็คโปรเจกต์เป็นไฟล์ไบนารีเดียว ทำให้ทำงานบน Linux ได้โดยไม่ต้องมีสภาพแวดล้อม PHP

> **หมายเหตุ**
> ไฟล์ที่แพ็คแล้วขณะนี้รองรับเฉพาะการทำงานบน Linux สถาปัตยกรรม x86_64 ไม่รองรับ Windows และ macOS
> ต้องปิดตัวเลือก phar ใน `php.ini` โดยตั้งค่า `phar.readonly = 0`

## ติดตั้งเครื่องมือบรรทัดคำสั่ง
`composer require webman/console`

## การแพ็ค
รันคำสั่ง
```
php webman build:bin
```
สามารถระบุเวอร์ชัน PHP ที่ใช้แพ็คได้ เช่น
```
php webman build:bin 8.3
```

หลังแพ็ค จะได้ไฟล์ `webman.bin` ในโฟลเดอร์ build

## เริ่มต้น
อัปโหลด webman.bin ไปยังเซิร์ฟเวอร์ Linux แล้วรัน `./webman.bin start` หรือ `./webman.bin start -d`

## หลักการ
* ขั้นแรกแพ็คโปรเจกต์ webman ในเครื่องเป็นไฟล์ phar
* จากนั้นดาวน์โหลด php8.x.micro.sfx ระยะไกลมาเก็บในเครื่อง
* เชื่อม php8.x.micro.sfx เข้ากับไฟล์ phar เป็นไฟล์ไบนารีเดียว

## ข้อควรระวัง
* แนะนำให้ใช้เวอร์ชัน PHP เดียวกันทั้งในเครื่องและตอนแพ็ค (เช่น PHP 8.3 ทั้งคู่) เพื่อหลีกเลี่ยงปัญหาไม่เข้ากัน
* การแพ็คจะดาวน์โหลดซอร์ส PHP 8 แต่ไม่ได้ติดตั้งในเครื่อง จึงไม่กระทบสภาพแวดล้อม PHP
* webman.bin ปัจจุบันรองรับเฉพาะ Linux x86_64 และไม่รองรับ macOS
* โปรเจกต์ที่แพ็คแล้วไม่รองรับ reload การอัปเดตโค้ดต้อง restart
* ค่าเริ่มต้นไม่แพ็คไฟล์ env (ควบคุมโดย exclude_files ใน `config/plugin/webman/console/app.php`) ดังนั้นตอนเริ่มต้น ไฟล์ env ต้องอยู่โฟลเดอร์เดียวกับ webman.bin
* ขณะทำงานจะสร้างโฟลเดอร์ runtime ในโฟลเดอร์ที่มี webman.bin เพื่อเก็บไฟล์ log
* ตอนนี้ webman.bin ไม่อ่านไฟล์ php.ini ข้างนอก หากต้องปรับ php.ini ให้ตั้งค่าใน custom_ini ของ `config/plugin/webman/console/app.php`
* บางไฟล์ไม่จำเป็นต้องแพ็ค สามารถตั้งค่าแยกใน `config/plugin/webman/console/app.php` เพื่อไม่ให้แพ็คใหญ่เกินไป
* การแพ็คไบนารีไม่รองรับ Swoole coroutine
* ห้ามเก็บไฟล์ที่ผู้ใช้อัปโหลดไว้ในแพ็คไบนารี เพราะการเข้าถึงผ่านโปรโตคอล `phar://` เสี่ยงต่อช่องโหว่ phar deserialization ไฟล์อัปโหลดต้องเก็บแยกบนดิสก์นอกแพ็ค
* หากแอปต้องอัปโหลดไฟล์ไปโฟลเดอร์ public ให้แยกโฟลเดอร์ public มาวางตำแหน่งเดียวกับ webman.bin ตั้งค่า `config/app.php` ดังนี้ แล้วแพ็คใหม่:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## ดาวน์โหลด PHP สแตติกแยกต่างหาก
บางครั้งคุณแค่ต้องการไฟล์ PHP ที่รันได้ โดยไม่ต้องติดตั้งสภาพแวดล้อม PHP [ดาวน์โหลด PHP สแตติกที่นี่](https://www.workerman.net/download)

> **เคล็ดลับ**
> หากต้องระบุไฟล์ php.ini สำหรับ PHP สแตติก: `php -c /your/path/php.ini start.php start -d`

## ส่วนขยายที่รองรับ
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## แหล่งโปรเจกต์

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
