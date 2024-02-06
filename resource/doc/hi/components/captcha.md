# कैप्चा संबंधित कंपोनेंट

## webman/captcha
प्रोजेक्ट लिंक https://github.com/webman-php/captcha

### स्थापना
```
कमांड कोम्पोजर का उपयोग कर webman/captcha के लिए इंस्टॉल करें
```

### उपयोग

**फ़ाइल बनाएं `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * परीक्षण पेज
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * कैप्चा छवि उत्पन्न करें
     */
    public function captcha(Request $request)
    {
        // कैप्चा बिल्डर का प्रारंभ
        $builder = new CaptchaBuilder;
        // कैप्चा उत्पन्न करें
        $builder->build();
        // कैप्चा मान को सत्रांश में स्टोर करें
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // कैप्चा छवि के बाइनरी डेटा प्राप्त करें
        $img_content = $builder->get();
        // कैप्चा बाइनरी डेटा प्रदान करें
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * कैप्चा की जाँच करें
     */
    public function check(Request $request)
    {
        // captcha फ़ील्ड को पोस्ट अनुरोध से प्राप्त करें
        $captcha = $request->post('captcha');
        // सत्रांश में captcha मान की तुलना करें
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'दर्ज किया गया कैप्चा गलत है']);
        }
        return json(['code' => 0, 'msg' => 'ठीक है']);
    }

}
```

**टेम्पलेट फ़ाइल बनाएं`app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>कैप्चा परीक्षण</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="सबमिट" />
    </form>
</body>
</html>
```

पेज पर जाएँ `http://127.0.0.1:8787/login` और दस्तावेज़ की तरह दिखिएगा:
  ![](../../assets/img/captcha.png)

### आम चरण सेटिंग्स
```php
    /**
     * कैप्चा छवि उत्पन्न करें
     */
    public function captcha(Request $request)
    {
        // कैप्चा बिल्डर का प्रारंभ
        $builder = new CaptchaBuilder;
        // कैप्चा लंबाई
        $length = 4;
        // किस चरित्रों को शामिल करें
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // कैप्चा उत्पन्न करें
        $builder->build();
        // कैप्चा मान को सत्रांश में स्टोर करें
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // कैप्चा छवि के बाइनरी डेटा प्राप्त करें
        $img_content = $builder->get();
        // कैप्चा बाइनरी डेटा प्रदान करें
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

अधिक विवरण और पैरामीटर देखें https://github.com/webman-php/captcha
