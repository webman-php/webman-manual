# العمليات المخصصة

في webman ، يمكنك تخصيص الاستماع أو العمليات بنفس الطريقة التي يمكنك تخصيص workerman.

> **ملاحظة**
> يجب على مستخدمي Windows استخدام `php windows.php` لتشغيل webman وإعداد العمليات المخصصة.

## تخصيص خدمة HTTP
في بعض الأحيان قد يكون لديك متطلبات خاصة تتطلب تعديلًا على كود النواة لخدمة webman HTTP. يمكنك في هذه الحالة استخدام العمليات المخصصة.

على سبيل المثال ، قم بإنشاء app\Server.php

```php
<?php

namespace app;

use Webman\App;

class Server extends App
{
    // هنا يتم إعادة كتابة الأساليب في نوع App من Webman\App
}
```

أضف الإعدادات التالية في `config/process.php`

```php
use Workerman\Worker;

return [
    // ... نوع الإعدادات الأخرى...
    
    'my-http' => [
        'handler' => app\Server::class,
        'listen' => 'http://0.0.0.0:8686',
        'count' => 8, // عدد العمليات
        'user' => '',
        'group' => '',
        'reusePort' => true,
        'constructor' => [
            'request_class' => \support\Request::class, // تعيين فئة الطلب
            'logger' => \support\Log::channel('default'), // مثيل السجل
            'app_path' => app_path(), // موقع الدليل التطبيق
            'public_path' => public_path() // موقع الدليل العام
        ]
    ]
];
```

> **نصيحة**
> إذا كنت ترغب في إيقاف خدمة HTTP المدمجة في webman ، كل ما عليك فعله هو ضبط `listen=>''` في `config/server.php`
## مثال تخصيص استماع WebSocket

قم بإنشاء `app/Pusher.php`

```php
<?php
namespace app;

use Workerman\Connection\TcpConnection;

class Pusher
{
    public function onConnect(TcpConnection $connection)
    {
        echo "onConnect\n";
    }

    public function onWebSocketConnect(TcpConnection $connection, $http_buffer)
    {
        echo "onWebSocketConnect\n";
    }

    public function onMessage(TcpConnection $connection, $data)
    {
        $connection->send($data);
    }

    public function onClose(TcpConnection $connection)
    {
        echo "onClose\n";
    }
}
```
> ملاحظة: جميع الخصائص onXXX هي عامة

أضف الإعدادات التالية في `config/process.php`

```php
return [
    // ... تخطي إعدادات العمليات الأخرى...
    
    // websocket_test اسم العملية
    'websocket_test' => [
        // هنا يتم تحديد فئة العملية، أي الفئة Pusher المعرفة أعلاه
        'handler' => app\Pusher::class,
        'listen'  => 'websocket://0.0.0.0:8888',
        'count'   => 1,
    ],
];
```

## مثال عملية مخصصة غير الاستماع

قم بإنشاء `app/TaskTest.php`

```php
<?php
namespace app;

use Workerman\Timer;
use support\Db;

class TaskTest
{
  
    public function onWorkerStart()
    {
        // يتم فحص قاعدة البيانات كل 10 ثوانٍ للتحقق من وجود مستخدمين جدد قاموا بالتسجيل
        Timer::add(10, function(){
            Db::table('users')->where('regist_timestamp', '>', time()-10)->get();
        });
    }
    
}
```
أضف الإعدادات التالية في `config/process.php`

```php
return [
    // ... تخطي إعدادات العمليات الأخرى
    
    'task' => [
        'handler'  => app\TaskTest::class
    ],
];
```

>ملاحظة: إذا تم تخطي الاستماع فإن العملية لا تقوم بالاستماع إلى أي منفذ، وإذا تم تخطي العدد فإن عدد العمليات يكون افتراضيًا 1.

## شرح ملف الإعدادات

يتم تعريف عملية كاملة بالتفصيل كما يلي:
```php
return [
    // ... 
    
    // websocket_test اسم العملية
    'websocket_test' => [
        // هنا تم تحديد فئة العملية
        'handler' => app\Pusher::class,
        // بروتوكول الاستماع وعنوان IP والمنفذ (اختياري)
        'listen'  => 'websocket://0.0.0.0:8888',
        // عدد العمليات (اختياري، الافتراضي 1)
        'count'   => 2,
        // المستخدم الذي يقوم بتشغيل العملية (اختياري، الافتراضي مستخدم الحالي)
        'user'    => '',
        // مجموعة المستخدمين الذين يقومون بتشغيل العملية (اختياري، الافتراضي مجموعة المستخدم الحالية)
        'group'   => '',
        // إذا كانت العملية قابلة لإعادة التحميل (اختياري، الافتراضي صواب)
        'reloadable' => true,
        // هل تم تمكين reusePort (اختياري، يتطلب PHP >= 7.0، يكون الافتراضي صواب)
        'reusePort'  => true,
        // النقل (اختياري، عند الحاجة لتمكين SSL، يتم تعيينها إلى ssl، الافتراضي tcp)
        'transport'  => 'tcp',
        // السياق (اختياري، عندما يكون النقل هو SSL، يجب تمرير مسار الشهادة)
        'context'    => [], 
        // معلمات بناء جملة دالة البنائي هنا لفئة process\Pusher::class (اختياري)
        'constructor' => [],
    ],
];
```

## توضيح
عمليات تخصيص webman في الواقع هي تغليف بسيط للعمليات في workerman. إنها تفصل التكوين عن الأعمال وتقوم بتنفيذ دوال `onXXX` التي تقوم بها workerman من خلال طرق الفئة، وغير ذلك فإن الاستخدام الآخر هو بالكامل مشابه لـ workerman.
