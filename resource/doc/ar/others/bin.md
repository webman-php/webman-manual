# التغليف الثنائي

يدعم webman تجميع المشروع في ملف ثنائي واحد، مما يتيح تشغيل webman على Linux دون الحاجة إلى بيئة PHP.

> **تنبيه**
> الملف المعبأ يدعم حاليًا التشغيل على أنظمة Linux ذات معمارية x86_64 فقط. لا يدعم Windows وmacOS.
> يجب تعطيل خيار phar في `php.ini` بتعيين `phar.readonly = 0`.

## تثبيت أداة سطر الأوامر
`composer require webman/console`

## التغليف
شغّل الأمر
```
php webman build:bin
```
يمكنك أيضًا تحديد إصدار PHP المستخدم في التغليف، مثلاً
```
php webman build:bin 8.3
```

بعد التغليف، سيتم إنشاء ملف `webman.bin` في مجلد build.

## التشغيل
ارفع webman.bin إلى خادم Linux وشغّل `./webman.bin start` أو `./webman.bin start -d` للبدء.

## آلية العمل
* أولاً يتم تجميع مشروع webman المحلي في ملف phar
* ثم يتم تنزيل php8.x.micro.sfx عن بُعد
* يُمزج php8.x.micro.sfx وملف phar في ملف ثنائي واحد

## ملاحظات
* يُفضّل بشدة استخدام نفس إصدار PHP محلياً وعند التغليف (مثلاً PHP 8.3 في كلتا الحالتين) لتجنب مشكلات التوافق
* التغليف سينزّل كود مصدر PHP 8 لكن دون تثبيته محلياً، ولن يؤثر على بيئة PHP المحلية
* webman.bin يدعم حاليًا التشغيل على Linux x86_64 فقط ولا يدعم macOS
* المشاريع المعبأة لا تدعم reload؛ تحديث الكود يتطلب إعادة التشغيل
* افتراضيًا لا يُضمّن ملف env في التغليف (يتحكم فيه exclude_files في `config/plugin/webman/console/app.php`)، لذا يجب وضع ملف env في نفس مجلد webman.bin عند البدء
* أثناء التشغيل يُنشأ مجلد runtime في مجلد webman.bin لتخزين ملفات السجل
* حاليًا webman.bin لا يقرأ ملفات php.ini الخارجية. لتخصيص php.ini، عيّنه في custom_ini في `config/plugin/webman/console/app.php`
* بعض الملفات لا تحتاج للتغليف؛ يمكن تكوين الاستثناءات في `config/plugin/webman/console/app.php` لتجنب حزمة كبيرة الحجم
* التغليف الثنائي لا يدعم كوروتينات Swoole
* لا تخزّن أبدًا الملفات المرفوعة من المستخدمين داخل الحزمة الثنائية. التعامل معها عبر البروتوكول `phar://` خطر (ثغرة إلغاء التسلسل phar). يجب تخزين الملفات المرفوعة خارج الحزمة على القرص
* إذا كان التطبيق يحتاج رفع ملفات إلى مجلد public، انقل مجلد public إلى نفس مكان webman.bin وعدّل `config/app.php` كما يلي ثم أعد التغليف:
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

## تنزيل PHP الساكن منفصلًا
أحيانًا تحتاج فقط لملف PHP قابل للتنفيذ دون نشر بيئة PHP كاملة. [حمّل PHP الساكن من هنا](https://www.workerman.net/download).

> **نصيحة**
> لتحديد ملف php.ini لـ PHP الساكن: `php -c /your/path/php.ini start.php start -d`

## الامتدادات المدعومة
apcu, bcmath, bz2, calendar, Core, ctype, curl, date, dba, dom, event, exif, fileinfo, filter, ftp, gd, gmp, hash, iconv, imagick, imap, json, libxml, mbstring, mysqli, mysqlnd, openssl, pcntl, pcre, PDO, pdo_mysql, pgsql, Phar, posix, protobuf, readline, redis, Reflection, session, shmop, SimpleXML, soap, sockets, sodium, SPL, sqlite3, standard, swoole, sysvmsg, sysvsem, sysvshm, tokenizer, xml, xmlreader, xmlwriter, xsl, Zend OPcache, zip, zlib

## مصدر المشروع

https://github.com/crazywhalecc/static-php-cli
https://github.com/webman-php/static-php-cli-hosted
