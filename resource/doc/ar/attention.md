# ملاحظات حول البرمجة

## نظام التشغيل
يدعم webman تشغيله على نظام Linux ونظام Windows. ولكن نظرًا لعدم قدرة workerman على دعم إعدادات متعددة العمليات والعمليات الخلفية على نظام Windows ، فإنه يُوصى بشدة باستخدام نظام Windows فقط في بيئة تطوير وتصحيح وفحص الأخطاء. بينما يُفضل استخدام نظام Linux في البيئة الإنتاجية.

## كيفية بدء التشغيل
**على نظام Linux** تستخدم الأمر `php start.php start` (في وضع تصحيح) أو `php start.php start -d` (في وضع العمليات الخلفية) لبدء التشغيل.
**أما بالنسبة لنظام Windows**، يُنفذ ملف `windows.bat` أو يُستخدم الأمر `php windows.php` لبدء التشغيل، ويتم إيقاف التشغيل باستخدام "Ctrl + c". ولا تدعم أنظمة Windows الأوامر مثل stop و reload و status و reload connections.

## تحميل الذاكرة
webman هو إطار عمل دائم الإقامة في الذاكرة، حيث عند تحميل ملف PHP في الذاكرة، يتم إعادة استخدامه عادةً ولا يتم قراءته مرة أخرى من القرص (ما عدا الملفات القالبية). لذا، في حالة تغيير الشفرة أو تغيير الإعدادات في بيئة الإنتاج، يجب تنفيذ `php start.php reload` ليتم تطبيق التغييرات. وفي حالة تغيير إعدادات العمليات أو تثبيت حزمة مكتبة composer جديدة، يجب إعادة تشغيل `php start.php restart`.

> من أجل تسهيل عملية التطوير، تحتوي webman على عملية مراقبة مخصصة تُستخدم لمراقبة تحديثات الملفات المستخدمة في العمل. حيث يتم تنفيذ عملية إعادة التحميل تلقائيًا عند تحديث الملفات. وتكون هذه الوظيفة متاحة فقط عند تشغيل workerman في وضع التصحيح (بدون استخدام `-d` عند البدء). ويحتاج مستخدمو نظام Windows إلى تنفيذ `windows.bat` أو `php windows.php` لتفعيل هذه الوظيفة.

## بشأن عبارات الإخراج
في المشاريع التقليدية التي تعمل على PHP-FPM، يتم استخدام دوال `echo` و `var_dump` لإخراج البيانات مباشرة إلى الصفحة، ولكن في webman، يتم عرض هذه الإخراجات غالبًا في واجهة سطر الأوامر ولا تُظهر على الصفحة (ما عدا الإخراج في ملفات القوالب).

## يُرجى عدم استخدام عبارات `exit` و `die`
تنفيذ عبارات `die` أو `exit` سيؤدي إلى إيقاف العملية وإعادة التشغيل، مما يؤدي إلى عدم استجابة الطلب الحالي بشكل صحيح.

## يُرجى عدم استخدام دالة `pcntl_fork`
تقوم دالة `pcntl_fork` بإنشاء عملية، وهو ما لا يُسمح به في webman.
