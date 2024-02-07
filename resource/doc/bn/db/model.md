# দ্রুত শুরু

webman মডেলটি [Eloquent ORM](https://laravel.com/docs/7.x/eloquent) ভিত্তিতে তৈরি করা হয়েছে। প্রতিটি ডাটাবেস টেবিলের জন্য একটি "মডেল" আছে যা ঐ টেবিলে সাথে সাম্প্রদায়িক। আপনি মডেল দ্বারা ডাটাবেস টেবিলে ডেটা চেক করতে পারেন, এবং নতুন রেকর্ড টেবিলে ঢুকিয়ে দিতে পারেন।

শুরু করার আগে, দয়া করে নিশ্চিত করুন যে `config/database.php` তে ডাটাবেস সংযোগ কনফিগার করা আছে।

> লক্ষ্য করুন: Eloquent ORM মডেল অবশ্যই মডেল অবদানকারী সমর্থন করতে ইভেন্টস অতিরিক্ত আমদানি করতে হবে `composer require "illuminate/events"` [উদাহরণ](#মডেল-অবদানকারী)

## উদাহরণ
```php
<?php
namespace app\model;

use support\Model;

class User extends Model
{
    /**
     * মডেল সঙ্গে সংবাদ সংক্রান্ত টেবিল নাম
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * পুনরায় সংজ্ঞায়িত প্রাথমিক কী, ডিফল্ট হল ‘id’
     *
     * @var string
     */
    protected $primaryKey = 'uid';

    /**
     * কি স্বয়ংক্রিয় রকমের ড্রাফ্ট করতে
     *
     * @var bool
     */
    public $timestamps = false;
}
```

## টেবিলের নাম
আপনি মডেলে টেবিল নির্ধারণ করতে table বৈশিষ্ট্য উল্লেখ করে করতে পারেন:
```php
class User extends Model
{
    /**
     * মডেল সঙ্গে সংবাদ সংক্রান্ত টেবিল নাম
     *
     * @var string
     */
    protected $table = 'user';
}
```

## প্রাথমিক কী
Eloquent এটিও অনুমান করবে যে প্রতিটি ডাটাবেস টেবিলে id নামের একটি প্রাথমিক কী কলাম আছে। আপনি এই সাম্প্রদায়িক $primaryKey বৈশিষ্ট্য পুনরায় সংজ্ঞায়িত করতে পারেন।
```php
class User extends Model
{
    /**
     * পুনরায় সংজ্ঞায়িত প্রাথমিক কী, ডিফল্ট হল ‘id’
     *
     * @var string
     */
    protected $primaryKey = 'uid';
}
```

Eloquent প্রাথমিক কীকে একটা স্বয়ংক্রিয় ইউম্যান মান ধরতে ধরে, এটা মানে ডিফল্টভাবে প্রাথমিক কীঐ স্বয়ংক্রিয় ভাবে int ধরা হবে। আপনি স্বাভাবিকভাবে বা সংখ্যা না থাকা প্রাথমিক কী ব্যবহার করতে চান তবে আপনাকে অব্যাহত $incrementing বৈশিষ্ট্যকে false হিসাবে সেট করতে হবে
```php
class User extends Model
{
    /**
     * মডেল প্রাথমিক কী যথার্থতাকে কি নাকি আরোপ করে
     *
     * @var bool
     */
    public $incrementing = false;
}
```

আপনার প্রাথমিক কী যদি একটি পুণ্য না হয়, তবে আপনার মডেলে সুরক্ষিত সংজ্ঞায়িত $keyType বৈশিষ্ট্যকে string হিসাবে সেট করতে হবে:
```php
class User extends Model
{
    /**
     * স্বয়ংক্রিয়ভাবে প্রাথমিক কী ধরের "ধরন"।
     *
     * @var string
     */
    protected $keyType = 'string';
}
```
## টাইমস্ট্যাম্প
ডিফল্টভাবে, Eloquent আশা করে যে আপনার ডেটা টেবিলে created_at এবং updated_at ফিল্ড থাকবে। এদিকে, যদি আপনি চান না যে Eloquent নিজে স্বয়ংক্রিয়ে এই দুটি কলাম পরিচালনা করে, তবে মডেলে $timestamps প্রপার্টিতে false সেট করুন:
```php
class User extends Model
{
    /**
     * স্বয়ংক্রিয়ভাবে সময়ট্যামেইন্ট পরিচালনা করণ করা উচিত
     *
     * @var bool
     */
    public $timestamps = false;
}
```
যদি আপনি স্বনির্দিষ্ট টাইমস্ট্যাম্প ফরম্যাটের প্রয়োজন হয়, তবে আপনার মডেলে $dateFormat প্রপার্টিটি সেট করুন। এই প্রপার্টি তার ভাবে নির্ধারণ করে যে ডেটা প্রপার্টি কীভাবে ডাটাবেজে সংরক্ষিত হবে, এবং মডেলকে অ্যারে বা JSON হিসেবে সিরিয়ালাইজ করা হবে:
```php
class User extends Model
{
    /**
     * সংরক্ষণ ফরম্যাট্টিং টাইমস্ট্যাম্প
     *
     * @var string
     */
    protected $dateFormat = 'U';
}
```
যদি আপনি স্টোর করার সময়সীমা ফিল্ডের নাম সংস্করণ করতে চান, তবে মডেলে CREATED_AT এবং UPDATED_AT কনস্টেন্টের মান সেট করুন:
```php
class User extends Model
{
    const CREATED_AT = 'creation_date';
    const UPDATED_AT = 'last_update';
}
```

## ডাটাবেস সংযোগ
ডিফল্টভাবে, Eloquent মডেলটি আপনার অ্যাপ্লিকেশনের ডিফল্ট ডেটাবেস সংযোগটি ব্যবহার করবে। যদি আপনি মডেলের জন্য একটি বিভিন্ন সংযোগ নির্ধারণ করতে চান, তবে $connection প্রপার্টিটি সেট করুন:
```php
class User extends Model
{
    /**
     * মডেলের সংযোগ নাম
     *
     * @var string
     */
    protected $connection = 'connection-name';
}
```

## ডিফল্ট গুণমান
মডেলের কিছু গুণমান জন্য ডিফল্ট মান সেট করতে চাইলে, মডেলে $attributes প্রপার্টিটি সেট করতে পারেন:
```php
class User extends Model
{
    /**
     * মডেলের ডিফল্ট গুণমান।
     *
     * @var array
     */
    protected $attributes = [
        'delayed' => false,
    ];
}
```

## মডেল অনুসন্ধান
মডেল এবং তার মুল ডাটাবেস টেবিল তৈরি করার পর, আপনি ডাটাবেস থেকে ডেটা সন্ধান করতে পারেন। প্রতিটি Eloquent মডেলকে একটি শক্তিশালী ক्यুয়ারী বিল্ডার হিসেবে ভাবা যেতে পারে, যা আপনি থাকবেন তার সংস্থানিক ডাটাবেস টেবিল সাথে দ্রুত অনুসন্ধান করার জন্য ব্যবহার করতে পারবেন। যেমন: 
```php
$users = app\model\User::all();

foreach ($users as $user) {
    echo $user->name;
}
```
> পরামর্শ: একবারই Eloquent মডেলটিও কুয়েরি বিল্ডার, আপনাকে দেখতে হবে [কুয়েরি বিল্ডার](queries.md) এখানে সব প্রয়োজনীয় পদক্ষেপ উপলব্ধ। আপনি এই পদক্ষেপগুলি Eloquent অনুসন্ধানে ব্যবহার করতে পারবেন।

## অতিরিক্ত সীমাবদ্ধতা
Eloquent এর সমস্ত পদক্ষেপ মডেলের সমস্ত ফলাফল ফিরের করবে। কারণ প্রতিটি Eloquent মডেল একটি কুয়েরি বিল্ডার হিসেবে কাজ করে, তার মাধ্যমে আপনি অনুসন্ধানের শর্তগুলি যোগ করতে পারেন এবং তারপর get মেথড ব্যবহার করে অনুসন্ধান ফলাফল পেতে পারেন:
```php
$users = app\model\User::where('name', 'like', '%tom')
               ->orderBy('uid', 'desc')
               ->limit(10)
               ->get();
```
## মডেল পুনরায় লোড করুন
আপনি fresh এবং refresh মেথড ব্যবহার করে মডেল পুনরায় লোড করতে পারেন। fresh মেথড ডাটাবেস থেকে মডেল পুনরায় পেতে ব্যবহার করা হয়। বর্তমান মডেল ইনস্ট্যান্স প্রভাবিত হয় না:
```php
$user = app\model\User::where('name', 'tom')->first();

$fresh_user = $user->fresh();
```

refresh মেথড ডাটাবেস থেকে নতুন ডেটা দিয়ে বর্তমান মডেলে পুনরায় ভ্যালু দেয়। তাছাড়া, ইতিমধ্যে লোড করা রিলেশনগুলি পুনরায় লোড হবে:
```php
$user = app\model\User::where('name', 'tom')->first();

$user->name = 'jerry';

$user = $user->fresh();

$user->name; // "tom"
```

## কালেকশন
Eloquent এর all এবং get মেথড দিয়ে একাধিক ফলাফল অনুসন্ধান করতে পারে এবং `Illuminate\Database\Eloquent\Collection` ইনস্ট্যান্স প্রদান করে। `Collection` ক্লাস ব্যবহার করে Eloquent রেজাল্ট নিয়ে বিভিন্ন সহায়ক ফাংশন প্রদান করে:
```php
$users = $users->reject(function ($user) {
    return $user->disabled;
});
```

## কার্সর ব্যবহার
cursor মেথড ব্যবহার করে আপনি ডাটাবেস পার করতে পারেন, এটি শুধুমাত্র একবার কুয়েরি চালায়। বড় পরিমাণে ডেটা প্রসেসিং করার সময়, cursor মেথড মেমোরি ব্যবহারের পরিমাণ খুব কমায় তা:
```php
foreach (app\model\User::where('sex', 1)->cursor() as $user) {
    //
}
```

cursor এ `Illuminate\Support\LazyCollection` ইনস্ট্যান্স রিটার্ন করে। [Lazy collections](https://laravel.com/docs/7.x/collections#lazy-collections) আপনাকে Laravel কালেকশনের বেশিরভাগ মেথড ব্যবহার করতে দেয় এবং প্রতিবার শুধুমাত্র একটি মডেল মেমোরিতে লোড করে:
```php
$users = app\model\User::cursor()->filter(function ($user) {
    return $user->id > 500;
});

foreach ($users as $user) {
    echo $user->id;
}
```

## সাব-কুয়েরি ব্যবহার
Eloquent উচ্চতর সাব-কুয়েরি সাপোর্ট প্রদান করে, আপনি সাব-কুয়েরি ভাবে সংশ্লিষ্ট টেবিল থেকে তথ্য প্রাপ্ত করতে পারেন। উদাহরণ হিসাবে, ধরা যাক আমাদের একটি গন্তব্য টেবিল destinations এবং গন্তব্যে যাওয়ার জন্য উড়ান টেবিল flights আছে। উড়ান টেবিলে একটি arrival_at ফিল্ড থাকে, যা দেখায় উড়ান গন্তব্যে কখন পৌঁছায়।

সাব-কুয়েরি ব্যবহার করে প্রদান করা select এবং addSelect মেথড ব্যবহার করে, আমরা একটি সিঙ্গেল স্টেটমেন্ট দিয়ে সমস্ত গন্তব্য destinations এবং প্রতিটি গন্তব্যের জন্য শেষ ফ্লাইটের নাম জানতে পারি:
```php
use app\model\Destination;
use app\model\Flight;

return Destination::addSelect(['last_flight' => Flight::select('name')
    ->whereColumn('destination_id', 'destinations.id')
    ->orderBy('arrived_at', 'desc')
    ->limit(1)
])->get();
```

## সাব-কুয়েরি অনুযায়ী সাজানো
এছাড়াও, ক্যুয়েরি বিল্ডারের orderBy ফাংশনও সাব-কুয়েরি সমর্থন করে। আমরা এই ফিচার ব্যবহার করে সমস্ত গন্তব্যকে শেষ উড়ান গন্তব্যের সময় অনুযায়ী সাজিয়ে দেতে পারি। এটা ছাড়াও, এটি ডাটাবেসকে একটি কোনও সিঙ্গেল কুয়েরি চালাবে:
```php
return Destination::orderByDesc(
    Flight::select('arrived_at')
        ->whereColumn('destination_id', 'destinations.id')
        ->orderBy('arrived_at', 'desc')
        ->limit(1)
)->get();
```
## একটি মডেল / সংগ্রহ চেক করুন
দিতে বিশেষ করে তালিকা থেকে সমস্ত রেকর্ড পুনরায় খোঁজ করা ছাড়া, আপনি find, first বা firstWhere মেথড ব্যবহার করে একটি রেকর্ড চেক করতে পারেন। এই মেথডগুলি একটি একক মডেল ইনস্ট্যান্স ফেরত দেয়, সংগ্রহের পরিবর্তে:
```php
// প্রাথমিক কী ব্যবহার করে একটি মডেল খুঁজুন...
$flight = app\model\Flight::find(1);

// প্রথম মডেল যা জোরে কাজ করে তা খুঁজুন...
$flight = app\model\Flight::where('active', 1)->first();

// প্রথম মডেলের জন্য অ্যাক্টিভ অনুসন্ধানে সারাংশিক ব্যবহার করুন...
$flight = app\model\Flight::firstWhere('active', 1);
```

আপনি একাধিক প্রাথমিক শব্দের সাথে ব্যবহার করে একটি প্রাথমিক ধারণার পিছনে একটি মডেল ফ্রিত এবং ক্রিয়াশীল মেথডে অ্যারের মত ফিরে যাবে:
```php
$flights = app\model\Flight::find([1, 2, 3]);
```

সময়ে সময় আপনি প্রথম ফলাফল খুঁজে পাওয়ার পরে বােটায় অন্যান্য কার্য করার আবেশ করতে পারেন। firstOr মেথডটি ফলাফল খুঁজে পাওয়ার পরে প্রথম ফলাফল ফেরত দেবে যদি কোনও ফলাফল না পাওয়া যায়, তাহলে নির্দিষ্ট কলব্যাক করা হবে। কলব্যবহার ফলাফল হিসেবে ফিরে দেওয়া হবে:
```php
$model = app\model\Flight::where('legs', '>', 100)->firstOr(function () {
        // ...
});
```
firstOr মেথডটি আকারের সাথে কালাম অ্যারে গ্রহণ করে:
```php
$model = app\modle\Flight::where('legs', '>', 100)
            ->firstOr(['id', 'legs'], function () {
                // ...
            });
```

## "পাওয়া যায়নি" অসুবিধা
সময়ে সময় আপনি প্রথম ফলাফল খুঁজে না পেলে একটি অসুবিধায় প্রকাশ করা চান। এটি নিয়ন্ত্রণাধীনগন্য ও পথে এটি খুঁজে পেলে প্রথম প্রতিষ্ঠান থেকে প্রকাশ করিবে Illuminate\Database\Eloquent\ModelNotFoundException অসুবিধা:
```php
$model = app\modle\Flight::findOrFail(1);
$model = app\modle\Flight::where('legs', '>', 100)->firstOrFail();
```

## সংগ্রহ পাওয়া
আপনি পরিশোধ বিতরণকারী দ্বারা প্রদত্ত count, sum এবং max মেথড, এবং অন্যান্য সংগ্রহ কার্য ব্যবহার করে সংগ্রহ পরিত্যাগ করতে পারেন। এই মেথডগুলি শুধুমাত্র ঠিকমতি স্কেলার মান ফেরত দেয় দ্বিতীয় মডেল ইনস্ট্যান্স:
```php
$count = app\modle\Flight::where('active', 1)->count();

$max = app\modle\Flight::where('active', 1)->max('price');
```

## প্রবেশ করান
একটি রেকর্ড লাগবে তাদের পরিবর্তন করতে, প্রথমে একটি নতুন মডেল ইনস্ট্যান্স তৈরি করুন, ইনস্ট্যান্সে মান সেট করুন, এবং পরে save মেথডটি কল করুন:
```php
<?php

namespace app\controller;

use app\model\User;
use support\Request;
use support\Response;

class FooController
{
    /**
     * একটি নতুন রেকর্ড টেবিলে যুক্ত করুন
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // অনুরোধ যাচাই করুন

        $user = new User;

        $user->name = $request->get('name');

        $user->save();
    }
}
```

সৃষ্টির তারিখ এবং আপডেটেড_এট সময় ট্যাম্প স্বয়ংক্রিয় ভাবে সেট করা হবে (যদি মডেলে $timestamps প্রোপার্টি সত্য হয়), ম্যানুয়ালি মান সেট করা প্রয়োজন নেই।
## আপডেট
save মেথডটি ডাটাবেসে অবস্থিত মডেল আপডেট করার জন্য ব্যবহার করা যাবে। মডেলকে আপডেট করতে, আপনাকে প্রথমে এটি অনুসন্ধান করে, আপডেট করতে চান তা সেট করে নিতে হবে, তারপর save মেথডটি কল করতে হবে। একইভাবে, updated_at সময়চিহ্ন স্বয়ংক্রিয়ভাবে আপডেট হবে, তাই কোনো মেনুয়াল মান অ্যাসাইন করার প্রয়োজন নেই:
```php
$user = app\model\User::find(1);
$user->name = 'jerry';
$user->save();
```

## বালিকা আপডেট
```php
app\model\User::where('uid', '>', 10)
          ->update(['name' => 'tom']);
```

## গুণ পরিবর্তন পরীক্ষা
Eloquent এ isDirty, isClean এবং wasChanged মেথড সরবরাহ করে, মডেলের অবস্থা পরীক্ষা করে এবং এর আবদ্ধতা নির্ধারণ করে কিভাবে প্রোপার্টিগুলি প্রাথমিক লোড হওয়া থেকে পরিবর্তিত হয়েছে। isDirty মেথড প্রোপার্টিগুলি আপডেট হওয়ার পর অবস্থা পরিষ্কার করে। আপনি নির্দিষ্ট প্রোপার্টিকে কাস্ট করে দেখতে পারেন। isClean মেথড isDirty এর বিপরীত, এটি পরিষ্কারতা প্রতীকী প্রোপার্টি পাশকলগুলি গ্রহণ করে:
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
wasChanged মেথড পর্যালোচনা করে যে কোনও অ্যাট্রিবিউটটির সর্বশেষ সংরক্ষণ চক্রটি পরিবর্তিত হয়েছে কিনা। আপনি নির্দিষ্ট প্রোপার্টিকে পরীক্ষা করার জন্য প্রোপার্টি নাম পাস করতে পারেন:
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
## বাল্য অনুদান
আপনি create মেথড ব্যবহার করে নতুন মডেল সংরক্ষণ করতে পারেন। এই পদক্ষেপটি মডেল ইনস্ট্যান্স ফিরিয়ে দেবে। তবে, ব্যবহার করার আগে, আপনার মডেলে নির্দিষ্ট করতে হবে fillable বা guarded মানতার সুত্রে, কারণ সমস্ত Eloquent মডেল ডিফল্টভাবে বাল্য অনুদান করার অনুমতি দেয় না।

যখন ব্যবহারকারীরা অপেক্ষারহিত HTTP প্যারামিটার প্রবেশ করিয়ে এবং ঐ প্যারামিটারটি ডাটাবেসের অংশ পরিবর্তন প্রয়োজন হয় না তখন বাল্য অনুদানের গাপ ঘটে। উদাহরণস্বরূপ: মন্দ ব্যবহারকারীগণ এইচটিটিপি অনুরোধ মাধ্যমে is_admin প্যারামিটার পাস করতে পারে এবং তারপরে অনুদান প্রোসেসিং পারমিটারটি create মেথডে পাঠিয়ে তাদের নিজেকে এডমিন হিসাবে আপগ্রেড করতে পারে।

তাই, শুরু করার আগে, আপনার কোন বৈশিষ্ট্যগুলি বাল্য অনুদান করা সম্পর্কে স্পষ্ট করা উচিত। আপনি মডেলের $fillable বৈশিষ্ট্য ব্যবহার করে সেটা করতে পারেন। উদাহরণস্বরূপ: করুন Flight মডেলের name বৈশিষ্ট্যটি বাল্য অনুদানযোগ্য করুন:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * বাল্য অনুদানযোগ্য বৈশিষ্ট্যগুলি।
     *
     * @var array
     */
    protected $fillable = ['name'];
}
```

যখন আমরা সেট করে দিয়েছি বাল্য অনুদান করা যাওয়ার বৈশিষ্ট্যগুলি, তাহলে আমরা create মেথডের মাধ্যমে নতুন ডেটা ডাটাবেসে ইনসার্ট করতে পারি। create মেথডটি সংরক্ষণকৃত মডেল ইনস্ট্যান্স ফিরিয়ে দেবে:

```php
$flight = app\modle\Flight::create(['name' => 'Flight 10']);
```

যদি আপনার ইতিমধ্যে একটি মডেল ইনস্ট্যান্স থাকে, আপনি fill মেথডে একটি অ্যারে পাঠাতে পারেন যেখানে মান প্রদান করতে:

```php
$flight->fill(['name' => 'Flight 22']);
```

$fillable ব্যবহার করা হয় একটি বাল্য অনুদানের "সাফল্যের তালিকা" হিসেবে, আপনি $guarded বৈশিষ্ট্য ব্যবহার করতে পারেন। $guarded বৈশিষ্ট্যটি বাল্য অনুদান করা যাবেনা এমন অ্যারে ধারণ করে। এটা ব্যাপারপূর্ণ: আপনি শুধুমাত্র $fillable বা  $guarded দুটির মধ্যে একটি ব্যবহার করতে পারবেন না, সাথে ব্যবহার করা যাবেনা। নীচের উদাহরণে, দাম বৈশিষ্ট্যের বাইরে আরও সব বৈশিষ্ট্যগুলি বাল্য অনুদান করা যাবে:

```php
<?php

namespace app\model;

use support\Model;

class Flight extends Model
{
    /**
     * বাল্য অনুদান যোগ্য না করা গুলি।
     *
     * @var array
     */
    protected $guarded = ['price'];
}
```

যদি আপনি সব বৈশিষ্ট্যগুলি বাল্য অনুদান করার ক্ষেত্রে, আপনি $guarded কে একটি ফাঁকা অ্যারে হিসেবে সংরক্ষিত করতে পারেন:

```php
/**
 * বাল্য অনুদান যোগ্য না করা গুলি।
 *
 * @var array
 */
protected $guarded = [];
```
## অন্যান্য তৈরি পদ্ধতি
প্রথমপ্রাপ্ত/প্রথম নতুন
এখানে দুটি মেথড আছে যা আপনি বাল্ক এসাইনমেন্ট করার জন্য ব্যবহার করতে পারেন: firstOrCreate এবং firstOrNew। firstOrCreate মেথডটি ডেটাবেসে ডেটা ম্যাচ করার জন্য দেওয়া কী/মান দিয়ে ব্যবহৃত হবে। ডেটাবেসে মডেল খুঁজে পাওয়া না গেলে, সে মডেলে একটি রেকর্ড ঢোকাবে, যা প্রথম প্যারামিটারের বৈশিষ্ট্য এবং ঐচ্ছিক দ্বিতীয় প্যারামিটারের বৈশিষ্ট্য ধারণ করবে।

firstOrNew মেথড firstOrCreate মেথডের মতই চেষ্টা করবে দেওয়া গুণের মডেল ডাটাবেসে খুঁজে পাওয়া যাবে না। তবে, যদি firstOrNew মেথডটি একটি সংবাদ মডেল অনুলিপি খুঁজে না পায়, তবে একটি নতুন মডেল ইনস্ট্যান্স ফিরিয়ে দেয়। দ্রষ্টব্য: firstOrNew মেথডের ইনস্ট্যান্সটি তথ্যসমূহ ডাটাবেসে সংরক্ষণ করা হয়নি, আপনাকে সংরক্ষণ করার জন্য সেভ মেথড সক্ষম করতে হবেঃ
```php
// নাম দিয়ে ফাইলরটেরা অনুসন্ধান, না পাওয়া গেলে তৈরি করবে...
$flight = app\modle\Flight::firstOrCreate(['name' => 'Flight 10']);

// নাম এবং এলাকার দিকটি পূরণ সহ ফাইলরটেরা অনুসন্ধান, না পাওয়া গেলে তৈরি করবে...
$flight = app\modle\Flight::firstOrCreate(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);

// নাম দিয়ে ফাইলরটেরা অনুসন্ধান, না পাওয়া গেলে একটি ইনস্ট্যান্স তৈরি করবে...
$flight = app\modle\Flight::firstOrNew(['name' => 'Flight 10']);

// নাম এবং ডিলেড সঙ্গে এবং আগমনের সময়ের সঙ্গে ফাইলরটেরা অনুসন্ধান, না পাওয়া গেলে একটি ইনস্ট্যান্স তৈরি করবে...
$flight = app\modle\Flight::firstOrNew(
    ['name' => 'Flight 10'],
    ['delayed' => 1, 'arrival_time' => '11:30']
);
```
আপনি আপত্তি কার্যক্রমে বা অস্তিত্ব না থাকলে বা নতুন মডেল তৈরিতে আপডেট করতে চাইতে পারেন। কোনো একাধিক মডেল করার জন্য updateOrCreate মেথড ব্যবহার করা যাবে। firstOrCreate মেথডের মতো, updateOrCreate মেথডটি মডেলকে স্থায়ী করে, সেভ() কল করা ছাড়া যাওয়া যাবেঃ
```php
// যদি ওকল্যান্ড থেকে সান ডিগো ফ্লাইট থাকে, তাহলে মূল্যটি 99 ডলার হবে।
// যদি মডেল ম্যাচ না পাওয়া যায়, তবে একটি তৈরি করা হবে।
$flight = app\modle\Flight::updateOrCreate(
    ['departure' => 'Oakland', 'destination' => 'San Diego'],
    ['price' => 99, 'discounted' => 1]
);
```

## মডেল মুছে ফেলুন
মডেল ইনস্ট্যান্সের উপর ডিলিট মেথড কল করে ইনস্ট্যান্স মুছে ফেলা যাবে।
```php
$flight = app\modle\Flight::find(1);
$flight->delete();
```

## প্রাইমারী কি ব্যবহার করে মডেল মুছে ফেলুন
```php
app\modle\Flight::destroy(1);

app\modle\Flight::destroy(1, 2, 3);

app\modle\Flight::destroy([1, 2, 3]);

app\modle\Flight::destroy(collect([1, 2, 3]));
```

## কুয়েরি ব্যবহার করে মডেল মুছে ফেলেন
```php
$deletedRows = app\modle\Flight::where('active', 0)->delete();
```

## মডেল অনুলিপি করুন
আপনি অনুলিপি মেথড ব্যবহার করে ডাটাবেসে সংরক্ষিত নতুন ইনস্ট্যান্স কপি তৈরি করতে পারেন, যখন মডেল ইনস্ট্যান্সগুলি অনেক সমান বৈশিষ্ট্য ভাগ করে।
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
## মডেল তুলনা
সময়ের চেয়ে যদি আপনারা দুটি মডেল যথাক্রমে "একই" কিনা তা নির্ধারণ করতে হতে পারে। এটি যাচাই করতে is মেথডটি ব্যবহার করা যেতে পারে যে দুটি মডেল কি একই মূল চাবি, টেবিল এবং ডাটাবেস সংযোগ রাখে কিনা।
```php
if ($post->is($anotherPost)) {
    //
}
```

## মডেল চেকার
[Laravel মডেল ইভেন্ট এবং অবজারভার উপর ভিত্তি করে](https://learnku.com/articles/6657/model-events-and-observer-in-laravel) ব্যবহার করা।

লক্ষ্যকরুন: Eloquent ORM মডেল চেকার সমর্থন করতে অতিরিক্তভাবে আমন্ত্রিত পাঠানোর composer require "illuminate/events" প্রয়োজন।
```php
<?php
namespace app\model;

use support\Model;
use app\observer\UserObserver;

class User extends Model
{
    public static function boot()
    {
        parent::boot();
        static::observe(UserObserver::class);
    }
}
```
