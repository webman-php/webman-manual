# سطر الأوامر

مكوّن سطر أوامر Webman

## التثبيت
```
composer require webman/console
```

## الفهرس

### إنشاء الكود
- [make:controller](#make-controller) - إنشاء فئة وحدة التحكم
- [make:model](#make-model) - إنشاء فئة نموذج من جدول قاعدة البيانات
- [make:crud](#make-crud) - إنشاء CRUD كامل (نموذج + وحدة تحكم + موثّق)
- [make:middleware](#make-middleware) - إنشاء فئة وسيط
- [make:command](#make-command) - إنشاء فئة أمر وحدة التحكم
- [make:bootstrap](#make-bootstrap) - إنشاء فئة تهيئة للبدء
- [make:process](#make-process) - إنشاء فئة عملية مخصصة

### البناء والنشر
- [build:phar](#build-phar) - حزم المشروع كملف أرشيف PHAR
- [build:bin](#build-bin) - حزم المشروع كملف ثنائي مستقل
- [install](#install) - تشغيل برنامج التثبيت Webman

### أدوات الأوامر المفيدة
- [version](#version) - عرض إصدار إطار العمل Webman
- [fix-disable-functions](#fix-disable-functions) - إصلاح الدوال المعطلة في php.ini
- [route:list](#route-list) - عرض جميع المسارات المسجلة

### إدارة ملحقات التطبيق (app-plugin:*)
- [app-plugin:create](#app-plugin-create) - إنشاء ملحق تطبيق جديد
- [app-plugin:install](#app-plugin-install) - تثبيت ملحق التطبيق
- [app-plugin:uninstall](#app-plugin-uninstall) - إلغاء تثبيت ملحق التطبيق
- [app-plugin:update](#app-plugin-update) - تحديث ملحق التطبيق
- [app-plugin:zip](#app-plugin-zip) - حزم ملحق التطبيق كملف ZIP

### إدارة الملحقات (plugin:*)
- [plugin:create](#plugin-create) - إنشاء ملحق Webman جديد
- [plugin:install](#plugin-install) - تثبيت ملحق Webman
- [plugin:uninstall](#plugin-uninstall) - إلغاء تثبيت ملحق Webman
- [plugin:enable](#plugin-enable) - تفعيل ملحق Webman
- [plugin:disable](#plugin-disable) - إلغاء تفعيل ملحق Webman
- [plugin:export](#plugin-export) - تصدير كود مصدر الملحق

### إدارة الخدمات
- [start](#start) - بدء عمليات Webman
- [stop](#stop) - إيقاف عمليات Webman
- [restart](#restart) - إعادة تشغيل عمليات Webman
- [reload](#reload) - إعادة تحميل الكود دون توقف
- [status](#status) - عرض حالة عمليات التشغيل
- [connections](#connections) - الحصول على معلومات اتصالات العمليات

## إنشاء الكود

<a name="make-controller"></a>
### make:controller

إنشاء فئة وحدة التحكم.

**الاستخدام:**
```bash
php webman make:controller <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم وحدة التحكم (بدون لاحقة) |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--plugin` | `-p` | إنشاء وحدة التحكم في دليل الملحق المحدد |
| `--path` | `-P` | مسار وحدة التحكم المخصص |
| `--force` | `-f` | استبدال الملف إذا كان موجوداً |
| `--no-suffix` | | عدم إضافة لاحقة "Controller" |

**أمثلة:**
```bash
# إنشاء UserController في app/controller
php webman make:controller User

# إنشاء في الملحق
php webman make:controller AdminUser -p admin

# مسار مخصص
php webman make:controller User -P app/api/controller

# استبدال ملف موجود
php webman make:controller User -f

# إنشاء بدون لاحقة "Controller"
php webman make:controller UserHandler --no-suffix
```

**هيكل الملف الناتج:**
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function index(Request $request)
    {
        return response('hello user');
    }
}
```

**ملاحظات:**
- تُوضع وحدات التحكم افتراضياً في `app/controller/`
- تُضاف لاحقة وحدة التحكم من التكوين تلقائياً
- يطلب تأكيد الاستبدال إذا كان الملف موجوداً (كما في الأوامر الأخرى)

<a name="make-model"></a>
### make:model

إنشاء فئة نموذج من جدول قاعدة البيانات. يدعم Laravel ORM و ThinkORM.

**الاستخدام:**
```bash
php webman make:model [name]
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | No | اسم فئة النموذج، يمكن حذفه في الوضع التفاعلي |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--plugin` | `-p` | إنشاء النموذج في دليل الملحق المحدد |
| `--path` | `-P` | الدليل الهدف (نسبة إلى جذر المشروع) |
| `--table` | `-t` | تحديد اسم الجدول؛ يُنصح به عندما لا يتبع الاسم الاتفاقية |
| `--orm` | `-o` | اختيار ORM: `laravel` أو `thinkorm` |
| `--database` | `-d` | تحديد اسم اتصال قاعدة البيانات |
| `--force` | `-f` | استبدال الملف الموجود |

**ملاحظات المسارات:**
- افتراضي: `app/model/` (التطبيق الرئيسي) أو `plugin/<plugin>/app/model/` (الملحق)
- `--path` نسبة إلى جذر المشروع، مثل `plugin/admin/app/model`
- عند استخدام `--plugin` و `--path` معاً، يجب أن يشير كلاهما إلى نفس الدليل

**أمثلة:**
```bash
# إنشاء نموذج User في app/model
php webman make:model User

# تحديد اسم الجدول و ORM
php webman make:model User -t wa_users -o laravel

# إنشاء في الملحق
php webman make:model AdminUser -p admin

# مسار مخصص
php webman make:model User -P plugin/admin/app/model
```

**الوضع التفاعلي:** عند حذف الاسم، يدخل في التدفق التفاعلي: اختيار الجدول → إدخال اسم النموذج → إدخال المسار. يدعم: Enter لعرض المزيد، `0` لإنشاء نموذج فارغ، `/keyword` لتصفية الجداول.

**هيكل الملف الناتج:**
```php
<?php
namespace app\model;

use support\Model;

/**
 * @property integer $id (primary key)
 * @property string $name
 */
class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
    public $timestamps = true;
}
```

تُنشأ تعليقات `@property` تلقائياً من بنية الجدول. يدعم MySQL و PostgreSQL.

<a name="make-crud"></a>
### make:crud

إنشاء نموذج ووحدة تحكم وموثّق من جدول قاعدة البيانات دفعة واحدة، لتشكيل قدرة CRUD كاملة.

**الاستخدام:**
```bash
php webman make:crud
```

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--table` | `-t` | تحديد اسم الجدول |
| `--model` | `-m` | اسم فئة النموذج |
| `--model-path` | `-M` | دليل النموذج (نسبة إلى جذر المشروع) |
| `--controller` | `-c` | اسم فئة وحدة التحكم |
| `--controller-path` | `-C` | دليل وحدة التحكم |
| `--validator` | | اسم فئة الموثّق (يتطلب `webman/validation`) |
| `--validator-path` | | دليل الموثّق (يتطلب `webman/validation`) |
| `--plugin` | `-p` | إنشاء الملفات في دليل الملحق المحدد |
| `--orm` | `-o` | ORM: `laravel` أو `thinkorm` |
| `--database` | `-d` | اسم اتصال قاعدة البيانات |
| `--force` | `-f` | استبدال الملفات الموجودة |
| `--no-validator` | | عدم إنشاء الموثّق |
| `--no-interaction` | `-n` | وضع غير تفاعلي، استخدام القيم الافتراضية |

**تدفق التنفيذ:** عند عدم تحديد `--table`، يدخل في اختيار الجدول تفاعلياً؛ اسم النموذج يُستنتج افتراضياً من اسم الجدول؛ اسم وحدة التحكم يُستنتج من اسم النموذج + لاحقة وحدة التحكم؛ اسم الموثّق يُستنتج من اسم وحدة التحكم بدون لاحقة + `Validator`. المسارات الافتراضية: النموذج `app/model/`، وحدة التحكم `app/controller/`، الموثّق `app/validation/`؛ للملحقات: المجلدات الفرعية المناسبة تحت `plugin/<plugin>/app/`.

**أمثلة:**
```bash
# إنشاء تفاعلي (تأكيد خطوة بخطوة بعد اختيار الجدول)
php webman make:crud

# تحديد اسم الجدول
php webman make:crud --table=users

# تحديد اسم الجدول والملحق
php webman make:crud --table=users --plugin=admin

# تحديد المسارات
php webman make:crud --table=users --model-path=app/model --controller-path=app/controller

# عدم إنشاء الموثّق
php webman make:crud --table=users --no-validator

# غير تفاعلي + استبدال
php webman make:crud --table=users --no-interaction --force
```

**هيكل الملف الناتج:**

النموذج (`app/model/User.php`):
```php
<?php

namespace app\model;

use support\Model;

class User extends Model
{
    protected $connection = 'mysql';
    protected $table = 'users';
    protected $primaryKey = 'id';
}
```

وحدة التحكم (`app/controller/UserController.php`):
```php
<?php

namespace app\controller;

use support\Request;
use support\Response;
use app\model\User;
use app\validation\UserValidator;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create', in: ['body'])]
    public function create(Request $request): Response
    {
        $data = $request->post();
        $model = new User();
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'update', in: ['body'])]
    public function update(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $data = $request->post();
        unset($data['id']);
        foreach ($data as $key => $value) {
            $model->setAttribute($key, $value);
        }
        $model->save();
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }

    #[Validate(validator: UserValidator::class, scene: 'delete', in: ['body'])]
    public function delete(Request $request): Response
    {
        if (!$model = User::find($request->post('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        $model->delete();
        return json(['code' => 0, 'msg' => 'ok']);
    }

    #[Validate(validator: UserValidator::class, scene: 'detail')]
    public function detail(Request $request): Response
    {
        if (!$model = User::find($request->input('id'))) {
            return json(['code' => 1, 'msg' => 'not found']);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $model]);
    }
}
```

الموثّق (`app/validation/UserValidator.php`):
```php
<?php
declare(strict_types=1);

namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:0',
        'username' => 'required|string|max:32'
    ];

    protected array $messages = [];

    protected array $attributes = [
        'id' => 'المفتاح الأساسي',
        'username' => 'اسم المستخدم'
    ];

    protected array $scenes = [
        'create' => ['username', 'nickname'],
        'update' => ['id', 'username'],
        'delete' => ['id'],
        'detail' => ['id'],
    ];
}
```

**ملاحظات:**
- يُتخطى إنشاء الموثّق إذا لم يكن `webman/validation` مثبتاً أو مفعّلاً (التثبيت: `composer require webman/validation`)
- تُنشأ `attributes` في الموثّق تلقائياً من تعليقات حقول قاعدة البيانات؛ بدون تعليقات لا تُنشأ `attributes`
- تدعم رسائل أخطاء الموثّق التعدد اللغوي؛ تُختار اللغة من `config('translation.locale')`

<a name="make-middleware"></a>
### make:middleware

إنشاء فئة وسيط وتسجيلها تلقائياً في `config/middleware.php` (أو `plugin/<plugin>/config/middleware.php` للملحقات).

**الاستخدام:**
```bash
php webman make:middleware <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم الوسيط |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--plugin` | `-p` | إنشاء الوسيط في دليل الملحق المحدد |
| `--path` | `-P` | الدليل الهدف (نسبة إلى جذر المشروع) |
| `--force` | `-f` | استبدال الملف الموجود |

**أمثلة:**
```bash
# إنشاء وسيط Auth في app/middleware
php webman make:middleware Auth

# إنشاء في الملحق
php webman make:middleware Auth -p admin

# مسار مخصص
php webman make:middleware Auth -P plugin/admin/app/middleware
```

**هيكل الملف الناتج:**
```php
<?php
namespace app\middleware;

use Webman\MiddlewareInterface;
use Webman\Http\Response;
use Webman\Http\Request;

class Auth implements MiddlewareInterface
{
    public function process(Request $request, callable $handler) : Response
    {
        return $handler($request);
    }
}
```

**ملاحظات:**
- يُوضع افتراضياً في `app/middleware/`
- يُضاف اسم الفئة تلقائياً إلى ملف تكوين الوسائط لتفعيلها

<a name="make-command"></a>
### make:command

إنشاء فئة أمر وحدة التحكم.

**الاستخدام:**
```bash
php webman make:command <command-name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `command-name` | Yes | اسم الأمر بصيغة `group:action` (مثل `user:list`) |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--plugin` | `-p` | إنشاء الأمر في دليل الملحق المحدد |
| `--path` | `-P` | الدليل الهدف (نسبة إلى جذر المشروع) |
| `--force` | `-f` | استبدال الملف الموجود |

**أمثلة:**
```bash
# إنشاء أمر user:list في app/command
php webman make:command user:list

# إنشاء في الملحق
php webman make:command user:list -p admin

# مسار مخصص
php webman make:command user:list -P plugin/admin/app/command

# استبدال ملف موجود
php webman make:command user:list -f
```

**هيكل الملف الناتج:**
```php
<?php

namespace app\command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('user:list', 'user list')]
class UserList extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('<info>Hello</info> <comment>' . $this->getName() . '</comment>');
        return self::SUCCESS;
    }
}
```

**ملاحظات:**
- يُوضع افتراضياً في `app/command/`

<a name="make-bootstrap"></a>
### make:bootstrap

إنشاء فئة تهيئة للبدء. تُستدعى الدالة `start` تلقائياً عند بدء العملية، وعادةً للتهيئة العامة.

**الاستخدام:**
```bash
php webman make:bootstrap <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم فئة Bootstrap |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--plugin` | `-p` | إنشاء في دليل الملحق المحدد |
| `--path` | `-P` | الدليل الهدف (نسبة إلى جذر المشروع) |
| `--force` | `-f` | استبدال الملف الموجود |

**أمثلة:**
```bash
# إنشاء MyBootstrap في app/bootstrap
php webman make:bootstrap MyBootstrap

# إنشاء بدون تفعيل تلقائي
php webman make:bootstrap MyBootstrap no

# إنشاء في الملحق
php webman make:bootstrap MyBootstrap -p admin

# مسار مخصص
php webman make:bootstrap MyBootstrap -P plugin/admin/app/bootstrap

# استبدال ملف موجود
php webman make:bootstrap MyBootstrap -f
```

**هيكل الملف الناتج:**
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MyBootstrap implements Bootstrap
{
    public static function start($worker)
    {
        $is_console = !$worker;
        if ($is_console) {
            return;
        }
        // ...
    }
}
```

**ملاحظات:**
- يُوضع افتراضياً في `app/bootstrap/`
- عند التفعيل، تُضاف الفئة إلى `config/bootstrap.php` (أو `plugin/<plugin>/config/bootstrap.php` للملحقات)

<a name="make-process"></a>
### make:process

إنشاء فئة عملية مخصصة وكتابتها في `config/process.php` للبدء التلقائي.

**الاستخدام:**
```bash
php webman make:process <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم فئة العملية (مثل MyTcp، MyWebsocket) |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--plugin` | `-p` | إنشاء في دليل الملحق المحدد |
| `--path` | `-P` | الدليل الهدف (نسبة إلى جذر المشروع) |
| `--force` | `-f` | استبدال الملف الموجود |

**أمثلة:**
```bash
# إنشاء في app/process
php webman make:process MyTcp

# إنشاء في الملحق
php webman make:process MyProcess -p admin

# مسار مخصص
php webman make:process MyProcess -P plugin/admin/app/process

# استبدال ملف موجود
php webman make:process MyProcess -f
```

**التدفق التفاعلي:** يُسأل بالتتابع: الاستماع على منفذ؟ → نوع البروتوكول (websocket/http/tcp/udp/unixsocket) → عنوان الاستماع (IP:port أو مسار unix socket) → عدد العمليات. يطلب بروتوكول HTTP أيضاً وضع مدمج أو مخصص.

**هيكل الملف الناتج:**

عملية غير مستمعة (فقط `onWorkerStart`):
```php
<?php
namespace app\process;

use Workerman\Worker;

class MyProcess
{
    public function onWorkerStart(Worker $worker)
    {
        // TODO: Write your business logic here.
    }
}
```

تولّد عمليات الاستماع TCP/WebSocket قوالب ردود `onConnect` و `onMessage` و `onClose` المناسبة.

**ملاحظات:**
- يُوضع افتراضياً في `app/process/`؛ يُكتب تكوين العملية في `config/process.php`
- مفتاح التكوين هو snake_case لاسم الفئة؛ يفشل إذا كان موجوداً مسبقاً
- الوضع المدمج لـ HTTP يعيد استخدام ملف العملية `app\process\Http` ولا ينشئ ملفاً جديداً
- البروتوكولات المدعومة: websocket، http، tcp، udp، unixsocket

## البناء والنشر

<a name="build-phar"></a>
### build:phar

حزم المشروع كملف أرشيف PHAR للتوزيع والنشر.

**الاستخدام:**
```bash
php webman build:phar
```

**البدء:**

انتقل إلى دليل البناء وشغّل

```bash
php webman.phar start
```

**ملاحظات:**
* المشروع المُحزَم لا يدعم reload؛ استخدم restart لتحديث الكود

* لتجنب حجم ملف كبير واستهلاك ذاكرة عالي، اضبط exclude_pattern و exclude_files في config/plugin/webman/console/app.php لاستبعاد الملفات غير الضرورية.

* يشغّل webman.phar إنشاء دليل runtime في نفس الموقع للسجلات والملفات المؤقتة.

* إذا كان المشروع يستخدم ملف .env، ضع .env في نفس دليل webman.phar.

* webman.phar لا يدعم العمليات المخصصة على Windows

* لا تخزّن أبداً ملفات رفع المستخدمين داخل حزمة phar؛ التعامل مع رفع المستخدمين عبر phar:// خطر (ثغرة إعادة التسلسل phar). يجب تخزين رفع المستخدمين بشكل منفصل على القرص خارج phar. انظر أدناه.

* إذا كان المشروع يحتاج رفع ملفات إلى دليل public، استخرج دليل public وضعه في نفس موقع webman.phar واضبط config/app.php:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```
استخدم الدالة المساعدة public_path($relative_path) للحصول على مسار دليل public الفعلي.


<a name="build-bin"></a>
### build:bin

حزم المشروع كملف ثنائي مستقل مع تضمين وقت تشغيل PHP. لا حاجة لتثبيت PHP في البيئة المستهدفة.

**الاستخدام:**
```bash
php webman build:bin [version]
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `version` | No | إصدار PHP (مثل 8.1، 8.2)، افتراضي الإصدار الحالي، الحد الأدنى 8.1 |

**أمثلة:**
```bash
# استخدام إصدار PHP الحالي
php webman build:bin

# تحديد PHP 8.2
php webman build:bin 8.2
```

**البدء:**

انتقل إلى دليل البناء وشغّل

```bash
./webman.bin start
```

**ملاحظات:**
* يُنصح بشدة أن يطابق إصدار PHP المحلي إصدار البناء (مثل PHP 8.1 محلياً → بناء بـ 8.1) لتجنب مشاكل التوافق
* يحمّل البناء مصدر PHP 8 ولا يثبته محلياً؛ لا يؤثر على بيئة PHP المحلية
* webman.bin يعمل حالياً فقط على x86_64 Linux؛ غير مدعوم على macOS
* المشروع المُحزَم لا يدعم reload؛ استخدم restart لتحديث الكود
* .env لا يُحزَم افتراضياً (يتحكم به exclude_files في config/plugin/webman/console/app.php)؛ ضع .env في نفس دليل webman.bin عند البدء
* يُنشأ دليل runtime في دليل webman.bin لملفات السجل
* webman.bin لا يقرأ php.ini الخارجي؛ لإعدادات php.ini مخصصة، استخدم custom_ini في config/plugin/webman/console/app.php
* استبعد الملفات غير الضرورية عبر config/plugin/webman/console/app.php لتجنب حجم حزمة كبير
* بناء الثنائي لا يدعم Swoole coroutines
* لا تخزّن أبداً ملفات رفع المستخدمين داخل الحزمة الثنائية؛ التعامل عبر phar:// خطر (ثغرة إعادة التسلسل phar). يجب تخزين رفع المستخدمين بشكل منفصل على القرص خارج الحزمة.
* إذا كان المشروع يحتاج رفع ملفات إلى دليل public، استخرج دليل public وضعه في نفس موقع webman.bin واضبط config/app.php كما يلي، ثم أعد البناء:
```php
'public_path' => base_path(false) . DIRECTORY_SEPARATOR . 'public',
```

<a name="install"></a>
### install

تشغيل برنامج تثبيت إطار Webman (استدعاء `\Webman\Install::install()`)، لتهيئة المشروع.

**الاستخدام:**
```bash
php webman install
```

## أدوات الأوامر المفيدة

<a name="version"></a>
### version

عرض إصدار workerman/webman-framework.

**الاستخدام:**
```bash
php webman version
```

**ملاحظات:** يُقرأ الإصدار من `vendor/composer/installed.php`؛ يُرجع فشلاً إذا تعذّر القراءة.

<a name="fix-disable-functions"></a>
### fix-disable-functions

إصلاح `disable_functions` في php.ini، وإزالة الدوال المطلوبة لـ Webman.

**الاستخدام:**
```bash
php webman fix-disable-functions
```

**ملاحظات:** يزيل الدوال التالية (ومطابقات البادئة) من `disable_functions`: `stream_socket_server`، `stream_socket_accept`، `stream_socket_client`، `pcntl_*`، `posix_*`، `proc_*`، `shell_exec`، `exec`. يتخطى إذا لم يُعثر على php.ini أو كانت `disable_functions` فارغة. **يعدّل ملف php.ini مباشرة**؛ يُنصح بعمل نسخة احتياطية.

<a name="route-list"></a>
### route:list

عرض جميع المسارات المسجلة بصيغة جدول.

**الاستخدام:**
```bash
php webman route:list
```

**مثال على الإخراج:**
```
+-------+--------+-----------------------------------------------+------------+------+
| URI   | Method | Callback                                      | Middleware | Name |
+-------+--------+-----------------------------------------------+------------+------+
| /user | GET    | ["app\\controller\\UserController","index"] | null       |      |
| /api  | POST   | Closure                                      | ["Auth"]   | api  |
+-------+--------+-----------------------------------------------+------------+------+
```

**أعمدة الإخراج:** URI، Method، Callback، Middleware، Name. تُعرض استدعاءات Closure كـ "Closure".

## إدارة ملحقات التطبيق (app-plugin:*)

<a name="app-plugin-create"></a>
### app-plugin:create

إنشاء ملحق تطبيق جديد، مع إنشاء هيكل الدليل الكامل والملفات الأساسية تحت `plugin/<name>`.

**الاستخدام:**
```bash
php webman app-plugin:create <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم الملحق؛ يجب أن يطابق `[a-zA-Z0-9][a-zA-Z0-9_-]*`، ولا يحتوي على `/` أو `\` |

**أمثلة:**
```bash
# إنشاء ملحق تطبيق باسم foo
php webman app-plugin:create foo

# إنشاء ملحق بشرطة
php webman app-plugin:create my-app
```

**هيكل الدليل الناتج:**
```
plugin/<name>/
├── app/
│   ├── controller/IndexController.php
│   ├── model/
│   ├── middleware/
│   ├── view/index/index.html
│   └── functions.php
├── config/          # app.php, route.php, menu.php, إلخ.
├── api/Install.php  # معالجات التثبيت/إلغاء التثبيت/التحديث
├── public/
└── install.sql
```

**ملاحظات:**
- يُنشأ الملحق تحت `plugin/<name>/`؛ يفشل إذا كان الدليل موجوداً

<a name="app-plugin-install"></a>
### app-plugin:install

تثبيت ملحق التطبيق، بتنفيذ `plugin/<name>/api/Install::install($version)`.

**الاستخدام:**
```bash
php webman app-plugin:install <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم الملحق؛ يجب أن يطابق `[a-zA-Z0-9][a-zA-Z0-9_-]*` |

**أمثلة:**
```bash
php webman app-plugin:install foo
```

<a name="app-plugin-uninstall"></a>
### app-plugin:uninstall

إلغاء تثبيت ملحق التطبيق، بتنفيذ `plugin/<name>/api/Install::uninstall($version)`.

**الاستخدام:**
```bash
php webman app-plugin:uninstall <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم الملحق |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--yes` | `-y` | تخطي التأكيد، التنفيذ مباشرة |

**أمثلة:**
```bash
php webman app-plugin:uninstall foo
php webman app-plugin:uninstall foo -y
```

<a name="app-plugin-update"></a>
### app-plugin:update

تحديث ملحق التطبيق، بتنفيذ `Install::beforeUpdate($from, $to)` و `Install::update($from, $to, $context)` بالترتيب.

**الاستخدام:**
```bash
php webman app-plugin:update <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم الملحق |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--from` | `-f` | الإصدار المصدر، افتراضي الإصدار الحالي |
| `--to` | `-t` | الإصدار الهدف، افتراضي الإصدار الحالي |

**أمثلة:**
```bash
php webman app-plugin:update foo
php webman app-plugin:update foo --from 1.0.0 --to 1.1.0
```

<a name="app-plugin-zip"></a>
### app-plugin:zip

حزم ملحق التطبيق كملف ZIP، الإخراج إلى `plugin/<name>.zip`.

**الاستخدام:**
```bash
php webman app-plugin:zip <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم الملحق |

**أمثلة:**
```bash
php webman app-plugin:zip foo
```

**ملاحظات:**
- يُستبعد تلقائياً `node_modules`، `.git`، `.idea`، `.vscode`، `__pycache__`، إلخ.

## إدارة الملحقات (plugin:*)

<a name="plugin-create"></a>
### plugin:create

إنشاء ملحق Webman جديد (بصيغة حزمة Composer)، مع إنشاء دليل التكوين `config/plugin/<name>` ودليل مصدر الملحق `vendor/<name>`.

**الاستخدام:**
```bash
php webman plugin:create <name>
php webman plugin:create --name <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم حزمة الملحق بصيغة `vendor/package` (مثل `foo/my-admin`)؛ يجب اتباع تسمية حزم Composer |

**أمثلة:**
```bash
php webman plugin:create foo/my-admin
php webman plugin:create --name foo/my-admin
```

**الهيكل الناتج:**
- `config/plugin/<name>/app.php`: تكوين الملحق (يتضمن مفتاح `enable`)
- `vendor/<name>/composer.json`: تعريف حزمة الملحق
- `vendor/<name>/src/`: دليل مصدر الملحق
- إضافة تلقائية لتعيين PSR-4 إلى `composer.json` في جذر المشروع
- تنفيذ `composer dumpautoload` لتحديث التحميل التلقائي

**ملاحظات:**
- يجب أن يكون الاسم بصيغة `vendor/package`: حروف صغيرة، أرقام، `-`، `_`، `.`، ويجب أن يحتوي على `/` واحد
- يفشل إذا كانت `config/plugin/<name>` أو `vendor/<name>` موجودة
- خطأ إذا تم تمرير المعلمة و `--name` بقيم مختلفة

<a name="plugin-install"></a>
### plugin:install

تنفيذ برنامج تثبيت الملحق (`Install::install()`)، ونسخ موارد الملحق إلى دليل المشروع.

**الاستخدام:**
```bash
php webman plugin:install <name>
php webman plugin:install --name <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم حزمة الملحق بصيغة `vendor/package` (مثل `foo/my-admin`) |

**الخيارات:**

| الخيار | الوصف |
|--------|-------------|
| `--name` | تحديد اسم الملحق كخيار؛ استخدم إما هذا أو المعلمة |

**أمثلة:**
```bash
php webman plugin:install foo/my-admin
php webman plugin:install --name foo/my-admin
```

<a name="plugin-uninstall"></a>
### plugin:uninstall

تنفيذ برنامج إلغاء تثبيت الملحق (`Install::uninstall()`)، وإزالة موارد الملحق من المشروع.

**الاستخدام:**
```bash
php webman plugin:uninstall <name>
php webman plugin:uninstall --name <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم حزمة الملحق بصيغة `vendor/package` |

**الخيارات:**

| الخيار | الوصف |
|--------|-------------|
| `--name` | تحديد اسم الملحق كخيار؛ استخدم إما هذا أو المعلمة |

**أمثلة:**
```bash
php webman plugin:uninstall foo/my-admin
```

<a name="plugin-enable"></a>
### plugin:enable

تفعيل الملحق، بتعيين `enable` إلى `true` في `config/plugin/<name>/app.php`.

**الاستخدام:**
```bash
php webman plugin:enable <name>
php webman plugin:enable --name <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم حزمة الملحق بصيغة `vendor/package` |

**الخيارات:**

| الخيار | الوصف |
|--------|-------------|
| `--name` | تحديد اسم الملحق كخيار؛ استخدم إما هذا أو المعلمة |

**أمثلة:**
```bash
php webman plugin:enable foo/my-admin
```

<a name="plugin-disable"></a>
### plugin:disable

إلغاء تفعيل الملحق، بتعيين `enable` إلى `false` في `config/plugin/<name>/app.php`.

**الاستخدام:**
```bash
php webman plugin:disable <name>
php webman plugin:disable --name <name>
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم حزمة الملحق بصيغة `vendor/package` |

**الخيارات:**

| الخيار | الوصف |
|--------|-------------|
| `--name` | تحديد اسم الملحق كخيار؛ استخدم إما هذا أو المعلمة |

**أمثلة:**
```bash
php webman plugin:disable foo/my-admin
```

<a name="plugin-export"></a>
### plugin:export

تصدير تكوين الملحق والمجلدات المحددة من المشروع إلى `vendor/<name>/src/`، وإنشاء `Install.php` للتعبئة والنشر.

**الاستخدام:**
```bash
php webman plugin:export <name> [--source=path]...
php webman plugin:export --name <name> [--source=path]...
```

**المعلمات:**

| المعلمة | مطلوب | الوصف |
|----------|----------|-------------|
| `name` | Yes | اسم حزمة الملحق بصيغة `vendor/package` |

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--name` | | تحديد اسم الملحق كخيار؛ استخدم إما هذا أو المعلمة |
| `--source` | `-s` | المسار للتصدير (نسبة إلى جذر المشروع)؛ يمكن تحديده عدة مرات |

**أمثلة:**
```bash
# تصدير الملحق، يتضمن افتراضياً config/plugin/<name>
php webman plugin:export foo/my-admin

# تصدير إضافي لـ app، config، إلخ.
php webman plugin:export foo/my-admin --source app --source config
php webman plugin:export --name foo/my-admin -s app -s config
```

**ملاحظات:**
- يجب أن يتبع اسم الملحق تسمية حزم Composer (`vendor/package`)
- إذا كانت `config/plugin/<name>` موجودة ولم تكن في `--source`، تُضاف تلقائياً إلى قائمة التصدير
- يتضمن `Install.php` المُصدَّر `pathRelation` للاستخدام مع `plugin:install` / `plugin:uninstall`
- يتطلب `plugin:install` و `plugin:uninstall` وجود الملحق في `vendor/<name>`، مع فئة `Install` وثابت `WEBMAN_PLUGIN`

## إدارة الخدمات

<a name="start"></a>
### start

بدء عمليات Webman. وضع DEBUG افتراضي (واجهة أمامية).

**الاستخدام:**
```bash
php webman start
```

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--daemon` | `-d` | البدء في وضع DAEMON (خلفية) |

<a name="stop"></a>
### stop

إيقاف عمليات Webman.

**الاستخدام:**
```bash
php webman stop
```

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--graceful` | `-g` | إيقاف سلس؛ انتظار انتهاء الطلبات الحالية قبل الخروج |

<a name="restart"></a>
### restart

إعادة تشغيل عمليات Webman.

**الاستخدام:**
```bash
php webman restart
```

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--daemon` | `-d` | التشغيل في وضع DAEMON بعد إعادة التشغيل |
| `--graceful` | `-g` | إيقاف سلس قبل إعادة التشغيل |

<a name="reload"></a>
### reload

إعادة تحميل الكود دون توقف. مناسب لإعادة التحميل الساخن بعد تحديث الكود.

**الاستخدام:**
```bash
php webman reload
```

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--graceful` | `-g` | إعادة تحميل سلس؛ انتظار انتهاء الطلبات الحالية قبل إعادة التحميل |

<a name="status"></a>
### status

عرض حالة تشغيل عمليات Webman.

**الاستخدام:**
```bash
php webman status
```

**الخيارات:**

| الخيار | الاختصار | الوصف |
|--------|----------|-------------|
| `--live` | `-d` | عرض التفاصيل (حالة حية) |

<a name="connections"></a>
### connections

الحصول على معلومات اتصالات عمليات Webman.

**الاستخدام:**
```bash
php webman connections
```
