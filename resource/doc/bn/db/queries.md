# কুয়েরি বিল্ডার
## সব সারিগুলি পেতে
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

## নির্দিষ্ট কলাম পেতে
```php
$users = Db::table('user')->select('name', 'email as user_email')->get();
```

## একটি সারি পেতে
```php
$user = Db::table('users')->where('name', 'John')->first();
```

## একটি কলাম পেতে
```php
$titles = Db::table('roles')->pluck('title');
```
স্পেসিফিক আইডি ফিল্ড যখন নির্দিষ্ট করা হয়
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## একক মান পেতে (ফিল্ড)
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## ডুপ্লিকেট মুছে ফেলা
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## চাংড়া পদক্ষেপ
যদি আপনি হাজার হাজার রেকর্ড দরকার হয়, তাহলে সা জ লোড করা খুব সময় সাপেড় এবং মেমরি ভরাডু অতীত এবং এটি লাগতেই মেমরি বাফায় আঁচ পড়ে, তাহলে আপনি `chunkById` মেথড ব্যবহার করতে পারেন। এই মেথডটি একবারে একটা পর্যায়ের একটি ছোট অংশে ফলাফলগুলি পেতে। উদাহরণস্বরূপ, আমরা সমস্তগুলি ব্যবহারকারী টেবিল তথ্যগুলি কে একই সময়ে 100 রেকর্ডের সাথে একবারে প্রস্তুত করতে এতে পারি
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
আপনি `ফরিক্ষণ` মাধ্যমে ফলাফলগুলি অব্যাহত করতে চাইলে বন্ধক `ফরিক্ষণ` মধ্যে ফলাফলের চয়ন
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // রেকর্ড গুছাতে...

    return false;
});
```

> সাবধানতা: রেকর্ড মোছে দিতে না চেয়ে ফন তা ত্ত্ব হতে পারে যেটা সেটি  ાযোজিত নডের ফলাফলে যুগ র্ক

## এগ্রাগেট
ক্যুয়েরি বিল্ডার আরও বিভিন্ন এগ্রিগেট মেথডগুলি প্রদান করে, যথা `গণন`, `সর্বনিম্ন`, `সর্বাধিক`, `গড়`, `মোট` ইত্যাদি।
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## রেকর্ড বিদ্যমান কিনা পরীক্ষা করা
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```

## অসম্প্রজ্ঞান প্রকারি সাজানো
অসম্প্রজ্ঞান প্রকারি হলো কোনও কিছুতে কিছু অর্ডার থাকা।
```php
selectRaw($expression, $bindings = [])
```
কখনওই আপনি কুয়েরি এর মধ্যে অরিজিনাল অব্যাহত প্রকারি ব্যবহার করতে হত. আপনি `selectRaw()` ব্যবহার করে অস্মৃতি প্রকারি তৈরি করতে পারেন:

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();

```

একইভাবে, `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` অরিজিনাল প্রকারি মেথডগুলি প্রদান করা হয়। 

`Db::raw($value)`এও একটি অসম্প্রজ্ঞান প্রকারি তৈরি করা হয়, কিন্তু এটির বাইন্ডিং প্যারামিটার সুরক্ষা ফিচার নেই, এর ব্যবহারের সময় একটু সাবধানতা অবাঞ্জাত।
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();

```
## যোগদান স্টেটম্যান্ট
```php
// যোগদান
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

## ইউনিয়ন স্টেটম্যান্ট
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```

## যেখান স্টেটম্যান্ট
মৌলিক ফর্ম
```php
where($column, $operator = null, $value = null)
```
প্রথম প্যারামিটারটি কলাম নাম, দ্বিতীয়টি যেকোনো ডেটাবেস সিস্টেম সমর্থি অপারেটর, তৃতীয়টি ঐ কলামের মান তারা তুলনা
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// যখন অপারেটর বারাবার চিহ্ন আছে তবে আপনি powers পৃর্থতা বর্ণা
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

আপনি where ফাংশনে শর্ত অ্যারে পাস করতে পারেন
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

orWhere মেথড এবং where মেথড পেয়ারামিটারার প্রয়াোয় যেইরীয় যায়
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

আপনি orWhere মেথডে একটি মেয়াদী বন্দোনী পাস করতে পারেন
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

whereBetween / orWhereBetween মেথডগুলি মান্দটি মানেই অবধিরতার মানটি প্রমাণ করে
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween মেথডগুলিমান্দটি মানেই অবধিরতার মানটি প্রমাণ করে যেটির মধ্যে
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn মেথডগুলির মাধ্যমে মান তাদের অংশ হওয়া প্রয়োজন
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull মেথডগুলির মাধ্যমে স্পষ্ট করা হয়েছে যে স্পষ্টটি NULL হওয়া প্রয়োজন
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull মেথডগুলির মাধ্যমে স্পষ্ট করা হয়েছে যে স্পষ্ট নয়
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime মেথডগুলি মানের মানের সাথে ভালো করে সাম্মেলিত হয়
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn মেথডগুলি মান দুই ফিল্ড এর মান তুলনা
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// তুমি প্লাস় একটা তুলনা অপারেটর পাস করতে পারো
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn মেথডটিরও মাধ্যমে আপনি অ্যারে পাস করতে পারেন
$users = Db::table('users')
                ->whereColumn([
                    ['first_name', '=', 'last_name'],
                    ['updated_at', '>', 'created_at'],
                ])->get();

```

প্যারামিটার গ্রুপ
```php
// select * from users where name = 'John' and (votes > 100 or title = 'Admin')
$users = Db::table('users')
           ->where('name', '=', 'John')
           ->where(function ($query) {
              
## প্রবেশ
একক রেকর্ড প্রবেশ
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
একাধিক রেকর্ড প্রবেশ
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```

## স্বয়ংক্রিয় আইডি
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```

> লক্ষ্য করুন: PostgreSQL ব্যবহার করার সময়, insertGetId পদক্ষেপটি ডিফল্টভাবে idকে স্বয়ংক্রিয় ঘটনীয় ক্ষেত্রের নাম হিসাবে ধরে নেয়। যদি আপনি অন্য "শ্রৃঙ্খলা" থেকে ID প্রাপ্ত করতে চান, তবে আপনি insertGetId পদক্ষেপটিতে দ্বিতীয় প্যারামিটার হিসাবে ক্ষেত্রের নাম পাস করতে পারেন।

## আপডেট
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```

## আপডেট অথবা ইনসার্ট
কিছু সময় আপনি গতিশীল রেকর্ড আপডেট করতে চান বা যদি কোনও মেলা রেকর্ড না থাকে তাহলে তা তৈরি করার ক্ষেত্রে:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert পদক্ষেপটি প্রথম প্যারামিটারের কী এবং মান দিয়ে ডেটাবেস রেকর্ড সন্ধান করার চেষ্টা করবে। যদি রেকর্ড পাওয়া যায় তবে রেকর্ডটি আপডেট করতে দ্বিতীয় প্যারামিটারের মান ব্যবহার করা হবে। যদি রেকর্ড পাওয়া না যায়, তবে নতুন রেকর্ডটি তৈরি করা হবে, যা দুটি অ্যারের সংযোজনের মাধ্যমে।

## স্বয়ংক্রিয় ও স্বয়ংক্রিয় ঘটনীয়
এই দুটি পদক্ষেপ কমপক্ষে একটি প্যারামিটার গ্রহণ করে: পরিবর্তন করতে হবে কলাম। দ্বিতীয় প্যারামিটার ঐচ্ছিক, এবং কলামটি বৃদ্ধি অথবা ক্ষয়নের পরিমাণ নিয়ন্ত্রণ করতে ব্যবহৃত হয়:
```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
আপনি প্রয়োগ পদক্ষেপের সাথে আপডেট করতে হতে পারে:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## মুছে ফেলুন
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
আপনি যদি টেবিল খালি করতে চান, তবে আপনি truncate পদক্ষেপ ব্যবহার করতে পারেন, এটি সব সারি মুছে ফেলবে এবং স্বয়ংক্রিয় আইডি শূন্য করবে:
```php
Db::table('users')->truncate();
```

## দু:খদায়ক লক
প্রশ্ন নির্মাণকারী অনেকগুলি "দু:খদায়ক লক"কে বামন লক৷ আপনি "শেয়ারড লক্‍"কে প্রয়োজনে গুণক করতে পারেন। "শেয়ারড লক"টি অন্যান্য মান সম্পাদনা বা নির্বাচন হবার পর্যন্ত ডাটা কলাম পক্ষে রোধ করবে:
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
অথবা, আপনি lockForUpdate পদক্ষেপ ব্যবহার করতে পারেন। "আপডেট"লক (লকােপাই) ব্যবহার করা লকনীয়া লাইনগুলি অন্যান্য "শেয়ারড লক"সম্পাদনা বা নির্বাচন করতে বাতিল করবে:
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## ডিবাগ
আপনি প্রশ্ন ক্রিয়াকলাপ বা SQL প্রশ্নাবলি আউটপুট করতে ডিবাগ বা ডাম্প পদক্ষেপ ব্যবহার করতে পারেন। ডি ডি পদক্ষেপটি ডিবাগ তথ্য প্রদর্শন করবে এবং তারপর অনুরোধ বন্ধ করবে। ডাম্প পদক্ষেপটি যদি ডিবাগ তথ্য প্রদর্শন করে, তবুও রিকুস্ট বন্ধ হবে না:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **দ্রষ্টব্য**
> ডিবাগের জন্য `symfony/var-dumper` নিয়ে ইনস্টলকরণ আপনার আবশ্যক, কমান্ড  `composer require symfony/var-dumper` 
