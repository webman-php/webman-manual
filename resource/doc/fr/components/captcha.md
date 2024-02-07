# Composant de captcha

## webman/captcha
Adresse du projet https://github.com/webman-php/captcha

### Installation
```composer require webman/captcha```

### Utilisation

**Créer le fichier `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Page de test
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * Afficher l'image du captcha
     */
    public function captcha(Request $request)
    {
        // Initialisation de la classe de captcha
        $builder = new CaptchaBuilder;
        // Générer le captcha
        $builder->build();
        // Stocker la valeur du captcha dans la session
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtenir les données binaires de l'image du captcha
        $img_content = $builder->get();
        // Afficher les données binaires du captcha
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Vérifier le captcha
     */
    public function check(Request $request)
    {
        // Obtenir le champ captcha de la requête post
        $captcha = $request->post('captcha');
        // Comparer la valeur du captcha dans la session
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'Le captcha saisi est incorrect']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Créer le fichier de modèle `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Test de captcha</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="Soumettre" />
    </form>
</body>
</html>
```

Accédez à la page `http://127.0.0.1:8787/login`, l'interface ressemble à ceci :
  ![](../../assets/img/captcha.png)

### Paramètres courants
```php
    /**
     * Afficher l'image du captcha
     */
    public function captcha(Request $request)
    {
        // Initialiser la classe de captcha
        $builder = new CaptchaBuilder;
        // Longueur du captcha
        $length = 4;
        // Caractères inclus
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Générer le captcha
        $builder->build();
        // Stocker la valeur du captcha dans la session
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtenir les données binaires de l'image du captcha
        $img_content = $builder->get();
        // Afficher les données binaires du captcha
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Pour plus d'interfaces et de paramètres, consultez https://github.com/webman-php/captcha
