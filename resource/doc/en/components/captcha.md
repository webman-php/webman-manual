# Captcha Related Components


## webman/captcha
Project address https://github.com/webman-php/captcha

### Install
```
composer require webman/captcha
```

### Usage

**Create file `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Test Page
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * output captcha image
     */
    public function captcha(Request $request)
    {
        // initialize captcha class
        $builder = new CaptchaBuilder;
        // Generate CAPTCHA
        $builder->build();
        // Store the value of the captcha in the session
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Get captcha image binary data
        $img_content = $builder->get();
        // Exporting CAPTCHA binary data
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Check CAPTCHA
     */
    public function check(Request $request)
    {
        // Get the captcha field in post requests
        $captcha = $request->post('captcha');
        // Compare captcha values in session
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Incorrectly entered captcha']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Creating Template Files`app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Captcha Test</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="Submit" />
    </form>
</body>
</html>
```

Go to page `http://127.0.0.1:8787/login` The interface is similar to the following：
  ![](img/captcha.png)