# البدء السريع

يعتمد نموذج webman على [Eloquent ORM](https://laravel.com/docs/7.x/eloquent). كل جدول في قاعدة البيانات لديه "نموذج" مقابل للتفاعل مع هذا الجدول. يمكنك استخدام النموذج للاستعلام عن بيانات الجدول وإدراج سجلات جديدة في الجدول.

قبل البدء ، تأكد من تكوين اتصال قاعدة البيانات في `config/database.php`.

> ملاحظة: يتطلب دعم مراقب النموذج في Eloquent ORM استيراد إضافي `composer require "illuminate/events"` [مثال](#مراقب-النموذج)

## مثال
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * اسم الجدول المرتبط بالنموذج
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * إعادة تعريف المفتاح الرئيسي ، الافتراضي هو id
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * تشير ما إذا كان يتم صيانة البصمة الزمنية تلقائيًا
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## اسم الجدول 
يمكنك تحديد جدول البيانات المخصص من خلال تعريف خاصية الجدول في النموذج:
```php
class User extends Model
{
    /**
     * اسم الجدول المرتبط بالنموذج
     *
     * @var string
     */
    protected $table = 'user';
}
```

## المفتاح الرئيسي
سيفترض Eloquent أيضًا أن كل جدول بيانات لديه عمود مفتاح رئيسي يسمى id. يمكنك تحديد مفتاح رئيسي محمي باستخدام خاصية $primaryKey لإعادة الكتابة لهذا الافتراضي.
```php
class User extends Model
{
    /**
     * إعادة تعريف المفتاح الرئيسي ، الافتراضي هو id
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

يفترض Eloquent أن المفتاح الرئيسي هو قيمة صحيحة متزايدة تلقائيًا ، وهذا يعني أن المفتاح الرئيسي سيتم تحويله تلقائيًا إلى نوع int. إذا كنت ترغب في استخدام مفتاح غير متزايد أو غير رقمي ، فيجب عليك تعيين
$incrementing إلى false علنا.
```php
class User extends Model
{
    /**
     * تشير ما إذا كان مفتاح النموذج متزايدًا تلقائيًا
     *
     * @var bool
     */
    public $incrementing = false;
}
```

إذا كان مفتاحك الرئيسي ليس عددًا صحيحًا ، فيجب أن تقوم بتعيين خاصية $keyType المحمية على النموذج إلى string:
```php
class User extends Model
{
    /**
     * "النوع" الذي تزداد فيه الهوية تلقائيًا.
     *
     * @var string
     */
    protected $keyType = 'string';
}
```

## البصمة الزمنية
بشكل افتراضي ، يفترض Eloquent أن هناك حقلي created_at و updated_at في جدول البيانات الخاص بك. إذا كنت لا ترغب في إدارة هذين الحقلين تلقائيًا من قبل Eloquent ، يجب ضبط خاصية $timestamps في النموذج على false:
```php
class User extends Model
{
    /**
     * تشير ما إذا كان من المفترض إدارة البصمة الزمنية تلقائياً
     *
     * @var bool
     */
    public $timestamps = false;
}
```
إذا كنت بحاجة إلى تخصيص تنسيق البصمة الزمنية ، فيمكنك ضبط خاصية $dateFormat في النموذج الخاص بك. تحدد هذه الخاصية كيفية تخزين الخصائص التاريخية في قاعدة البيانات ، وكيفية تسلسل النموذج كمصفوفة أو JSON:
```php
class User extends Model
{
    /**
     * تنسيق تخزين البصمة الزمنية
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```

إذا كنت بحاجة إلى تخصيص أسماء حقول تسجيل الوقت ، فيمكنك ضبط قيم أثناء تشغيل الثوابت CREATED_AT و UPDATED_AT في النموذج:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## اتصال قاعدة البيانات
بشكل افتراضي ، سيستخدم نموذج Eloquent اتصال قاعدة بيانات التطبيق الافتراضي. إذا كنت ترغب في تحديد اتصال مختلف للنموذج ، يمكنك ضبط خاصية $connection:
```php
class User extends Model
{
    /**
     * اسم اتصال النموذج
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## قيم الافتراضي للسمات
إذا كنت ترغب في تحديد قيم افتراضية لبعض السمات في النموذج ، يمكنك تعريف خاصية $attributes في النموذج:
```php
class User extends Model
{
    /**
     * قيم الافتراضي للنموذج.
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## استرجاع النماذج
بمجرد إنشاء نموذج وجدول البيانات المرتبط به ، يمكنك الآن الاستعلام عن البيانات من قاعدة البيانات. تخيل كل نموذج Eloquent كبنّاء استعلام قوي ، يمكنك استخدامه للاستعلام بشكل أسرع عن جداول البيانات المرتبطة به. على سبيل المثال:
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> نصيحة: نظرًا لأن نماذج Eloquent هي أيضًا بناءات الاستعلام ، يجب أيضًا قراءة [بناء الاستعلامات](queries.md) لمعرفة جميع الطرق المتاحة. يمكنك استخدام هذه الأساليب في استعلامات Eloquent.

## تطبيق القيود الإضافية
سيعيد الطريقة all في Eloquent كل النتائج الموجودة في النموذج. نظرًا لأن كل نموذج Eloquent يعمل كبنّاء استعلام ، يمكنك أيضًا إضافة شروط البحث ، ثم استخدام الطريقة get للحصول على نتائج البحث:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```

## إعادة تحميل النموذج
يمكنك استخدام الطرق fresh و refresh لإعادة تحميل النموذج. ستقوم الطريقة fresh بإعادة استرداد النموذج من قاعدة البيانات. لن يتأثر النموذج الحالي:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

ستستخدم الطريقة refresh البيانات الجديدة من قاعدة البيانات لإعادة تعيين النموذج الحالي. بالإضافة إلى ذلك ، سيتم إعادة تحميل العلاقات المحملة بالفعل:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## المجموعات
يمكن أن تستخدم الطرق all و get في Eloquent للاستعلام عن العديد من النتائج ، والتي تعيد `Illuminate\Database\Eloquent\Collection` instance. تقدم فئة `Collection` مجموعة كبيرة من الوظائف المساعدة لمعالجة نتائج Eloquent:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## استخدام المؤشر
يسمح طريقة cursor بمرور قاعدة البيانات باستخدام مؤشر ، حيث يتم تنفيذ الاستعلام مرة واحدة فقط. عند معالجة كميات كبيرة من البيانات ، يمكن أن تقلل طريقة المؤشر بشكل كبير من استخدام الذاكرة:
```php
foreach (app\model\User::where('sex', 1')->cursor() as $user) {
    //
}
```

تعيد طريقة المؤشر مثيل `Illuminate\Support\LazyCollection`. تتيح مجموعات "الخمول" [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) لك استخدام معظم الطرق في مجموعة Laravel مع تحميل نموذج واحد فقط إلى الذاكرة في كل مرة:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## الاستعلامات الفرعية
يوفر Eloquent دعمًا متقدمًا للاستعلامات الفرعية ، يمكنك استخدامها لاستخلاص المعلومات من الجداول ذات الصلة باستخدام استعلام واحد. على سبيل المثال ، فلنفترض أن لدينا جدول وجهات destinations وجدول رحلات الطيران إلى الوجهة flights. يحتوي جدول الرحلات على حقل arrival_at ليشير إلى موعد وصول الرحلة إلى الوجهة.

من خلال استخدام select و addSelect methods المقدمة من خلال ميزة الاستعلامات الفرعية ، يمكننا بواسطة الاستعلام الواحد استعلام جميع الوجهات destinations وحصول على اسم آخر رحلة تصل إلى كل وجهة:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## فرز وفق الاستعلام الفرعي
بالإضافة إلى ذلك ، يدعم استخدام orderBy function من مباني الاستعلام الفرعية. يمكننا استخدام هذه الميزة لفرز جميع الوجهات وفقًا لوقت وصول آخر رحلة إلى الوجهة. وبالتالي ، يمكن أن يتم تنفيذ هذا فقط في استعلام واحد:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```

## الاستعلام عن نموذج / مجموعة فردية
بالإضافة إلى استرداد جميع السجلات من الجدول المحدد ، يمكنك استخدام طرق find و first أو firstWhere لاسترداد سجل واحد. تعيد هذه الطرق مثيل النموذج الفردي بدلاً من مجموعة النماذج:
```php
// البحث عن نموذج باستخدام المفتاح الرئيسي...
$flight = app\model\Flight::find(1);

// العثور على أول نموذج مطابق للشرط...
$flight = app\model\Flight::where('active', 1)->first();

// العثور على أول نموذج مطابق للشرط بسرعة...
$flight = app\model\Flight::firstWhere('active', 1);
```

يمكنك أيضًا استخدام مصفوفة المفاتيح الرئيسية كوسيطة لدعوة الطريقة find ، حيث سترد مجموعة السجلات المطابقة:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

في بعض الأحيان قد تر
## جمع البحث
يمكنك استخدام أساليب العدد، المجموع، والأقصى المقدمة بواسطة بنّاء الاستعلامات للتحكم في الجمعيات. هذه الأساليب ستُرجع القيم الصحيحة بدلاً من حالة نموذجية واحدة:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## الإدراج
لإضافة سجل جديد إلى قاعدة البيانات، يجب أولاً إنشاء نموذج جديد، تعيين الخصائص للنموذج، ثم استدعاء الطريقة «save»:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * إضافة سجل جديد في جدول المستخدمين
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // التحقق من الطلب

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

سيتم ضبط الطابع الزمني لإنشاء (created_at) والتحديث (updated_at) تلقائيًا (في حال كان خاصية $timestamps في النموذج مفعلة)، ولا حاجة للتعيين اليدوي.

## التحديث
يُمكن استخدام طريقة «save» لتحديث نموذج موجود بالفعل في قاعدة البيانات. لتحديث النموذج، يجب استرجاعه أولاً، ثم تعيين الخصائص المراد تحديثها، وبعد ذلك استدعاء طريقة «save». بالمثل، سيتم تحديث الطابع الزمني للتحديث (updated_at) تلقائيًا، ولا حاجة للتعيين يدويًا:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## التحديث الجماعي
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## فحص تغيير الخصائص
توفر Eloquent طرق isDirty, isClean, وwasChanged لفحص الحالة الداخلية للنموذج وتحديد كيفية تغير خصائصه منذ تحميله أول مرة. طريقة isDirty تحدد ما إذا تم تغيير أي سمة منذ تحميل النموذج. يُمكنك تمرير اسم السمة المحددة لتحديد ما إذا كانت السمة مغشوشة بشكل محدد. الطريقة isClean مضادة لطريقة isDirty، وتقبل أيضًا معلمات سمة اختيارية:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';

$user->isDirty(); // true
$user->isDirty('title'); // true
$user->isDirty('first_name'); // false

$user->isClean(); // false
$user->isClean('title'); // false
$user->isClean('first_name'); // true

$user->save();

$user->isDirty(); // false
$user->isClean(); // true
```
الطريقة wasChanged تحدد ما إذا تم تغيير أي سمة منذ آخر حفظ للنموذج خلال دورة الطلب الحالي. يُمكنك أيضًا تمرير اسم السمة للتحقق من ما إذا تم تغيير السمة المحددة:
```php
$user = User::create([
    'first_name' => 'Taylor',
    'last_name' => 'Otwell',
    'title' => 'Developer',
]);

$user->title = 'Painter';
$user->save();

$user->wasChanged(); // true
$user->wasChanged('title'); // true
$user->wasChanged('first_name'); // false
```

## السمات الدفعية
يُمكنك أيضًا استخدام الطريقة create لحفظ نموذج جديد. ستعيد هذه الطريقة نموذجًا مُتوفرًا. ومع ذلك، قبل استعمالها، يجب تحديد السمات التي يُمكن تحديثها بشكل دفعي أو منعها في نموذجك، لأن جميع نماذج Eloquent بشكل افتراضي لا يمكن أن تتلقى مدخلات تأتي بشكل دفعي.

عندما يقوم المستخدم بإدخال معلمة HTTP غير متوقعة، وتغيير حقل في قاعدة البيانات لا تريد تغييره، تحدث ثغرة دفعية. على سبيل المثال: قد يقوم مستخدم شرير بإدخال معلمة is_admin من خلال طلب HTTP، ثم استخدامها في طريقة create لرفع مستواه إلى مشرف.

لذلك، يُجب أن تحدد السمات التي يُمكن تحديثها بشكل دفعي على نموذجك قبل البدء. يُمكنك تحقيق ذلك من خلال السمة fillable أو المحروسة guarded. السمة fillable تحتوي على سمات يُمكن تحديثها بشكل دفعي، مما يجعلها ما يشبه "القائمة البيضاء" للتحديثات الدفعية. على سبيل المثال: لتمكين تحديث خاصية الاسم في نموذج Flight:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * السمات المتاحة لتحديثها بشكل دفعي.
     *
     * @var array
     */
    protected $fillable = ['name'];
}

```

بمجرد تحديد السمات التي يُمكن تحديثها بشكل دفعي، يُمكنك استخدام الطريقة create لإدخال بيانات جديدة إلى قاعدة البيانات. ستعيد هذه الطريقة نموذجًا مُحفوظًا:
```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```

إذا كان لديك نموذج مُتاح، يُمكنك تمرير مصفوفة إلى الطريقة fill لتحديث السمات:
```php
$flight->fill(['name' => 'Flight 22']);
```

يُمكن اعتبار $fillable كـ "القائمة البيضاء" للتحديثات الدفعية. وبالمثل، يُمكن استخدام السمة المحروسة guarded. السمة guarded تحتوي على السمات التي لا يُسمح تحديثها بشكل دفعي. بمعنى آخر، ستعمل السمة guarded بشكل مشابه لـ "القائمة السوداء". ملاحظة: يُمكن استخدام إحدى السمتين fillable أو guarded فقط، ولا يُمكن استخدامهما معًا في وقت واحد.

في المثال أدناه، يمكن تحديث جميع السمات ما عدا السعر بشكل دفعي:
```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * السمات غير المسموح بها بالتحديث الدفعي.
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

إذا كنت ترغب في السماح بتحديث جميع السمات بشكل دفعي، يُمكنك تعيين $guarded إلى مصفوفة فارغة:
```php
/**
 * السمات غير المسموح بها بالتحديث الدفعي.
 *
 * @var array
 */
protected $guarded = [];
```

## طرق الإنشاء الأخرى
firstOrCreate/ firstOrNew
هناك اثنان من الأساليب الممكن استخدامها لتحديث الدفعي: firstOrCreate و firstOrNew. ستقوم طريقة firstOrCreate بالبحث في قاعدة البيانات باستخدام مفتاح / قيمة معينين. إذا لم يوجد نموذج متطابق في قاعدة البيانات، فسيتم إدراج سجل واحد يحتوي على الخاصية المحددة كما الخاصية الاختيارية الثانية.

الطريقة firstOrNew تُحاول أيضًا البحث في قاعدة البيانات بناءً على الخصائص المحددة. ومع ذلك، إذا فشلت الطريقة في العثور على نموذج مطابق، ستُعيد نموذجًا جديدًا. ملاحظة: سيتم حفظ النموذج الذي تم إعادته بواسطة الطريقة firstOrNew بشكل يدوي. يجب عليك استدعاء طريقة «save» يدوياً للحفظ:

```php
// البحث عن رحلة باسم معين، وإن لم تجدها فسيتم إنشاء...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// البحث عن رحلة بإسم معين، أو إنشاء نموذج بالإضافة إلى الخصائي المؤجل ووقت الوصول
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// البحث عن رحلة بإسم معين، وإن لم تجدها فسيتم إعادة نموذج جديد...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// البحث عن رحلة بإسم معين، أو إنشاء نموذج جديد بالإضافة إلى الخصائي المؤجل ووقت الوصول
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```

يُمكن أيضًا أن تواجه حالات ترغب فيها في تحديث نموذج موجود أو في إنشاء نموذج جديد إذا لم يكن موجودًا. يُمكن استخدام طريقة updateOrCreate للقيام بذلك. بشكل مماثل لطريقة firstOrCreate، تُقوم updateOrCreate بالحفاظ على النموذج، لذا لا حاجة لاستدعاء الطريقة «save»:
```php
// إذا وجدت رحلة من أوكلاند إلى سان دييجو، سيتم تحديد السعر عند 99 دولار.
// إذا لم يكن هناك نموذج مطابق، سيتم إنشاء واحد.
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## حذف النموذج
يُمكن استدعاء طريقة «delete» على نموذج لحذف النموذج:
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## حذف النموذج باستخدام المفتاح الأساسي
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## حذف النموذج باستخدام الاستعلام
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## استنساخ النموذج
يُمكن استخدام الطريقة replicate لاستنساخ نموذج جديد غير مُحفوظ في قاعدة البيانات. عندما تتشترك النماذج في العديد من السمات المشتركة، تكون هذه الطريقة مفيدة للغاية:
```php
$shipping = App\Address::create([
    'type' => 'shipping',
    'line_1' => '123 Example Street',
    'city' => 'Victorville',
    'state' => 'CA',
    'postcode' => '90001',
]);

$billing = $shipping->replicate()->fill([
    'type' => 'billing'
]);

$billing->save();
```

## مقارنة النماذج
قد تحتاج أحيانًا إلى التحقق مما إذا كان نموذجان متشابهان. يُمكن استخدام الطريقة is للتحقق بسرعة مما
