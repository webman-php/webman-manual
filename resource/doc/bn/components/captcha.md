# ভেরিফিকেশন কোড সংশ্লিষ্ট কম্পোনেন্টস

## webman/captcha
প্রোজেক্ট লিঙ্ক https://github.com/webman-php/captcha

### ইনস্টলেশন
``` 
composer require webman/captcha 
```

### ব্যবহার

**`app/controller/LoginController.php` ফাইল তৈরি করুন**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * টেস্ট পেজ
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * ভেরিফিকেশন চিত্র প্রদর্শন
     */
    public function captcha(Request $request)
    {
        // ভেরিফিকেশন বিল্ডারকে আপ করুন
        $builder = new CaptchaBuilder;
        // ভেরিফিকেশন তৈরি করুন
        $builder->build();
        // ভেরিফিকেশন মানটি সেশনে সংরক্ষণ করুন
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // ভেরিফিকেশন চিত্রের বাইনারি ডেটা পেতে
        $img_content = $builder->get();
        // ভেরিফিকেশন চিত্রের বাইনারি ডেটা প্রদান করুন
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * ভেরিফিকেশন যাচাইকরণ
     */
    public function check(Request $request)
    {
        // পোস্ট অনুরোধের 'ভেরিফিকেশন' ফিল্ড পেতে
        $captcha = $request->post('captcha');
        // সেশনের ভেরিফিকেশন মান সাথে তুলনা করুন
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'প্রবেশ করা ভেরিফিকেশন সঠিক নয়']);
        }
        return json(['code' => 0, 'msg' => 'ঠিক আছে']);
    }

}
```

**`app/view/login/index.html` ফাইল তৈরি করুন**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>ভেরিফিকেশন টেস্ট</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="জমা দিন" />
    </form>
</body>
</html>
```

`http://127.0.0.1:8787/login` পেজে যান, সেখানে প্রদর্শিত হবে এরকম একটি ইমেজ:

![](../../assets/img/captcha.png)

### সাধারণ প্যারামিটার সেটিং
```php
    /**
     * ভেরিফিকেশন চিত্র প্রদর্শন
     */
    public function captcha(Request $request)
    {
        // ভেরিফিকেশন বিল্ডারকে আপ করুন
        $builder = new CaptchaBuilder;
        // ভেরিফিকেশন দৈর্ঘ্য
        $length = 4;
        // কোন কোন অক্ষর থাকা উচিত
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // ভেরিফিকেশন তৈরি করুন
        $builder->build();
        // ভেরিফিকেশন মানটি সেশনে সংরক্ষণ করুন
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // ভেরিফিকেশন চিত্রের বাইনারি ডেটা পেতে
        $img_content = $builder->get();
        // ভেরিফিকেশন চিত্রের বাইনারি ডেটা প্রদান করুন
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

আরও প্রাক্কলিত ইন্টারফেস এবং প্যারামিটার দেখতে https://github.com/webman-php/captcha দেখুন।
