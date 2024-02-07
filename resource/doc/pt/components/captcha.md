# Componentes relacionados com o código de verificação


## webman/captcha
Endereço do projeto: https://github.com/webman-php/captcha

### Instalação
``` 
compositor exigir webman / captcha
``` 

### Uso

**Criação do arquivo `app/controller/LoginController.php`** 

``` php
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
     * Produzir imagem de verificação de código
     */
    public function captcha(Request $request)
    {
        // Inicialize a classe de verificação do código
        $builder = new CaptchaBuilder;
        // Gerar código de verificação
        $builder->build();
        // Armazenar o valor do código de verificação na sessão
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obter os dados binários da imagem de verificação
        $img_content = $builder->get();
        // Produzir dados binários da imagem de verificação
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Verificar código de verificação
     */
    public function check(Request $request)
    {
        // Obter o campo de verificação do código da solicitação POST
        $captcha = $request->post('captcha');
        // Comparar o valor do código de verificação na sessão
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'O código de verificação inserido está incorreto']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
``` 

**Criação do arquivo de modelo `app/view/login/index.html`** 

`html
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

Acesse a página `http://127.0.0.1:8787/login`, a aparência é semelhante à seguinte: 
  ![](../../assets/img/captcha.png)

### Configurações Comuns de Parâmetros
``` php
    /**
     * Produzir imagem de verificação de código
     */
    public function captcha(Request $request)
    {
        // Inicialize a classe de verificação do código
        $builder = new CaptchaBuilder;
        // Comprimento do código de verificação
        $length = 4;
        // Caracteres incluídos
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Gerar código de verificação
        $builder->build();
        // Armazenar o valor do código de verificação na sessão
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obter os dados binários da imagem de verificação
        $img_content = $builder->get();
        // Produzir dados binários da imagem de verificação
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
``` 

Para mais interfaces e parâmetros, consulte https://github.com/webman-php/captcha
