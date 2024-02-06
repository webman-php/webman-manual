# هيكل الدليل

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

لقد رأينا أن لدينا إضافة تطبيق تحتوي على هيكل الدليل وملفات تكوين متشابهة مع webman ، في الحقيقة فإن تجربة التطوير مع إضافات التطبيقات تقريباً لا تختلف كثيراً عن تطوير التطبيقات العادية ضمن webman.
تتبع إضافات التطبيقات للتوجيه PSR4 ، ونظرًا لأنَّ جميع إضافات التطبيقات موجودة في دليل plugin، فإن أسماء المساحات تبدأ بـ plugin ، مثلاً `plugin\foo\app\controller\UserController`.

## بخصوص دليل api
كل إضافة تطبيق تحتوي على دليل api ، إذا كان التطبيق الخاص بك يوفر بعض الواجهات الداخلية لاستخدام التطبيقات الأخرى ، فيجب وضع هذه الواجهات في دليل api.
يرجى ملاحظة أن هنا الواجهات المشار إليها هي واجهات استدعاء الدوال وليس واجهات استدعاء الشبكة.
على سبيل المثال، يوفر `إضافة البريد الإلكتروني` في `plugin/email/api/Email.php` واجهة `Email::send()` لاستخدام التطبيقات الأخرى لإرسال البريد الإلكتروني.
بالإضافة إلى ذلك، تم إنشاء `plugin/email/api/Install.php` تلقائيًا، وذلك ليتم استخدامها من قِبل سوق إضافات webman-admin لتنفيذ عمليات التثبيت أو الإلغاء.
