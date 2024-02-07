## থিংকORM

### থিংকORM ইনস্টল

`composer require -W webman/think-orm`

ইনস্টল হওয়ার পরে পুনরায় শুরু করতে হবে (রিলোড প্রভাবিত করবে না)

> **পরামর্শ**
> যদি ইনস্টলেশন ব্যর্থ হয়, তাহলে আপনার যদি কোম্পোজার প্রক্সি ব্যবহার করেন, তবে আপনি চেষ্টা করুন `composer config -g --unset repos.packagist` 

> [webman/think-orm](https://www.workerman.net/plugin/14) এটা যাত্রীক একটি`toptink/think-orm` এর স্বয়ংক্রিয় ইনস্টলেশন প্লাগইন, আপনার webman সংস্করণ যদি `1.2` এর নিচে থাকে তাহলে এই প্লাগইন ব্যবহার করা যাবেনা। আপনি যদি এই প্লাগইন ব্যবহার করতে না পারেন তাহলে আর্টিকেল [থিংক-ORM নিজস্বভাবে ইনস্টল ও কনফিগার করা](https://www.workerman.net/a/1289) দেখুন।

### কনফিগারেশন ফাইল
আপনার প্রায়নিক অবস্থার ভিত্তিতে `config/thinkorm.php` কনফিগারেশন ফাইল সম্পাদনা করুন।

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

### মডেল তৈরি

ThinkOrm মডেল `think\Model` থেকে উপঘাত পেতে, যেমন -

```php
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * মডেল সংশ্লিষ্ট টেবিল।
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * টেবিল সংশ্লিষ্ট প্রাথমিক কী।
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

আপনি এই কমান্ড ব্যবহার করে থিংকঅর্মের ভিত্তিতে মডেল তৈরি করতে পারেন

```
php webman make:model টেবিল নাম
```

> **পরামর্শ**
> এই কমান্ডটি সঠিকভাবে কাজ করতে হলে `webman/console` ইনস্টল করা আছে কি না তারা যাচাই করুন `composer require webman/console ^1.2.13`

> **লক্ষ্যঃ**
> make:model কমান্ডটি যদি প্রধান প্রকল্পে `illuminate/database` ব্যবহার করা হয় তাহলে, `illuminate/database` এর ভিত্তিতে মডেল ফাইল তৈরি হবে, থিংক-অর্মের মডেল নয়। এই সময় আপনি আরও একটি প্যারামিটার tp যুক্ত করে দেওয়ার মাধ্যমে মডেল তৈরি করতে পারেন, কমান্ডটি হবে `php webman make:model টেবিল নাম tp` (যদি কাজ না করে তাহলে `webman/console` আপডেট করুন)
