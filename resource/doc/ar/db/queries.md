# مولد الاستعلامات
## الحصول على كافة الصفوف
```php
<?php
namespace app\controller;

use support\Request;
use support\Db;

class UserController
{
    public function all(Request $request)
    {
        $users = Db::table('users')->get();
        return view('user/all', ['users' => $users]);
    }
}
```

## الحصول على أعمدة محددة
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## الحصول على صف واحد
```php
$user = Db::table('users')->where('name', 'John')->first();
```
## الحصول على عمود واحد
```php
$titles = Db::table('roles')->pluck('title');
```
تحديد قيمة الحقل id كفهرس
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## الحصول على قيمة واحدة (حقل)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## الحصول على القيم المكررة
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## تقسيم النتائج إلى مجموعات
إذا كنت بحاجة لمعالجة آلاف السجلات في قاعدة البيانات، فسحب جميع هذه البيانات في وقت واحد سيستغرق الكثير من الوقت ومن الممكن أن يؤدي إلى تجاوز الذاكرة، في هذه الحالة يمكنك استخدام طريقة chunkById. تقوم هذه الطريقة بالحصول على مجموعة صغيرة من نتائج الاستعلام وتمريرها إلى الدالة الفرعية لمعالجتها. على سبيل المثال، يمكننا تقسيم جميع بيانات جدول users إلى مجموعة صغيرة تحتوي على 100 سجل لمعالجة كل مرة:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
يمكنك وقف الحصول على النتائج المقسمة عن طريق إرجاع قيمة false داخل الدالة الفرعية.
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // معالجة السجلات...

    return false;
});
```

> ملاحظة: لا تقم بحذف البيانات داخل الدالة الفرعية لأن ذلك قد يؤدي إلى عدم تضمين بعض السجلات في نتيجة الاستعلام

## التجميع
يوفر مولد الاستعلامات أيضًا مجموعة متنوعة من الوظائف للتجميع مثل count, max،min، avg،sum، وغيرها.
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## التحقق من وجود السجل
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## التعبيرات الأصلية
النموذج
```php
selectRaw($expression, $bindings = [])
```
قد تحتاج في بعض الأحيان إلى استخدام تعبيرات أصلية في الاستعلام. يمكنك استخدام `selectRaw()` لإنشاء تعبير أصلي:
```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

بالمثل، يتم توفير `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` لاستخدام التعبيرات الأصلية.

`Db::raw($value)` يستخدم أيضًا لإنشاء تعبير أصلي، ولكنه لا يحتوي على وظيفة تجميد البيانات، لذلك يجب أخذ الحيطة عند استخدامه لتجنب ثغرات الحقن الخاصة بلغة SQL.
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```
## جملة الانضمام
```php
// join
$users = Db::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')
            ->select('users.*', 'contacts.phone', 'orders.price')
            ->get();

// leftJoin            
$users = Db::table('users')
            ->leftJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// rightJoin
$users = Db::table('users')
            ->rightJoin('posts', 'users.id', '=', 'posts.user_id')
            ->get();

// crossJoin    
$users = Db::table('sizes')
            ->crossJoin('colors')
            ->get();
```
## جملة الاتحاد
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## جملة الأين
النموذج
```php
where($column, $operator = null, $value = null)
```
المعامل الأول هو اسم العمود، المعامل الثاني هو أي من مشغلات دعم نظام قاعدة البيانات، والمعامل الثالث هو القيمة التي يجب مقارنة العمود بها.
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// عندما يكون المشغل هو القوانين يمكن حذفه، لذلك فإن العبارة السابقة تعطي نفس النتيجة
$users = Db::table('users')->where('votes', 100)->get();

$users = Db::table('users')
                ->where('votes', '>=', 100)
                ->get();

$users = Db::table('users')
                ->where('votes', '<>', 100)
                ->get();

$users = Db::table('users')
                ->where('name', 'like', 'T%')
                ->get();
```

يمكنك أيضًا تمرير مصفوفة الشروط إلى دالة where:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

ويكون المعامل الأول في دالة orWhere مشابهاً لدالة where:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

يمكنك تمرير إغلاق إلى دالة orWhere كمعامل أول:
```php
// SQL: select * from users where votes > 100 or (name = 'Abigail' and votes > 50)
$users = Db::table('users')
            ->where('votes', '>', 100)
            ->orWhere(function($query) {
                $query->where('name', 'Abigail')
                      ->where('votes', '>', 50);
            })
            ->get();

```
whereBetween / orWhereBetween للتحقق مما إذا كانت قيمة الحقل بين قيمتين معطاة:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween للتحقق مما إذا كانت قيمة الحقل خارج نطاق القيمتين المعطيتين:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn للتحقق مما إذا كانت قيمة الحقل موجودة في مصفوفة محددة:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull للتحقق من أن قيمة الحقل موجودة كقيمة NULL:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull للتحقق من أن قيمة الحقل غير موجودة كقيمة NULL:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime لمقارنة قيمة الحقل بتاريخ محدد:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn لمقارنة قيمتي حقلين إذا كانوا متساوين:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// يمكنك أيضًا تمرير مشغل المقارنة
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// يمكن أيضًا تمرير مصفوفة 
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```
تجميع المعاملات
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
               $query->where('votes', '>', 100)
                     ->orWhere('title', '=', 'Admin');
           })
           ->get();
```
whereExists
```php
// select * from users where exists ( select 1 from orders where orders.user_id = users.id )
$users = Db::table('users')
           ->whereExists(function ($query) {
               $query->select(Db::raw(1))
                     ->from('orders')
                     ->whereRaw('orders.user_id = users.id');
           })
           ->get();
```
## orderBy
```php
$users = Db::table('users')
                ->orderBy('name', 'desc')
                ->get();
```
## ترتيب عشوائي
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> تأثير ترتيب البيانات عشوائياً يكون كبيراً على أداء الخادم، لذلك لا يُنصح باستخدامه

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// يمكنك تمرير عدة معاملات لدالة groupBy
$users = Db::table('users')
                ->groupBy('first_name', 'status')
                ->having('account_id', '>', 100)
                ->get();
```

## offset / limit
```php
$users = Db::table('users')
                ->offset(10)
                ->limit(5)
                ->get();
```
الإدراج
إدراج سجل واحد
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
إدراج سجلات متعددة
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## تزايد الهوية
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> ملاحظة: عند استخدام PostgreSQL، سيقوم الأسلوب insertGetId افتراضيًا بتحويل الحقل id إلى اسم حقل التزايد التلقائي. إذا كنت ترغب في الحصول على معرف من مصفوفة أخرى ، يمكنك تمرير اسم الحقل كلمة مرور ثانوية إلى الطريقة insertGetId.

## تحديث
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## تحديث أو إدراج
قد ترغب في بعض الأحيان في تحديث سجلات موجودة في قاعدة البيانات أو إنشاؤها إذا لم تكن موجودة:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
سيحاول الأسلوب updateOrInsert أولاً استخدام مفتاح وقيمة الوسيطة الأولى للبحث عن سجلات قائمة في قاعدة البيانات. إذا تم العثور على السجل، سيتم استخدام القيمة في الوسيطة الثانية لتحديث السجل. وإذا لم يتم العثور على السجل، سيتم إدراج سجل جديد ببيانات من كل من المصفوفتين.

## زيادة ونقص
تستقبل كلتا الطرقتين متغيرًا على الأقل: العمود الذي يجب تعديله. والمتغير الثاني اختياري، يستخدم للتحكم في كمية الزيادة أو النقص:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
يمكنك أيضًا تحديد الحقول التي ترغب في تحديثها خلال العملية:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## حذف
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
إذا كنت بحاجة لتفريغ الجدول، يمكنك استخدام الأسلوب truncate الذي سيقوم بحذف كافة الصفوف وإعادة تعيين هوية التزايد إلى الصفر:
```php
Db::table('users')->truncate();
```

## قفل تشاؤمي
تحتوي بناء الاستعلام أيضًا على بعض الدوال التي يمكن أن تساعدك في تحقيق "قفل مشترك" عند استخدام جملة الاستعلام select. إذا كنت ترغب في تحقيق "قفل مشترك" في الاستعلام، يمكنك استخدام الأسلوب sharedLock. يمنع القفل المشترك تعديل الأعمدة المحددة حتى يتم تأكيد الصفقة:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
أو يمكنك استخدام الأسلوب lockForUpdate. ويمنع القفل إمكانية التعديل أو استخراج الصفوف من قبل قفل مشترك آخر:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## تصحيح
يمكنك استخدام الأسلوب dd أو dump لطباعة نتائج الاستعلام أو جملة SQL. الأسلوب dd يعرض معلومات التصحيح ويوقف تنفيذ الطلب. بينما الأسلوب dump يعرض معلومات التصحيح أيضًا، ولكن لا يتوقف عن تنفيذ الطلب:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **ملاحظة**
> يتطلب التصحيح تثبيت `symfony/var-dumper`. يمكنك تثبيته باستخدام الأمر `composer require symfony/var-dumper`.
