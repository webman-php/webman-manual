# CAPTCHA İlgili Bileşenler


## webman/captcha
Proje bağlantısı https://github.com/webman-php/captcha

### Kurulum
```composer require webman/captcha```

### Kullanım

**Dosya oluştur ```app/controller/LoginController.php```**

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
     * CAPTCHA görüntüsü çıkart
     */
    public function captcha(Request $request)
    {
        // CAPTCHA oluşturucuyu başlat
        $builder = new CaptchaBuilder;
        // CAPTCHA oluştur
        $builder->build();
        // CAPTCHA değerini oturuma kaydet
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // CAPTCHA görüntüsünün ikili verilerini al
        $img_content = $builder->get();
        // CAPTCHA ikili verilerini çıkart
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * CAPTCHA'yı kontrol et
     */
    public function check(Request $request)
    {
        // POST isteğindeki captcha alanını al
        $captcha = $request->post('captcha');
        // Oturumdaki captcha değeriyle karşılaştır
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Girilen CAPTCHA doğru değil']);
        }
        return json(['code' => 0, 'msg' => 'tamam']);
    }
}
```

**Şablon dosyası oluştur `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>CAPTCHA Test</title>  
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

`http://127.0.0.1:8787/login` adresine girildiğinde, sayfa aşağıdaki gibi görünecektir:
  ![](../../assets/img/captcha.png)

### Sık Kullanılan Parametre Ayarları
```php
    /**
     * CAPTCHA görüntüsü çıkart
     */
    public function captcha(Request $request)
    {
        // CAPTCHA oluşturucuyu başlat
        $builder = new CaptchaBuilder;
        // CAPTCHA uzunluğu
        $length = 4;
        // Hangi karakterleri içersin
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // CAPTCHA oluştur
        $builder->build();
        // CAPTCHA değerini oturuma kaydet
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // CAPTCHA görüntüsünün ikili verilerini al
        $img_content = $builder->get();
        // CAPTCHA ikili verilerini çıkart
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Daha fazla API ve parametre için https://github.com/webman-php/captcha adresine bakabilirsiniz.
