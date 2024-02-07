# Componenti relativi ai codici di verifica


## webman/captcha
Indirizzo del progetto https://github.com/webman-php/captcha

### Installazione
```
compositore richiedere webman/captcha
```

### Utilizzo

**Creare il file `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Pagina di prova
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * Stampare l'immagine del codice di verifica
     */
    public function captcha(Request $request)
    {
        // Inizializza la classe del codice di verifica
        $builder = new CaptchaBuilder;
        // Genera un codice di verifica
        $builder->build();
        // Salva il valore del codice di verifica nella sessione
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Ottiene dati binari dell'immagine del codice di verifica
        $img_content = $builder->get();
        // Restituisce i dati binari del codice di verifica
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Verifica il codice di verifica
     */
    public function check(Request $request)
    {
        // Ottiene il campo captcha dalla richiesta POST
        $captcha = $request->post('captcha');
        // Confronta il valore del captcha nella sessione
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Il codice di verifica inserito non è corretto']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Creare il file del modello `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test del codice di verifica</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="Invia" />
    </form>
</body>
</html>
```

Accedendo alla pagina `http://127.0.0.1:8787/login` l'aspetto sarà simile a quanto segue:
![](../../assets/img/captcha.png)

### Impostazioni dei parametri comuni
```php
    /**
     * Stampare l'immagine del codice di verifica
     */
    public function captcha(Request $request)
    {
        // Inizializza la classe del codice di verifica
        $builder = new CaptchaBuilder;
        // Lunghezza del codice di verifica
        $length = 4;
        // Caratteri inclusi
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Genera un codice di verifica
        $builder->build();
        // Salva il valore del codice di verifica nella sessione
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Ottiene dati binari dell'immagine del codice di verifica
        $img_content = $builder->get();
        // Restituisce i dati binari del codice di verifica
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Per ulteriori informazioni sull'API e i parametri, fare riferimento a https://github.com/webman-php/captcha
