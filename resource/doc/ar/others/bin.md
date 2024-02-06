# التعبئة الثنائية

يدعم webman تعبئة المشروع كملف ثنائي، مما يتيح لـ webman أن يعمل على نظام Linux بدون بيئة PHP.

> **ملاحظة**
> الملفات المعبأة تعمل حالياً فقط على أنظمة Linux بتركيبة x86_64 ولا تعمل على أنظمة Mac.
> يجب إيقاف تكوين phar في `php.ini` أي تعيين `phar.readonly = 0`.

## تثبيت أداة سطر الأوامر
`composer require webman/console ^1.2.24`

## إعدادات التكوين
افتح ملف `config/plugin/webman/console/app.php` وقم بضبط
```php
'exclude_pattern'   => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'
```
لتستبعد بعض الدلائل والملفات غير المستخدمة أثناء تعبئتها، لتجنب زيادة حجم الملفات المعبأة.

## التعبئة
تشغيل الأمر
```
php webman build:bin
```
يمكن أيضًا تحديد الإصدار من PHP الذي سيتم إنتاج التعبئة بناءً عليه، على سبيل المثال
```
php webman build:bin 8.1
```

سيتم إنتاج ملف `webman.bin` في الدليل `build` بعد التعبئة.

## التشغيل
بعد تحميل webman.bin على خادم Linux، يمكن تشغيله بأمر `./webman.bin start` أو `./webman.bin start -d`.

## المبدأ
* تعبئة المشروع المحلي لـ webman كملف phar
* تنزيل php8.x.micro.sfx بعيداً ثم دمجه مع ملف phar لنحصل على ملف ثنائي

## الأمور المهمة
* يمكن تنفيذ أمر التعبئة إذا كان إصدار php المحلي >= 7.2
* على الرغم من ذلك، يمكن تعبئة المشروع فقط كملف ثنائي يعمل على PHP 8
* من المستحسن بشدة أن يكون إصدار PHP المحلي متطابقًا مع إصدار التعبئة. يجب تجنب المشاكل التوافقية
* عملية التعبئة ستقوم بتنزيل ـأصدرط php8، ولكنها لن تقوم بتثبيته محلياً ولن تؤثر على بيئة PHP المحلية
* webman.bin تعمل حالياً فقط على أنظمة Linux بتركيبة x86_64، ولا تعمل على أنظمة Mac
* بشكل افتراضي، لا يتم تعبئة ملفات env (يتحكم في ذلك بين الأدلة المستثناة في `config/plugin/webman/console/app.php`)، يجب وضع ملف env في نفس الدليل الذي يتواجد فيه webman.bin عند التشغيل 
* خلال التشغيل، سيتم إنشاء دليل runtime في الدليل الذي يتواجد فيه webman.bin لحفظ ملفات السجل
* حاليًا، لا يقرأ webman.bin ملف php.ini الخارجي. إذا كنت بحاجة لتخصيص 
php.ini، يُرجى ضبط custom_ini في ملف `/config/plugin/webman/console/app.php`

## تنزيل PHP الثابت بشكل مستقل
في بعض الأحيان، قد تحتاج فقط إلى تثبيت ملف PHP وتشغيله بدون تشغيل بيئة PHP بأكملها، يمكنك التحميل من هنا [تنزيل PHP الثابت](https://www.workerman.net/download)

> **ملاحظة**
> إذا كنت بحاجة إلى تحديد ملف php.ini لـ PHP الثابت، يُرجى استخدام الأمر التالي `php -c /your/path/php.ini start.php start -d`

## الامتدادات المدعومة
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

## مصدر المشروع
https://github.com/crazywhalecc/static-php-cli
https://github.com/walkor/static-php-cli
