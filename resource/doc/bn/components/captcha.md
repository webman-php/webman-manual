# ভেরিফিকেশন কোড সংক্রান্ত কম্পোনেন্ট

## webman/captcha
প্রোজেক্ট ঠিকানা https://github.com/webman-php/captcha

### ইনস্টলেশন
```
composer require webman/captcha
```

### ব্যবহার

**ফাইল `app/controller/LoginController.php` তৈরি করুন**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * পরীক্ষা পৃষ্ঠা
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * ভেরিফিকেশন ছবি আউটপুট
     */
    public function captcha(Request $request)
    {
        // ভেরিফিকেশন বিল্ডার তৈরি করুন
        $builder = new CaptchaBuilder;
        // ভেরিফিকেশন তৈরি করুন
        $builder->build();
        // ভেরিফিকেশনের মানটি সেশনে সংরক্ষণ করুন
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // ভেরিফিকেশনের চিত্র দ্বিতীয়াংশ উদ্যোগ করুন
        $img_content = $builder->get();
        // ভেরিফিকেশনের দ্বিতীয়াংশ ডেটা আউটপুট করুন
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * ভেরিফিকেশন পরীক্ষা করুন
     */
    public function check(Request $request)
    {
        // পোস্ট অনুরোধে ভেরিফিকেশন ক্ষেত্র পান
        $captcha = $request->post('captcha');
        // সেশনের ভেরিফিকেশন মান অনুলিপি
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'প্রবেশ করা ভেরিফিকেশন সঠিক নয়']);
        }
        return json(['code' => 0, 'msg' => 'ঠিক আছে']);
    }

}
```

**টেমপ্লেট ফাইল`app/view/login/index.html` তৈরি করুন**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>ভেরিফিকেশন পরীক্ষা</title>  
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

পেইজে যান `http://127.0.0.1:8787/login` অবস্থা এমনঃ
  ![](../../assets/img/captcha.png)

### সাধারণ সেটিং প্যারামিটার
```php
    /**
     * ভেরিফিকেশন ছবি আউটপুট
     */
    public function captcha(Request $request)
    {
        // ভেরিফিকেশন ক্লাস আৰম্ভ কৰো
        $builder = new CaptchaBuilder;
        // ভেরিফিকেশন দৈর্ঘ্য
        $length = 4;
        // কোন কোন অক্ষর থাকা উচিত
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // ভেরিফিকেশন তৈরি করুন
        $builder->build();
        // ভেরিফিকেশনের মানটি সেশনে সংরক্ষণ করুন
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // ভেরিফিকেশনের চিত্র দ্বিতীয়াংশ উদ্যোগ করুন
        $img_content = $builder->get();
        // ভেরিফিকেশনের দ্বিতীয়াংশ ডেটা আউটপুট করুন
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

আরও ইন্টারফেস এবং প্যারামিটার জানতে দেখুন https://github.com/webman-php/captcha
