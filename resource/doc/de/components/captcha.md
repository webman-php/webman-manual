# Captcha-bezogene Komponenten

## webman/captcha
Projektadresse https://github.com/webman-php/captcha

### Installation
```composer require webman/captcha```

### Verwendung

**Erstellen Sie die Datei `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Testseite
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * Generieren des Captcha-Bildes
     */
    public function captcha(Request $request)
    {
        // Captcha-Klasse initialisieren
        $builder = new CaptchaBuilder;
        // Captcha generieren
        $builder->build();
        // Captcha-Wert in der Sitzung speichern
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Binärdaten des Captcha-Bildes erhalten
        $img_content = $builder->get();
        // Binärdaten des Captcha ausgeben
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Captcha überprüfen
     */
    public function check(Request $request)
    {
        // captcha-Feld aus dem POST-Request abrufen
        $captcha = $request->post('captcha');
        // Captcha-Wert in der Sitzung vergleichen
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Eingegebene Captcha ist nicht korrekt']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Erstellen Sie die Template-Datei `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Captcha-Test</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="Einreichen" />
    </form>
</body>
</html>
```

Rufe die Seite `http://127.0.0.1:8787/login` auf, das Interface ähnelt dem folgenden:
![](../../assets/img/captcha.png)

### Häufige Parameterkonfiguration
```php
    /**
     * Generieren des Captcha-Bildes
     */
    public function captcha(Request $request)
    {
        // Captcha-Klasse initialisieren
        $builder = new CaptchaBuilder;
        // Captchalänge
        $length = 4;
        // Enthaltene Zeichen
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Captcha generieren
        $builder->build();
        // Captcha-Wert in der Sitzung speichern
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Binärdaten des Captcha-Bildes erhalten
        $img_content = $builder->get();
        // Binärdaten des Captcha ausgeben
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Weitere Schnittstellen und Parameter finden Sie unter https://github.com/webman-php/captcha
