# حقن تلقائي للتبعيات
في webman ، حقن التبعيات الآلي هو وظيفة اختيارية، وهذه الوظيفة معطلة بشكل افتراضي. إذا كنت بحاجة إلى حقن تلقائي للتبعيات، نوصي باستخدام [php-di](https://php-di.org/doc/getting-started.html). التالي هو كيفية استخدام webman مع `php-di`.

## التثبيت
```
composer require psr/container ^1.1.1 php-di/php-di ^6 doctrine/annotations ^1.14
```

قم بتعديل ملف التكوين `config/container.php` ليكون المحتوى النهائي كما يلي:
```php
$builder = new \DI\ContainerBuilder();
$builder->addDefinitions(config('dependence', []));
$builder->useAutowiring(true);
$builder->useAnnotations(true);
return $builder->build();
```

> يجب أن يكون الملف `config/container.php` يعيد نسخة من حاوي PSR-11. إذا لم ترغب في استخدام `php-di`، يمكنك إنشاء وإرجاع نسخة أخرى من حاوي PSR-11.

## حقن البناء
أنشئ ملفًا جديدًا `app/service/Mailer.php` (في حالة عدم وجود الدليل، يرجى إنشاؤه بنفسك) بالمحتوى التالي:
```php
<?php
namespace app\service;

class Mailer
{
    public function mail($email, $content)
    {
        // قم بتجاهل كود إرسال البريد الإلكتروني
    }
}
```

يحتوي ملف `app/controller/UserController.php` على المحتوى التالي:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'مرحبًا وأهلاً بك!');
        return response('موافق');
    }
}
```
بشكل طبيعي، يحتاج الأمر التالي لاستكمال تفعيل `app\controller\UserController`:
```php
$mailer = new Mailer;
$user = new UserController($mailer);
```
عند استخدام `php-di`، لن يحتاج المطور إلى تهيئة يدوية لـ`Mailer` في التحكم. سيقوم webman تلقائيًا بذلك. إذا كان هناك تبعيات أخرى أثناء تهيئة `Mailer`، سيقوم webman بتهيئتها وحقنها تلقائيًا. لن يحتاج المطور إلى أي عمل تهيئة.

> **ملاحظة**
> يجب أن تكون النسخ التي تم إنشاؤها بواسطة الإطار أو `php-di` قادرة على إكمال حقن التبعيات تلقائيًا، لا يمكن إكمال حقن التبعيات تلقائيًا للنسخ التي تم إنشاؤها يدويًا باستخدام الكلمة الرئيسية`new`. إذا كنت بحاجة للحقن، يجب استخدام واجهة الصندوق `support\Container` كبديل عن الكلمة الرئيسية `new`، مثال:

```php
use app\service\UserService;
use app\service\LogService;
use support\Container;

// لا يمكن إكمال حقن التبعيات للنسخ التي تم إنشاؤها بواسطة new
$user_service = new UserService;
// لا يمكن إكمال حقن التبعيات للنسخ التي تم إنشاؤها بواسطة new
$log_service = new LogService($path, $name);

// يمكن إكمال حقن التبعيات للنسخ التي تم إنشاؤها بواسطة الصندوق
$user_service = Container::get(UserService::class);
// يمكن إكمال حقن التبعيات للنسخ التي تم إنشاؤها بواسطة الصندوق
$log_service = Container::make(LogService::class, [$path, $name]);
```

## حقن ملاحظة
بالإضافة إلى حقن البناء، يمكننا استخدام حقن ملاحظة. استمر في الفئة `app\controller\UserController` في المثال السابق على النحو التالي:

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var Mailer
     */
    private $mailer;

    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'مرحبًا وأهلاً بك!');
        return response('موافق');
    }
}
```
هذا المثال يستخدم حقن `@Inject` ويعلن نوع الكائن بواسطة `@var`. يعمل هذا المثال بنفس تأثير حقن بناء، ولكن الكود أكثر انضباطًا بالكتابة.

> **ملاحظة**
> webman لا يدعم حاليًا حقن معلمات التحكم، على سبيل المثال، الكود التالي لا يدعم في حالة ويبمان <= 1.4.6

```php
<?php
namespace app\controller;

use support\Request;
use app\service\Mailer;

class UserController
{
    // الإصدار 1.4.6 أو أقل لا يدعم حقن معلمات التحكم
    public function register(Request $request, Mailer $mailer)
    {
        $mailer->mail('hello@webman.com', 'مرحبًا وأهلاً بك!');
        return response('موافق');
    }
}
```

## حقن بناء مخصص
أحيانًا، قد لا تكون المعلمات التي تتم تمريرها إلى بناء الكائن هي حالة الكائن؛ بدلاً من ذلك يمكن أن تكون سلاسل، أرقام، مصفوفات وما إلى ذلك. على سبيل المثال، إذا كنا بحاجة إلى تمرير عنوان IP ومنفذ خادم smtp إلى بناء البريد الإلكتروني:

```php
<?php
namespace app\service;

class Mailer
{
    private $smtpHost;

    private $smtpPort;

    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }

    public function mail($email, $content)
    {
        // قم بتجاهل كود إرسال البريد الإلكتروني
    }
}
```

لا يمكن استخدام حقن بناء تلقائي لأن `php-di` لا يمكنه تحديد قيم `smtp_host` و`smtp_port`. في هذه الحالة، يمكنك محاولة حقن مخصصة.

ضمن `config/dependence.php` (في حالة عدم وجود الملف، يرجى إنشاؤه بنفسك)، أضف الكود التالي:

```php
return [
    // ... تجاهل تكوينات أخرى
    
    app\service\Mailer::class =>  new app\service\Mailer('192.168.1.11', 25);
];
```
بهذه الطريقة، عندما تحتاج عملية الحقن إلى الحصول على مثيل `app\service\Mailer`، سيتم استخدام مثيل `app\service\Mailer` الذي تم إنشاؤه بهذه التكوينات تلقائيًا.

لاحظ أننا استخدمنا `new` في `config/dependence.php` لتهيئة صنف `Mailer`. هذا لا يكون مشكلة في هذا المثال، ولكن فكر في حالة صنف `Mailer` كان يعتمد على صنوف أخرى أو يستخدم حقن ملاحظة داخليًا، فإعادة تهيئة مباشرة باستخدام `new` لن تكون مؤهلة لحقن التبعيات تلقائياً. لحل هذه المشكلة، استخدم حقن واجهة مخصصة واستخدم `Container::get(اسم_الصف)` أو `Container::make(اسم_الصف، [معلمات_بناء])` لتهيئة الصنف.

## حقن واجهة مخصصة
في المشاريع الحقيقية، نفضل توجيه البرمجة نحو الواجهة بدلاً من المصنف الفعلي. على سبيل المثال، في `app\controller\UserController`، يجب أن تقوم بإدخال `app\service\MailerInterface` بدلاً من `app\service\Mailer`.

قم بتعريف واجهة `MailerInterface`:
```php
<?php
namespace app\service;

interface MailerInterface
{
    public function mail($email, $content);
}
```

قم بتعريف تنفيذ `MailerInterface`:
```php
<?php
namespace app\service;

class Mailer implements MailerInterface
{
    private $smtpHost;
    private $smtpPort;
    public function __construct($smtp_host, $smtp_port)
    {
        $this->smtpHost = $smtp_host;
        $this->smtpPort = $smtp_port;
    }
    public function mail($email, $content)
    {
        // قم بتجاهل كود إرسال البريد الإلكتروني
    }
}
```

قم بإدخال `MailerInterface` بدلاً من التنفيذ الفعلي:
```php
<?php
namespace app\controller;

use support\Request;
use app\service\MailerInterface;
use DI\Annotation\Inject;

class UserController
{
    /**
     * @Inject
     * @var MailerInterface
     */
    private $mailer;
    
    public function register(Request $request)
    {
        $this->mailer->mail('hello@webman.com', 'مرحبًا وأهلاً بك!');
        return response('موافق');
    }
}
```

يقوم `config/dependence.php` بتحديد مثيل واجهة `MailerInterface` كما يلي:
```php
use Psr\Container\ContainerInterface;
return [
    app\service\MailerInterface::class => function(ContainerInterface $container) {
        return $container->make(app\service\Mailer::class, ['smtp_host' => '192.168.1.11', 'smtp_port' => 25]);
    }
];
```

بهذه الطريقة، عندما يحتاج المشروع إلى استخدام `MailerInterface`، سيتم استخدام تنفيذ `Mailer` تلقائياً.

> وسيلة استفادة من البرمجة نحو الواجهة هي أنه عندما نحتاج إلى استبدال مكون ما، لا يلزمنا تغيير كود المشروع. فقط قم بتغيير التنفيذ الفعلي في `config/dependence.php`.

## حقن مخصص أخرى
يمكن تحديد القيم الأخرى، مثل السلاسل أو الأرقام أو المصفوفات إلخ، ضمن `config/dependence.php`.

على سبيل المثال، يمكن تحديد ما يلي ضمن `config/dependence.php`:
```php
return [
    'smtp_host' => '192.168.1.11',
    'smtp_port' => 25
];
```

بهذا الشكل، يمكننا حقن `smtp_host` و `smtp_port` في خاصية الكائن في الفئة كما يلي:

```php
<?php
namespace app\service;

use DI\Annotation\Inject;

class Mailer
{
    /**
     * @Inject("smtp_host")
     */
    private $smtpHost;

    /**
     * @Inject("smtp_port")
     */
    private $smtpPort;

    public function mail($email, $content)
    {
        // قم بتجاهل كود إرسال البريد الإلكتروني
        echo "{$this->smtpHost}:{$this->smtpPort}\n"; // سيطبع 192.168.1.11:25
    }
}
```

> ملاحظة: `@Inject("key")` يجب أن يكون داخل اقواس مزدوجة.

## لمزيد من المحتويات
يرجى الرجوع إلى [دليل php-di](https://php-di.org/doc/getting-started.html)
