# فحص تعطيل الوظائف

استخدم هذا النصب لفحص ما إذا كانت هناك وظائف معينة معطّلة. قم بتشغيل الأمر التالي في سطر الأوامر ```curl -Ss https://www.workerman.net/webman/check | php```

إذا كان هناك تنبيه يظهر بأن الوظيفة Functions الخاصة بـ "اسم الوظيفة" قد تم تعطيلها. يرجى التحقق من disable_functions في php.ini ، فهذا يعني أن الوظيفة التي يعتمد عليها webman قد تم تعطيلها ويجب رفع التعطيل من php.ini لتمكين استخدام webman بشكل صحيح.
لرفع التعطيل يرجى اتباع أحد الطرق التالية.

## الطريقة الأولى
قم بتثبيت `webman/console` 
```
composer require webman/console ^v1.2.35
```

قم بتنفيذ الأمر التالي
```
php webman fix-disable-functions
```

## الطريقة الثانية

قم بتنفيذ النصب `curl -Ss https://www.workerman.net/webman/fix-disable-functions | php` لرفع التعطيل

## الطريقة الثالثة

قم بتشغيل `php --ini` للعثور على موقع ملف php.ini المستخدم من قبل php cli

افتح ملف php.ini وابحث عن `disable_functions` وازل الدوال التالية من القائمة
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
