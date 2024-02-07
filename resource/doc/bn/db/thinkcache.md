## থিংকক্যাশ

### থিংকক্যাশ ইনস্টল করুন  
`composer require -W webman/think-cache`

ইনস্টল করার পরে পুনরারম্ভ(restart) করা প্রয়োজন (মুলত বন্ধ করার পর পুনরারম্ভ করা সক্ষম হবে)

> [webman/think-cache](https://www.workerman.net/plugin/15) সক্রিয়তা বাহিত একটি `toptink/think-cache` এর অটোমেটেড ইনস্টলেশন প্লাগইন।

> **লক্ষ্য করুন**
> toptink/think-cache পিএইচপি ৮.১ সমর্থন করে না।
  
### কনফিগারেশন ফাইল

কনফিগারেশন ফাইলটি `config/thinkcache.php` নামে থাকবে।

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

### থিং-ক্যাশ ব্যবহার ডকুমেন্ট

[ThinkCache ডকুমেন্টেশন লিংক](https://github.com/top-think/think-cache)
