# ตรวจสอบการปิดใช้งานฟังก์ชัน

ใช้สคริปต์นี้เพื่อตรวจสอบว่าฟังก์ชันไหนถูกปิดใช้งานหรือไม่ โดยรันคำสั่งต่อไปนี้ในโหมดคำสั่ง
```curl -Ss https://www.workerman.net/webman/check | php```

หากมีการแจ้งเตือนว่า```Functions ชื่อฟังก์ชัน ถูกปิดใช้งาน โปรดตรวจสอบ disable_functions ใน php.ini``` หมายความว่าฟังก์ชันที่ webman ขึ้นอยู่ถูกปิดใช้งาน จึงต้องเอาปิดใช้งานใน php.ini ก่อนที่จะใช้ webman ได้อย่างปกติ
สามารถทำการเอามันออกจากการปิดใช้งานได้โดยการใช้วิธีใดก็ได้จากทางนี้

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

รันสคริปต์ `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` เพื่อเอามันออกจากการปิดใช้งาน

## วิธีที่ 3

รันคำสั่ง `php --ini` เพื่อค้นหาตำแหน่งของไฟล์ php.ini ที่ php cli ใช้

เปิด php.ini และค้นหา `disable_functions` และเอาออกชื่อฟังก์ชันต่อไปนี้ออก
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
