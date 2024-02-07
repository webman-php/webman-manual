# การปิดการตรวจสอบฟังก์ชัน

ใช้สคริปต์นี้เพื่อตรวจสอบว่ามีการปิดใช้งานฟังก์ชันหรือไม่ ใช้คำสั่งcurl -Ss https://www.workerman.net/webman/check | phpผ่าน command line

หากมีการแจ้งเตือน"ฟังก์ชันชื่อฟังก์ชัน ถูกปิดการใช้งาน โปรดตรวจสอบ disable_functions ใน php.ini" นั้นหมายความว่ามีการปิดใช้งานฟังก์ชันที่ webman ต้องการ จึงต้องระบุถอดการปิดใช้งานใน php.ini เพื่อให้ webman ใช้งานได้ตามปกติ
เพื่อถอดการปิดที่ใช้วิธีดังต่อไปนี้

## วิธีที่ 1 
ติดตั้ง `webman/console` 
``` 
composer require webman/console ^v1.2.35
```

รันคำสั่ง
``` 
php webman fix-disable-functions
```

## วิธีที่ 2

รันสคริปต์ `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` เพื่อถอดการปิดใช้งาน

## วิธีที่ 3

รัน `php --ini` เพื่อค้นหาตำแหน่งของไฟล์ php.ini ที่ php cli ใช้

เปิด php.ini แล้วค้นหา `disable_functions` และถอดการใช้งานฟังก์ชันต่อไปนี้
``` 
stream_socket_server
stream_socket_client
pcntl_signal_dispatch
pcntl_signal
pcntl_alarm
pcntl_fork
posix_getuid
posix_getpwuid
posix_kill
posix_setsid
posix_getpid
posix_getpwnam
posix_getgrnam
posix_getgid
posix_setgid
posix_initgroups
posix_setuid
posix_isatty
proc_open
proc_get_status
proc_close
shell_exec
```
