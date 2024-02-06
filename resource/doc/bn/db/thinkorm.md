## থিংকORM

### থিংকORM ইনস্টল করুন

`composer require -W webman/think-orm`

ইনস্টলেশন শেষ হওয়ার পরে বার্তা প্রদর্শন করতে হবে (রিলোড কাজ করবে না)

> **পরীক্ষার দায়ে**
> ইনস্টলেশন ব্যর্থ হতে পারে, এটা যেহেতু আপনি কোম্পোজার প্রক্সি ব্যবহার করছেন, তাই চেষ্টা করুন `composer config -g --unset repos.packagist` কমান্ডটি ছাড়ানোর জন্য।

> [webman/think-orm](https://www.workerman.net/plugin/14) সত্ত্বেও একটি `toptink/think-orm` এর স্বয়ংক্রিয় ইনস্টলেশন প্লাগইন, আপনার যদি ওয়েবম্যান ভার্সন  `1.2` এর নিচে হয় তবে প্লাগইন ব্যবহার করা যাবে না, তাহলে অনুগ্রহ করে নিচের টিউটোরিয়ালটি চেক করুন [থিং-ORM ম্যানুয়াল ইনস্টলেশন এবং কনফিগারেশন] (https://www.workerman.net/a/1289).

### কনফিগারেশন ফাইল
কনফিগারেশন ফাইল কে আপনার প্রয়োজনীয়ভাবে সম্পাদনা করুন `config/thinkorm.php`

### ব্যবহার
```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### মডেল তৈরি করুন

থিংকORM মডেল `think\Model` কে এক্সটেন্ড করে, এমনভাবে
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * মডেলের সাথে এসোসিয়েটেড টেবিল।
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * টেবিলে এসোসিয়েটেড প্রাইমারি কী।
     *
     * @var string
     */
    protected $pk = 'id';
}
```

আপনি এই কমান্ড ব্যবহার করেও থিংকORM ভিত্তিক মডেল তৈরি করতে পারেন
```
php webman make:model টেবিল-নাম
```

> **পরীক্ষার দায়ে**
> এই কমান্ডটি ব্যবহার করতে `webman/console` ইনস্টল করা আছে, ইনস্টলেশন কমান্ড হলো `composer require webman/console ^1.2.13`

> **মনে রাখবেন**
> make:model কমান্ডটি যদি প্রধান প্রকল্পে `illuminate/database` ব্যবহার হয়, তবে থিং-এওআর-এর মডেল ফাইল নিতে হলে `illuminate/database` ভিত্তিক মডেল ফাইল তৈরি করবে, এই সময় আপনি tp প্যারামিটারটি অ্যাটাচ করে থিং-ORM ভিত্তিক মডেল তৈরি করতে পারেন, কমান্ডটি হলো `php webman make:model টেবিল-নাম tp` (যদি এটি কাজ না করে তাহলে `webman/console` আপগ্রেড করুন)
