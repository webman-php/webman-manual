# 驗證碼相關組件


## webman/captcha
專案地址 https://github.com/webman-php/captcha

### 安裝
``` 
composer require webman/captcha
```

### 使用

**建立文件 `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * 測試頁面
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * 輸出驗證碼圖像
     */
    public function captcha(Request $request)
    {
        // 初始化驗證碼類
        $builder = new CaptchaBuilder;
        // 生成驗證碼
        $builder->build();
        // 將驗證碼的值存儲到session中
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // 獲取驗證碼圖片二進制數據
        $img_content = $builder->get();
        // 輸出驗證碼二進制數據
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 檢查驗證碼
     */
    public function check(Request $request)
    {
        // 獲取post請求中的captcha字段
        $captcha = $request->post('captcha');
        // 對比session中的captcha值
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => '輸入的驗證碼不正確']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**建立模板文件`app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>驗證碼測試</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="提交" />
    </form>
</body>
</html>
```

進入頁面`http://127.0.0.1:8787/login` 界面類似如下：
  ![](../../assets/img/captcha.png)

### 常見參數設置
```php
    /**
     * 輸出驗證碼圖像
     */
    public function captcha(Request $request)
    {
        // 初始化驗證碼類
        $builder = new CaptchaBuilder;
        // 驗證碼長度
        $length = 4;
        // 包含哪些字符
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // 生成驗證碼
        $builder->build();
        // 將驗證碼的值存儲到session中
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // 獲得驗證碼圖片二進制數據
        $img_content = $builder->get();
        // 輸出驗證碼二進制數據
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

更多接口及參數參考 https://github.com/webman-php/captcha
