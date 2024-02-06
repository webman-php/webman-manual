# التعبئة بتقنية Phar

Phar هو نوع من ملفات التعبئة في PHP تشبه JAR في Java. يمكنك استخدام Phar لتعبئة مشروعك webman كملف Phar واحد لسهولة النشر.

**نود أن نشكر [fuzqing](https://github.com/fuzqing) على المساهمة الكبيرة.**

> **يرجى الملاحظة**
> يجب إيقاف تكوينات phar في `php.ini` عن طريق تحديد `phar.readonly = 0`

## تثبيت أداة سطر الأوامر
`composer require webman/console`

## الإعدادات
افتح ملف `config/plugin/webman/console/app.php`، وحدد `'exclude_pattern' => '#^(?!.*(composer.json|/.github/|/.idea/|/.git/|/.setting/|/runtime/|/vendor-bin/|/build/|vendor/webman/admin))(.*)$#'`، لاستبعاد بعض الدلائل والملفات غير الضرورية أثناء عملية التعبئة، وتجنب زيادة حجم الملف.

## التعبئة
قم بتشغيل الأمر `php webman phar:pack` في دليل مشروع webman، سيتم إنشاء ملف `webman.phar` في دليل bulid.

> تُعد التكوينات المتعلقة بالتعبئة موجودة في ملف `config/plugin/webman/console/app.php`.

## الأوامر ذات الصلة بالبدء والإيقاف
**البدء**
`php webman.phar start` أو `php webman.phar start -d`

**الإيقاف**
`php webman.phar stop`

**عرض الحالة**
`php webman.phar status`

**عرض حالة الاتصال**
`php webman.phar connections`

**إعادة التشغيل**
`php webman.phar restart` أو `php webman.phar restart -d`

## الملاحظات
* بعد تشغيل webman.phar، سيتم إنشاء دليل runtime في المجلد الذي يحتوي على webman.phar، وذلك لتخزين الملفات المؤقتة مثل السجلات.

* إذا كنت تستخدم ملف .env في مشروعك، يجب وضع ملف .env في المجلد الذي يحتوي على webman.phar.

* إذا كنت تحتاج إلى رفع ملفات إلى مجلد public في مشروعك، يجب تحرير public مستقلاً ووضعه في المجلد الذي يحتوي على webman.phar. في هذه الحالة، ستحتاج إلى تكوين `config/app.php`.
```
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
يمكن للأعمال استخدام الدالة المساعدة `public_path()` للعثور على موقع المجلد الفعلي لـ public.

* لا يدعم webman.phar فتح العمليات المخصصة في نظام ويندوز.
