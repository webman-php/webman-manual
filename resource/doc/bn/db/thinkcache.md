## থিংকক্যাশ

### থিংকক্যাশ ইনস্টল করুন

`composer require -W webman/think-cache`

ইনস্টল হওয়ার পরে পুনঃরায় চালু করতে হবে (রিলোড কার্যকর নয়)


>[webman/think-cache](https://www.workerman.net/plugin/15) মূলত `toptink/think-cache` কে স্বয়ংক্রিয়ভাবে ইনস্টল করার একটি প্লাগইন।

> **লক্ষ্য করুন**
> toptink/think-cache পিএইচপি 8.1 সমর্থন করে না।

### কনফিগারেশন ফাইল

কনফিগারেশন ফাইলটি `config/thinkcache.php` অবস্থিত।

### ব্যবহার

  ```php
  <?php
  namespace app\controller;
    
  use support\Request;
  use think\facade\Cache;
  
  class UserController
  {
      public function db(Request $request)
      {
          $key = 'test_key';
          Cache::set($key, rand());
          return response(Cache::get($key));
      }
  }
  ```

### থিংক-ক্যাশ ব্যবহারের নথি

[ThinkCache ডকুমেন্টেশন](https://github.com/top-think/think-cache)
