# কুয়েরি সংযোগকারী
## সমস্ত সারি পেতে
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
আইডি ক্ষেত্র সনাক্ত করা
```php
$roles = Db::table('roles')->pluck('title', 'id');

foreach ($roles as $id => $title) {
    echo $title;
}
```

## একটি মান (কলাম) অর্থাত্ত ভ্যালু পেতে
```php
$email = Db::table('users')->where('name', 'John')->value('email');
```

## ডুপ্লিকেট বাদ দিতে
```php
$email = Db::table('user')->select('nickname')->distinct()->get();
```

## চাংপে ফলাফল
আপনি যদি হাজার হাজার ডাটাবেস রেকর্ড প্রসেস করতে চান, তাহলে পূর্ব্বনির্ধারিতভাবে তারা পেতে সময় এবং ভেঙে যাওয়ার সম্ভাবনার কারণে আপনি চাংপিং ফলাফল পেতে চিন্তা করতে পারেন। এই মেথডটি মূলত একটি ছোট টুকরা রেজাল্ট সেট পেতে উপভোগ করে এবং এটি ক্লোজার ফাংশনে প্রেরণ করার মাধ্যমে এগোচ্ছ। উদাহরণস্বরূপ, আমরা সব ইউজার টেবিলের ডাটা কে 100 টি রেকর্ডে একসাথে রিড করতে পারি:
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    foreach ($users as $user) {
        //
    }
});
```
আপনি পেতে পারবেন চাংপিং ফলাফলের মধ্যে false রিটার্ণ করে এবং আপনি যতেবার চান তাতে সমাপ্তি করতে পারেন।
```php
Db::table('users')->orderBy('id')->chunkById(100, function ($users) {
    // রেকর্ডগুলির প্রসেস...

    return false;
});
```

> দ্রষ্টব্য: মোকেল থেকে ডাটা মোছা না, তা রেজাল্টে থাকা অনেক রেকর্ড মিসিং করে দিতে পারে।

## আগ্রেগেশন
কুয়েরি সংযোগযন্ত্রটি উপলব্ধ করে বিভিন্ন আগ্রেগেশন পদ্ধতি, যেমন count, max, min, avg, sum ইত্যাদি।
```php
$users = Db::table('users')->count();
$price = Db::table('orders')->max('price');
$price = Db::table('orders')->where('finalized', 1)->avg('price');
```

## রেকর্ডের উপস্থিতি পরীক্ষা করুন
```php
return Db::table('orders')->where('finalized', 1)->exists();
return Db::table('orders')->where('finalized', 1)->doesntExist();
```
## প্রাকৃতিক অভিব্যক্তি
প্রটোটাইপ
```php
selectRaw($expression, $bindings = [])
```
কখনও কখনও আপনার কোয়েরিতে প্রাকৃতিক অভিব্যক্তি ব্যবহার করতে হতে পারে। আপনি `selectRaw()` ব্যবহার করে একটি প্রাকৃতিক অভিব্যক্তি তৈরি করতে পারেন:

```php
$orders = Db::table('orders')
                ->selectRaw('price * ? as price_with_tax', [1.0825])
                ->get();
```

একেই ভাবে, `whereRaw()` `orWhereRaw()` `havingRaw()` `orHavingRaw()` `orderByRaw()` `groupByRaw()` প্রাকৃতিক অভিব্যক্তি নিয়ে মেথডগুলি প্রদান করা হয়েছে।

`Db::raw($value)` এটি একটি প্রাকৃতিক অভিব্যক্তি তৈরি করতে ব্যবহৃত হয়, তবে এটি প্যারামিটার বাইন্ডিং সুবিধা নেই, SQL ইনজেকশন সমস্যা বিষয়ে এটি সতর্কতার সাথে ব্যবহার করা দরকার।
```php
$orders = Db::table('orders')
                ->select('department', Db::raw('SUM(price) as total_sales'))
                ->groupBy('department')
                ->havingRaw('SUM(price) > ?', [2500])
                ->get();
```

## যোগ বা যৌক্তিক বাক্য
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

## ইউনিয়ন বাক্য
```php
$first = Db::table('users')
            ->whereNull('first_name');

$users = Db::table('users')
            ->whereNull('last_name')
            ->union($first)
            ->get();
```
## কোথাও স্টেটমেন্ট
স্টেটমেন্ট ফর্ম্যাট
```php
where($column, $operator = null, $value = null)
```
প্রথম প্যারামিটার হল কলামের নাম, দ্বিতীয় প্যারামিটারটি হল যে কোন ডাটাবেস অপারেটর এবং তৃতীয় টি হল সে কলামের মান
```php
$users = Db::table('users')->where('votes', '=', 100)->get();

// যখন অপারেটর টি ব্যবহৃত হয় তখন এটি অপশনাল, অতএব এই মন্তব্যের সায়ঃ ঠিক একই ফলাফল পায়
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

তুমি where ফাংশনে শর্ত অ্যারে পাস করতে পারো:
```php
$users = Db::table('users')->where([
    ['status', '=', '1'],
    ['subscribed', '<>', '1'],
])->get();

```

orWhere মেথড এবং where মেথডে প্রদত্ত প্যারামিটারগুলি সহযোগী:
```php
$users = Db::table('users')
                    ->where('votes', '>', 100)
                    ->orWhere('name', 'John')
                    ->get();
```

তুমি orWhere মেথডে প্রথম প্যারামিটার হিসেবে একটি ক্লোজার পাস করতে পারো:
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

whereBetween / orWhereBetween মেথড সারা দুটি মানের মধ্যে ক্ষেত্রের মানগুলি যাচাই করে:
```php
$users = Db::table('users')
           ->whereBetween('votes', [1, 100])
           ->get();
```

whereNotBetween / orWhereNotBetween মেথড সারা দুটি মানের বাইরে ক্ষেত্রের মানগুলি যাচাই করে:
```php
$users = Db::table('users')
                    ->whereNotBetween('votes', [1, 100])
                    ->get();
```

whereIn / whereNotIn / orWhereIn / orWhereNotIn মেথড সারা ধারণকে যাচাই করে যে ক্ষেত্রের মানটি নির্দিষ্ট অ্যারেতে আছে:
```php
$users = Db::table('users')
                    ->whereIn('id', [1, 2, 3])
                    ->get();
```

whereNull / whereNotNull / orWhereNull / orWhereNotNull মেথড সারা নির্দিষ্ট ক্ষেত্রটি এনাল হতে হবে:
```php
$users = Db::table('users')
                    ->whereNull('updated_at')
                    ->get();
```

whereNotNull মেথড যাচাই করে যে নির্দিষ্ট ক্ষেত্রটি এনাল নয়:
```php
$users = Db::table('users')
                    ->whereNotNull('updated_at')
                    ->get();
```

whereDate / whereMonth / whereDay / whereYear / whereTime মেথড ক্ষেত্রের মানগুলি সম্পর্কে তারিখের সাথে তুলনা করতে ব্যবহৃত হয়:
```php
$users = Db::table('users')
                ->whereDate('created_at', '2016-12-31')
                ->get();
```

whereColumn / orWhereColumn মেথড ক্ষেত্রের মানের দুটি কলাম সমান কিনা তা চেক করে:
```php
$users = Db::table('users')
                ->whereColumn('first_name', 'last_name')
                ->get();
                
// তুমি একটি তুলনা অপারেটর পাস করতে পারো
$users = Db::table('users')
                ->whereColumn('updated_at', '>', 'created_at')
                ->get();
                
// whereColumn মেথড এই ভাবে অ্যারেগুলিও পাঠাতে পারে
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
## অর্ডারবাই
```php
$randomUser = Db::table('users')
                ->inRandomOrder()
                ->first();
```
> এই প্রক্রিয়াটি সার্ভারের কাজের সুবিধা প্রভাবিত করতে পারে, সামজানো হয়না।

## groupBy / having
```php
$users = Db::table('users')
                ->groupBy('account_id')
                ->having('account_id', '>', 100)
                ->get();
// আপনি groupBy মেথডে একাধিক প্যারামিটার পাঠাতে পারেন
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
## প্রবেশ
একক ধরনের প্রবেশ
```php
Db::table('users')->insert(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
একাধিক ধরনের প্রবেশ
```php
Db::table('users')->insert([
    ['email' => 'taylor@example.com', 'votes' => 0],
    ['email' => 'dayle@example.com', 'votes' => 0]
]);
```
## স্বয়ংক্রিয় আইডি
```php
$id = Db::table('users')->insertGetId(
    ['email' => 'john@example.com', 'votes' => 0]
);
```
> লক্ষ্য করুন: PostgreSQL ব্যবহার করা হলে, insertGetId মেথডটি ডিফল্টভাবে স্বয়ংক্রিয়ভাবে ID ক্ষেত্রের নাম হিসেবে নেয়। আপনি যদি অন্য "অনুক্রম" থেকে আইডি পেতে চান তবে আপনি insertGetId মেথডে দ্বিতীয় প্যারামিটার হিসেবে ক্ষেত্রের নাম পাঠাতে পারেন।

## আপডেট
```php
$affected = Db::table('users')
              ->where('id', 1)
              ->update(['votes' => 1]);
```
## আপডেট বা যোগ
প্রায়ই আপনি ডাটাবেসের বিদ্যমান রেকর্ড আপডেট করতে চান, বা যদি মিল না হয় তাহলে তা তৈরি করবেন:
```php
Db::table('users')
    ->updateOrInsert(
        ['email' => 'john@example.com', 'name' => 'John'],
        ['votes' => '2']
    );
```
updateOrInsert মেথডটি প্রথম প্যারামিটারের কী এবং মান ব্যবহার করে প্রথমে মিল রেকর্ড খুঁজে বের করার চেষ্টা করবে। যদি রেকর্ড পাওয়া যায়, তাহলে তা আপডেট করবে। যদি পাওয়া না যায়, তাহলে নতুন রেকর্ড যুক্ত করবে, নতুন রেকর্ডের ডেটা হবে দুটি অ্যারে।

## স্বয়ংক্রিয় এবং স্বতঃমিটি
এই দুটি মেথড সম্পূর্ণ উপরের একটি প্যারামিটার গ্রহণ করে: পরিবর্তনযোগ্য কলাম। দ্বিতীয় প্যারামিটার ঐচ্ছিক, কলাম বার্তা নিয়ন্ত্রণের জন্য ব্যবহার করা হয়:

```php
Db::table('users')->increment('votes');

Db::table('users')->increment('votes', 5);

Db::table('users')->decrement('votes');

Db::table('users')->decrement('votes', 5);
```
আপনি আপডেট প্রক্রিয়ার সময় নির্দিষ্ট ফিল্ডগুলি সিদ্ধান্ত করতে পারেন:
```php
Db::table('users')->increment('votes', 1, ['name' => 'John']);
```

## মুছে ফেলুন
```php
Db::table('users')->delete();

Db::table('users')->where('votes', '>', 100)->delete();
```
আপনি যদি টেবিল শুধুমাত্র খালি করতে চান, তাহলে আপনি truncate মেথডটি ব্যবহার করতে পারেন, এটি সমস্ত সারি মুছে ফেলবে এবং স্বয়ংক্রিয় আইডি শূন্যে পরিবর্তন করবে:
```php
Db::table('users')->truncate();
```
## দুঃখপ্রবণ লক

অনুসন্ধান নির্মাণকারী এমন কিছু ফাংশন অনুসরণ করে, যা আপনাকে লেখা "দুঃখপ্রবণ লক" কার্যত প্রায় সিলেক্ট সিনট্যাক্স পেশ করতে সাহায্য করে। যদি আপনি একটি "ভাগাভাগি লক" অনুসন্ধানে লাগানোর মত করে, আপনি `sharedLock` মেথডটি ব্যবহার করতে পারেন। ভাগাভাগি লক দাবি করে যে ডেটা কলামগুলি পরিবর্তন করা হবে না, যতক্ষণ না ট্রান্সাকশনটি সাবমিট না করা হয়ে যায়।
```php
Db::table('users')->where('votes', '>', 100)->sharedLock()->get();
```
বা, আপনি `lockForUpdate` মেথডটি ব্যবহার করতে পারেন। "আপডেট" লক ব্যবহার করে অন্যান্য ভাগাভাগি লক দ্বারা পরিবর্তন বা নির্বাচন করা হওয়া থেকে বাঁচিয়ে থাকা যায়।
```php
Db::table('users')->where('votes', '>', 100)->lockForUpdate()->get();
```

## ডিবাগ
আপনি প্রশ্ন সাবেক করার জন্য `dd` বা `dump` মেথড ব্যবহার করতে পারেন। `dd` মেথডটি ডিবাগ তথ্য প্রদর্শন করে এবং তারপর অনুরোধ বন্ধ করে। `dump` মেথডটি অনুরোধ বন্ধ না হলেও ডিবাগ তথ্য প্রদর্শন করে:
```php
Db::table('users')->where('votes', '>', 100)->dd();
Db::table('users')->where('votes', '>', 100)->dump();
```

> **লক্ষ্য করুন**
> ডিবাগ করার জন্য `symfony/var-dumper` ইনস্টল করা আবশ্যক, যা কমান্ড হল `composer require symfony/var-dumper`
