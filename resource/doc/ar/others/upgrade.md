# طريقة الترقية

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **ملحوظة**
> نظرًا لإيقاف بروكسي ألي بمكتبة
> composer عن مزامنة البيانات من مصدر اطلاق
> composer الرسمي، فإنه حالياً غير ممكن
> تحديث webman الى أحدث نسخة
> باستخدام بروكسي ألي بمكتبة، يُرجى استخدام
> الأمر التالي `composer config -g --unset repos.packagist` لاستعادة
> استخدام مصدر بيانات composer الرسمي
