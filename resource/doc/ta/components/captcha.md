# சோதித்தல் குறியீட்டு தொகுப்புகள்
## webman/captcha
திட்டத்தின் முகவரி https://github.com/webman-php/captcha

### நிறுவு
```
composer require webman/captcha
```

### பயன்பாடு

**கோப்பு `app/controller/LoginController.php` ஐ உருவாக்கு**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * சோதனை பக்கம்
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * சான்றுகளை வெளிப்படுத்தும்
     */
    public function captcha(Request $request)
    {
        // சான்றுகள் படித்தாக்கக் கட்டளை ஆரம்பிப்பது
        $builder = new CaptchaBuilder;
        // சான்று உருவாக்குக்கு
        $builder->build();
        // சான்றுகளின் மதிப்பை session இல் சேமிக்க
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // சான்று படத்தின் இருகமுறை தரவைப் பெறுங்கள்
        $img_content = $builder->get();
        // சான்று அடங்காத தரவைப் புறக்காக்கப்படுத்துக
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * சான்றுகளை சரிபார்க்கவும்
     */
    public function check(Request $request)
    {
        // போஸ்ட் வேலையான captcha புலத்தைப் பெறுங்கள்
        $captcha = $request->post('captcha');
        // session இல் உள்ள captcha மதிப்பை ஒப்பிடுங்கள்
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'உள்ளிட்ட சான்று தவறானது']);
        }
        return json(['code' => 0, 'msg' => 'சரி']);
    }
}
```

**தொகுப்பு கோப்பை `app/view/login/index.html` உருவாக்கவும்**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>சான்று சோதனை</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="சமர்ப்பிக்கவும்" />
    </form>
</body>
</html>
```

பக்கம் `http://127.0.0.1:8787/login` உள்ளிடப்பட்டால் பக்கம் போன்றதாகும்:
  ![](../../assets/img/captcha.png)

### பொதுவான அமைப்புகள்
```php
    /**
     * சான்றுகளை வெளிப்படுத்தும்
     */
    public function captcha(Request $request)
    {
        // சான்றுகள் படித்தாக்கத்தை ஆரம்பிப்பது
        $builder = new CaptchaBuilder;
        // சான்று நீளம்
        $length = 4;
        // எந்த எழுத்துகள் அடைவதற்குச் சேர்க்கப்படுகின்றன
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // சான்று உருவாக்குங்கள்
        $builder->build();
        // சான்றுகளின் மதிப்பை session இல் சேமிக்க
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // சான்று படத்தின் இருகமுறை தரவைப் பெறுங்கள்
        $img_content = $builder->get();
        // சான்று அடங்காத தரவைப் புறக்காக்கப்படுத்துங்கள்
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

மேலும் அனைத்து அமைப்புகள் மற்றும் அளபுருக்களுக்கு https://github.com/webman-php/captcha பார்வையிடவும்
