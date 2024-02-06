# متطلبات البيئة

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. إنشاء المشروع

```php
composer create-project workerman/webman
```

### 2. تشغيل

قم بالدخول إلى دليل webman

#### لمستخدمي Windows
انقر نقرًا مزدوجًا فوق `windows.bat` أو قم بتشغيل `php windows.php` للبدء

> **ملحوظة**
> إذا كان هناك خطأ، فقد يكون هناك وظيفة معينة ممنوعة، راجع [فحص تعطيل الدوال](others/disable-function-check.md) لإزالة القيود

#### لمستخدمي Linux
التشغيل بطريقة `debug` (لأغراض التطوير والتصحيح)

```php
php start.php start
```

التشغيل بطريقة `daemon` (للبيئة الإنتاجية)

```php
php start.php start -d
```

> **ملحوظة**
> إذا كان هناك خطأ، فقد يكون هناك وظيفة معينة ممنوعة، راجع [فحص تعطيل الدوال](others/disable-function-check.md) لإزالة القيود

### 3. الوصول

افتح المتصفح واذهب إلى `http://عنوان-الآيبي:8787`
