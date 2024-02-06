# Captcha İlgili Bileşenler


## webman/captcha
Proje bağlantısı: https://github.com/webman-php/captcha

### Kurulum
```
composer require webman/captcha
```

### Kullanım

**`app/controller/LoginController.php` dosyası oluşturun.**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Test sayfası
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * Captcha görüntüsünü çıkar
     */
    public function captcha(Request $request)
    {
        // CaptchaBuilder sınıfını başlat
        $builder = new CaptchaBuilder;
        // Captcha oluştur
        $builder->build();
        // Captcha değerini oturuma depolayın
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Captcha görüntüsünün ikili verilerini al
        $img_content = $builder->get();
        // Captcha ikili verilerini çıkar
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Captcha kontrolü
     */
    public function check(Request $request)
    {
        // post isteğinden captcha alanını al
        $captcha = $request->post('captcha');
        // Oturumdaki captcha değeri ile karşılaştır
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Girilen captcha doğru değil']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**`app/view/login/index.html` dosyası oluşturun.**

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
        <input type="submit" value="Gönder" />
    </form>
</body>
</html>
```

`http://127.0.0.1:8787/login` adresine gidildiğinde, sayfa aşağıdaki gibi görünecektir:
  ![](../../assets/img/captcha.png)

### Yaygın Parametre Ayarları
```php
    /**
     * Captcha görüntüsünü çıkar
     */
    public function captcha(Request $request)
    {
        // CaptchaBuilder sınıfını başlat
        $builder = new CaptchaBuilder;
        // Captcha uzunluğu
        $length = 4;
        // Hangi karakterleri içersin
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Captcha oluştur
        $builder->build();
        // Captcha değerini oturuma depolayın
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Captcha görüntüsünün ikili verilerini al
        $img_content = $builder->get();
        // Captcha ikili verilerini çıkar
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Daha fazla API ve parametre için https://github.com/webman-php/captcha adresine bakın.
