# مكون مهمات التخطيط الزمني crontab

## workerman/crontab

### الوصف

`workerman/crontab` شبيهة بـ crontab في نظام Linux ولكن الاختلاف الرئيسي أن `workerman/crontab` تدعم الجدولة بدقة الثواني.

شرح الوقت:

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ يوم الأسبوع (0 - 6) (الأحد=0)
|   |   |   |   +------ الشهر (1 - 12)
|   |   |   +-------- يوم الشهر (1 - 31)
|   |   +---------- ساعة (0 - 23)
|   +------------ دقيقة (0 - 59)
+-------------- ثانية (0-59) [يمكن حذفها، إذا تم حذف 0، فإن أصغر وحدة زمنية تكون الدقيقة]
```

### عنوان المشروع

https://github.com/walkor/crontab

### التثبيت

```php
composer require workerman/crontab
```

### الاستخدام

**الخطوة الأولى: إنشاء ملف العمليات `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // تنفيذ كل ثانية
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // تنفيذ كل 5 ثواني
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // تنفيذ كل دقيقة
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // تنفيذ كل 5 دقائق
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // تنفيذ في الثانية الأولى من كل دقيقة
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // تنفيذ في الساعة 7:50 كل يوم، تم حذف الثانية هنا
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**الخطوة الثانية: تكوين ملف العمليات ليبدأ مع webman**

افتح ملف التكوين `config/process.php` وأضف التكوين التالي:

```php
return [
    ....تكوينات أخرى، يتم حذفها هنا....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**الخطوة الثالثة: إعادة تشغيل webman**

> ملاحظة: لن يتم تنفيذ مهام التخطيط الزمني على الفور، فسيتم بدء تنفيذها في الدقيقة التالية

### الوصف
crontab ليست غير متزامنة، على سبيل المثال، إذا كان هناك عملية task تعيد تعيين مهام A و B، حيث يتم تنفيذ كل منهما مرة واحدة كل ثانية، ولكن إذا كانت المهمة A تأخذ 10 ثوانٍ للتنفيذ، فإن المهمة B يجب أن تنتظر حتى اكتمال تنفيذ A، مما يؤدي إلى تأخير تنفيذ B.
إذا كانت العملية حساسة لفترة الزمن، فيجب وضع مهام التخطيط الزمني الحساسة في عملية منفصلة لتشغيلها بشكل منفصل، لتجنب تأثير باقي المهام التخطيط الزمني عليها. على سبيل المثال في ملف `config/process.php`:

```php
return [
    ....تكوينات أخرى، يتم حذفها هنا....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
يرجى وضع المهام التخطيط الزمني الحساسة للوقت في `process/Task1.php`، ووضع المهام التخطيط الزمني الأخرى في `process/Task2.php`

### المزيد
لمزيد من شرح تكوين `config/process.php`، يرجى الرجوع إلى  [Custom Process](../process.md)
