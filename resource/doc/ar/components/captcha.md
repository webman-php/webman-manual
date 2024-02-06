# مكونات الرمز التحقق

## webman/captcha
رابط المشروع: https://github.com/webman-php/captcha

### التثبيت
```
composer require webman/captcha
```

### الاستخدام

**إنشاء ملف `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * صفحة الاختبار
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * إخراج صورة الرمز التحقق
     */
    public function captcha(Request $request)
    {
        // تهيئة فئة الرمز التحقق
        $builder = new CaptchaBuilder;
        // إنشاء الرمز التحقق
        $builder->build();
        // تخزين قيمة الرمز التحقق في الجلسة
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // الحصول على بيانات الصورة الثنائية للرمز التحقق
        $img_content = $builder->get();
        // إخراج بيانات الصورة الثنائية للرمز التحقق
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * فحص الرمز التحقق
     */
    public function check(Request $request)
    {
        // الحصول على حقل الرمز التحقق من طلب الـpost
        $captcha = $request->post('captcha');
        // مقارنة قيمة الرمز التحقق في الجلسة
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'الرمز التحقق الذي أدخلته غير صحيح']);
        }
        return json(['code' => 0, 'msg' => 'حسنًا']);
    }

}
```

**إنشاء ملف القالب `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>اختبار الرمز التحقق</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="إرسال" />
    </form>
</body>
</html>
```

انتقل إلى الصفحة `http://127.0.0.1:8787/login` ستكون الواجهة مشابهة للصورة التالية:
  ![](../../assets/img/captcha.png)

### إعدادات المعلمات الشائعة
```php
    /**
     * إخراج صورة الرمز التحقق
     */
    public function captcha(Request $request)
    {
        // تهيئة فئة الرمز التحقق
        $builder = new CaptchaBuilder;
        // طول الرمز التحقق
        $length = 4;
        // الأحرف المتضمنة
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // إنشاء الرمز التحقق
        $builder->build();
        // تخزين قيمة الرمز التحقق في الجلسة
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // الحصول على بيانات الصورة الثنائية للرمز التحقق
        $img_content = $builder->get();
        // إخراج بيانات الصورة الثنائية للرمز التحقق
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

لمزيد من الواجهات البرمجية والمعلمات، راجع https://github.com/webman-php/captcha
