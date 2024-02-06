# الكوروتين

> **متطلبات الكوروتين**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> أمر ترقية webman `composer require workerman/webman-framework ^1.5.0`
> أمر ترقية workerman `composer require workerman/workerman ^5.0.0`
> يتطلب Fiber الكوروتين التثبيت `composer require revolt/event-loop ^1.0.0`

# مثال
### تأخير الاستجابة

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // نوم لمدة 1.5 ثانية
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` شبيه بـ `sleep()` المدمج في PHP، لكن الفرق هو أن `Timer::sleep()` لن يحجب العملية.

### إرسال طلب HTTP

> **ملاحظة**
> يتطلب التثبيت `composer require workerman/http-client ^2.0.0`

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // إرسال طلب غير محجوب بطريقة متزامنة
        return $response->getBody()->getContents();
    }
}
```
بنفس الطريقة، يتم طلب `$client->get('http://example.com')` بغير استجابة، وهذا يمكن استخدامه في webman لإرسال طلبات http بشكل غير محجوب، مما يعزز من أداء التطبيق.

لمزيد من المعلومات، راجع [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### إضافة صنف support\Context

يُستخدم الصنف `support\Context` لتخزين بيانات سياق الطلب، وعند اكتمال الطلب، سيتم حذف بيانات السياق ذاتيًا. وهذا يعني أن عمر بيانات السياق تتبع عمر الطلب. يُدعم `support\Context` بيئات الكوروتين Fiber وSwoole وSwow.

### الكوروتين Swoole
بعد تثبيت إضافة Swoole (المتطلبات: swoole>=5.0)، يُمكن تفعيل الكوروتين Swoole عبر تكوين config/server.php كالتالي
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

لمزيد من المعلومات، راجع [أحداث workerman](https://www.workerman.net/doc/workerman/appendices/event.html)

### التلوث العالمي للمتغيرات

تحظر بيئة الكوروتين تخزين معلومات الحالة ذات الصلة بالطلب في المتغيرات العالمية أو المتغيرات الستاتيكية، لأن ذلك قد يؤدي إلى التلوث العالمي للمتغيرات، على سبيل المثال

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

عندما يتم تعيين عدد العمليات إلى 1، وعند إرسال طلبين متتاليين  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
نتوقع أن يكون نتيجة الطلبين هي `lilei` و `hanmeimei` على التوالي، ولكن في الواقع يكون الناتج للطلب الثاني هو `hanmeimei`.
ذلك لأن الطلب الثاني قام بإعادة تعيين المتغير الستاتيكي `$name`، وعندما يستيقظ الطلب الأول بعد السبات، سيتم إرجاع المتغير الستاتيكي `$name` الذي أُعيد تعيينه بقيمة `hanmeimei`.

**الطريقة الصحيحة هي استخدام context لتخزين بيانات حالة الطلب**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**المتغيرات المحلية لا تسبب تلوث البيانات**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
لأن `$name` هو متغير محلي ولا يمكن للكوروتينات الوصول إليه، لذا يكون استخدام المتغيرات المحلية آمنًا في بيئة الكوروتين.

# حول الكوروتين
الكوروتين ليست حلاً نهائياً، إدخال الكوروتين يعني الحفاظ على دقة تلوث البيانات في المتغيرات العالمية أو المتغيرات الستاتيكية، ويتطلب ضبط سياق الطلب. بالإضافة إلى ذلك، تصبح تصحيح الأخطاء في بيئة الكوروتين أكثر تعقيدا من البرمجة المحجوبة.

في الواقع، فإن البرمجة المحجوبة في webman سريعة بما فيه الكفاية، وفقًا لبيانات [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2) التي تظهر نتائج ضغط ثلاث دورات لمدة ثلاث سنوات، أداء برمجة الاستدعاء المحجوب في webman في أعمال قواعد البيانات يفوق أداء إطار العمل الويب gin و echo لغة go بمقدار تقريباً مرتين، ويفوق أداء إطار العمل tranditional laravel بمقدار تقريباً 40 مرة.

![](../../assets/img/benchemarks-go-sw.png?)

عندما تكون قواعد البيانات والريدز داخل الشبكة الداخلية، قد تكون أداء البرمجة المحجوبة مرتفعة جداً مقارنة مع الكوروتين، وذلك لأن تكلفة إنشاء الكوروتين وجدولة وتدميره قد تكون أعلى من تكلفة تغيير العمليات في هذه الأوقات. لذا، قد لا يكون إدخال الكوروتين مفيداً بشكل كبير في هذه الحالة.

# متى يُستخدم الكوروتين
عند وجود أداء رديئ في العمليات مثل الحاجة للوصول إلى واجهة برمجة التطبيقات الخارجية، يُمكن استخدام [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) بطريقة الكوروتين لإطلاق استدعاءات HTTP الغير محجوبة بشكل غير متزامن، مما يعزز قدرة التطبيق على التزامن.


