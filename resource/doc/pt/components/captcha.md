# Componentes relacionados com o código de verificação

## webman/captcha
Endereço do projeto: https://github.com/webman-php/captcha

### Instalação
```
composer require webman/captcha
```

### Uso

**Crie o arquivo `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Página de teste
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * Retorna a imagem do código de verificação
     */
    public function captcha(Request $request)
    {
        // Inicializa a classe de código de verificação
        $builder = new CaptchaBuilder;
        // Gera o código de verificação
        $builder->build();
        // Armazena o valor do código de verificação na sessão
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtém os dados binários da imagem do código de verificação
        $img_content = $builder->get();
        // Retorna os dados binários da imagem do código de verificação
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Verifica o código de verificação
     */
    public function check(Request $request)
    {
        // Obtém o campo de código de verificação da requisição POST
        $captcha = $request->post('captcha');
        // Compara o valor do código de verificação armazenado na sessão
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'O código de verificação inserido está incorreto']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Crie o arquivo de modelo `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teste de Código de Verificação</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="Enviar" />
    </form>
</body>
</html>
```

Acesse a página `http://127.0.0.1:8787/login`. A aparência da interface é semelhante à seguinte:
  ![](../../assets/img/captcha.png)

### Configurações comuns
```php
    /**
     * Retorna a imagem do código de verificação
     */
    public function captcha(Request $request)
    {
        // Inicializa a classe de código de verificação
        $builder = new CaptchaBuilder;
        // Comprimento do código de verificação
        $length = 4;
        // Caracteres incluídos
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Gera o código de verificação
        $builder->build();
        // Armazena o valor do código de verificação na sessão
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtém os dados binários da imagem do código de verificação
        $img_content = $builder->get();
        // Retorna os dados binários da imagem do código de verificação
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Para mais interfaces e parâmetros, consulte https://github.com/webman-php/captcha
