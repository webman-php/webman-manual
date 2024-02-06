# 验证码相关组件


## webman/captcha
项目地址 https://github.com/webman-php/captcha

### 安装
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
     * 测试页面
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * 输出验证码图像
     */
    public function captcha(Request $request)
    {
        // 初始化验证码类
        $builder = new CaptchaBuilder;
        // 生成验证码
        $builder->build();
        // 将验证码的值存储到session中
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // 获得验证码图片二进制数据
        $img_content = $builder->get();
        // 输出验证码二进制数据
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 检查验证码
     */
    public function check(Request $request)
    {
        // 获取post请求中的captcha字段
        $captcha = $request->post('captcha');
        // 对比session中的captcha值
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => '输入的验证码不正确']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**建立模版文件`app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>验证码测试</title>  
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

进入页面`http://127.0.0.1:8787/login` 界面类似如下：
  ![](../../assets/img/captcha.png)

### 常见参数设置
```php
    /**
     * 输出验证码图像
     */
    public function captcha(Request $request)
    {
        // 初始化验证码类
        $builder = new CaptchaBuilder;
        // 验证码长度
        $length = 4;
        // 包含哪些字符
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // 生成验证码
        $builder->build();
        // 将验证码的值存储到session中
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // 获得验证码图片二进制数据
        $img_content = $builder->get();
        // 输出验证码二进制数据
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

更多接口及参数参考 https://github.com/webman-php/captcha
