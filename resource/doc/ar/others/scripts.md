# النصائح المخصصة

أحيانًا نحتاج إلى كتابة بعض النصوص المؤقتة، حيث يمكننا استدعاء أي فئة أو واجهة مثل webman في هذه النصوص لإتمام العمليات مثل استيراد البيانات أو تحديث الإحصائيات وما شابه ذلك. وهذا أمر سهل للغاية في webman، على سبيل المثال:

**إنشاء `scripts/update.php` جديدة** (إذا لم تكن المجلد موجودًا، فيُرجى إنشاؤه يدويًا)
```php
<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../support/bootstrap.php';

use think\facade\Db;

$user = Db::table('user')->find(1);

var_dump($user);
```

بالطبع يمكننا أيضًا استخدام `webman/console` لإنشاء أوامر مخصصة مثل هذه العمليات، يُرجى الرجوع إلى [سطر الأوامر](../plugin/console.md)
