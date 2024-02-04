# Captcha-bezogene Komponenten

## webman/captcha
Projektadresse https://github.com/webman-php/captcha

### Installation
```
composer require webman/captcha
```

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
     * Ausgabe des Captcha-Bildes
     */
    public function captcha(Request $request)
    {
        // Initialisieren der Captcha-Klasse
        $builder = new CaptchaBuilder;
        // Captcha erstellen
        $builder->build();
        // Den Wert des Captcha in der Session speichern
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Binärdaten des Captcha-Bildes abrufen
        $img_content = $builder->get();
        // Binärdaten des Captcha ausgeben
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Überprüfen des Captchas
     */
    public function check(Request $request)
    {
        // Den captcha-Wert aus dem POST-Request abrufen
        $captcha = $request->post('captcha');
        // Den captcha-Wert in der Session vergleichen
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Der eingegebene Captcha-Code ist inkorrekt']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Erstellen Sie die Vorlagendatei `app/view/login/index.html`**

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

Gehen Sie zur Seite `http://127.0.0.1:8787/login`. Die Oberfläche ähnelt der folgenden:
  ![](../../assets/img/captcha.png)

### Häufige Parameter-Einstellungen
```php
    /**
     * Ausgabe des Captcha-Bildes
     */
    public function captcha(Request $request)
    {
        // Initialisieren der Captcha-Klasse
        $builder = new CaptchaBuilder;
        // Captcha-Länge
        $length = 4;
        // Enthaltene Zeichen
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Captcha erstellen
        $builder->build();
        // Den Wert des Captcha in der Session speichern
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Binärdaten des Captcha-Bildes abrufen
        $img_content = $builder->get();
        // Binärdaten des Captcha ausgeben
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Weitere Schnittstellen und Parameter finden Sie unter https://github.com/webman-php/captcha
