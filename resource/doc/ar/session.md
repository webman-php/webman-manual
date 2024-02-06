# إدارة الجلسة

## مثال
```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        $name = $request->get('name');
        $session = $request->session();
        $session->set('name', $name);
        return response('hello ' . $session->get('name'));
    }
}
```

احصل على `Workerman\Protocols\Http\Session` مثيل من خلال `$request->session();` وقم بزيادة أو تعديل أو حذف بيانات الجلسة باستخدام أساليب المثيل.

> ملحوظة: عند تدمير كائن الجلسة، سيتم حفظ بيانات الجلسة تلقائياً، لذلك لا تقم بحفظ كائن `$request->session()` في مصفوفة عامة أو كعضو في الفئة مما يؤدي إلى عدم القدرة على حفظ الجلسة.

## الحصول على جميع بيانات الجلسة
```php
$session = $request->session();
$all = $session->all();
```
سيعيد مصفوفة. إذا لم تكن هناك أي بيانات جلسة، ستُعاد مصفوفة فارغة.


## الحصول على قيمة محددة في الجلسة
```php
$session = $request->session();
$name = $session->get('name');
```
إذا لم تكن البيانات موجودة، سيُعاد القيمة null.

يمكنك أيضاً تمرير قيمة افتراضية كمعامل ثانوي لطريقة get، إذا لم يتم العثور على قيمة مطابقة في مصفوفة الجلسة، سيتم إعادة القيمة الافتراضية. على سبيل المثال:
```php
$session = $request->session();
$name = $session->get('name', 'tom');
```


## حفظ الجلسة
استخدم طريقة `set` لحفظ بيانات معينة.
```php
$session = $request->session();
$session->set('name', 'tom');
```
لا تُعيد `set` أي قيمة، سيتم حفظ الجلسة تلقائياً عند تدمير كائن الجلسة.

عند حفظ عدة قيم، استخدم طريقة `put`.
```php
$session = $request->session();
$session->put(['name' => 'tom', 'age' => 12]);
```
بنفس الشكل، `put` لا تُعيد قيمة.


## حذف بيانات الجلسة
عند حذف بيانات جلسة معينة، استخدم طريقة `forget`.
```php
$session = $request->session();
// حذف عنصر واحد
$session->forget('name');
// حذف عدة عناصر
$session->forget(['name', 'age']);
```

بالإضافة إلى ذلك، توفر النظام طريقة حذف أيضاً، والفرق بينهما هو أن طريقة `delete` تعمل فقط على حذف عنصر واحد.
```php
$session = $request->session();
// نفس $session->forget('name');
$session->delete('name');
```

## الحصول على قيمة الجلسة وحذفها
```php
$session = $request->session();
$name = $session->pull('name');
```
نفس الكود التالي
```php
$session = $request->session();
$value = $session->get($name);
$session->delete($name);
```
إذا لم تكن الجلسة المطابقة موجودة، سيُعاد القيمة null.


## حذف جميع بيانات الجلسة
```php
$request->session()->flush();
```
ليس له قيمة الإرجاع، سيتم حذف بيانات الجلسة من التخزين تلقائياً عند تدمير كائن الجلسة.


## التحقق مما إذا كانت بيانات الجلسة موجودة
```php
$session = $request->session();
$has = $session->has('name');
```
عندما لا تكون الجلسة المطابقة موجودة أو قيمة الجلسة المطابقة تساوي القيمة null، سيُعاد false.


```
$session = $request->session();
$has = $session->exists('name');
```
الكود السابق هو أيضاً مستخدم للتحقق مما إذا كانت بيانات الجلسة موجودة، الفرق هو أنه عندما تساوي قيمة العنصر المطابقة القيمة null، ستُعاد القيمة true.


## دالة المساعدة session()
> 09-12-2020 إضافة جديدة

يوفر webman دالة المساعدة `session()` لإكمال نفس الوظيفة.
```php
// الحصول على مثيل الجلسة
$session = session();
// مكافئ
$session = $request->session();

// الحصول على قيمة معينة
$value = session('key', 'default');
// مكافئ
$value = session()->get('key', 'default');
// مكافئ
$value = $request->session()->get('key', 'default');

// تعيين قيمة للجلسة
session(['key1'=>'value1', 'key2' => 'value2']);
// مكافئ
session()->put(['key1'=>'value1', 'key2' => 'value2']);
// مكافئ
$request->session()->put(['key1'=>'value1', 'key2' => 'value2']);
```

## ملف الاعدادات
ملف الاعدادات للجلسة في `config/session.php`، ومحتواه شبيه بالتالي:
```php
use Webman\Session\FileSessionHandler;
use Webman\Session\RedisSessionHandler;
use Webman\Session\RedisClusterSessionHandler;

return [
    // FileSessionHandler::class أو RedisSessionHandler::class أو RedisClusterSessionHandler::class 
    'handler' => FileSessionHandler::class,
    
    // إذا كان شرط الشاشة `FileSessionHandler::class` فإن القيمة ملف
    // إذا كان شرط الشاشة `RedisSessionHandler::class` فإن القيمة ريدس
    // إذا كان شرط الشاشة `RedisClusterSessionHandler::class` فإن القيمة مجموعة ريدس، تُسمى كذلك مجموعة ريدس
    'type'    => 'file',

    // اعداد مختلفة لكل شرط عرض
    'config' => [
        // اعداد الشاشة `file`
        'file' => [
            'save_path' => runtime_path() . '/sessions',
        ],
        // اعداد الشاشة `redis`
        'redis' => [
            'host'      => '127.0.0.1',
            'port'      => 6379,
            'auth'      => '',
            'timeout'   => 2,
            'database'  => '',
            'prefix'    => 'redis_session_',
        ],
        'redis_cluster' => [
            'host'    => ['127.0.0.1:7000', '127.0.0.1:7001', '127.0.0.1:7001'],
            'timeout' => 2,
            'auth'    => '',
            'prefix'  => 'redis_session_',
        ]
        
    ],

    'session_name' => 'PHPSID', // اسم ملف التسجيل

    // === اعداد الشروط متوفرة في webman-framework>=1.3.14 workerman>=4.0.37 ===
    'auto_update_timestamp' => false,  // هل يتم تجديد الجلسة تلقائياً. القيمة الافتراضية لها هي إيقاف التشغيل
    'lifetime' => 7*24*60*60,          // مدة انتهاء الجلسة
    'cookie_lifetime' => 365*24*60*60, // مدة انتهاء كوكي ملف التسجيل
    'cookie_path' => '/',              // مسار كوكي ملف التسجيل
    'domain' => '',                    // نطاق كوكي ملف التسجيل
    'http_only' => true,               // هل تفعيل خاصية httpOnly. القيمة الافتراضية لها هي تفعيل
    'secure' => false,                 // هل تفعيل جلسة https فقط. القيمة الافتراضية لها هي إيقاف التشغيل
    'same_site' => '',                 // للحماية من الهجمات CSRF وتتبع المستخدم، يُمكن اختيار القيم: strict / lax / none
    'gc_probability' => [1, 1000],     // احتمالية اتخاذ الإجراء للجلسة
];
```

> **ملاحظة** 
> بدأ webman اعتبارًا من الإصدار 1.4.0 في تغيير أسماء أساليب SessionHandler، حيث تغيرت من:
> use Webman\FileSessionHandler;  
> use Webman\RedisSessionHandler;  
> use Webman\RedisClusterSessionHandler;  
> إلى  
> use Webman\Session\FileSessionHandler;  
> use Webman\Session\RedisSessionHandler;  
> use Webman\Session\RedisClusterSessionHandler;  

## إعداد مدة الصلاحية
عندما يكون ويب مان-فريم ورك أقل من 1.3.14، تحتاج مدة صلاحية الجلسة في ويب مان لتكون محددة في `php.ini`.

```
session.gc_maxlifetime = x
session.cookie_lifetime = x
session.gc_probability = 1
session.gc_divisor = 1000
```

في حالة تحديد المدة الصالحية لتكون 1440 ثانية، يجب أن يكون التكوين كالتالي:
```
session.gc_maxlifetime = 1440
session.cookie_lifetime = 1440
session.gc_probability = 1
session.gc_divisor = 1000
```

> **ملاحظة**
> يمكنك استخدام الأمر `php --ini` للعثور على موقع `php.ini`
