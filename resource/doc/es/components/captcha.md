# Componentes relacionados con el captcha

## webman/captcha
Dirección del proyecto: https://github.com/webman-php/captcha

### Instalación
``` 
composer require webman/captcha
```

### Uso

**Crear archivo `app/controller/LoginController.php`**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * Página de prueba
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * Mostrar imagen del captcha
     */
    public function captcha(Request $request)
    {
        // Inicializar la clase del captcha
        $builder = new CaptchaBuilder;
        // Generar el captcha
        $builder->build();
        // Almacenar el valor del captcha en la sesión
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtener los datos binarios de la imagen del captcha
        $img_content = $builder->get();
        // Mostrar los datos binarios del captcha
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Verificar el captcha
     */
    public function check(Request $request)
    {
        // Obtener el campo captcha de la solicitud POST
        $captcha = $request->post('captcha');
        // Comparar el valor del captcha en la sesión
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'El captcha ingresado no es correcto']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**Crear el archivo de plantilla `app/view/login/index.html`**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Prueba de Captcha</title>  
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

Al ingresar a la página `http://127.0.0.1:8787/login`, la interfaz se verá similar a la siguiente:
  ![](../../assets/img/captcha.png)

### Configuraciones comunes de los parámetros
```php
    /**
     * Mostrar imagen del captcha
     */
    public function captcha(Request $request)
    {
        // Inicializar la clase del captcha
        $builder = new CaptchaBuilder;
        // Longitud del captcha
        $length = 4;
        // Caracteres incluidos
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Generar el captcha
        $builder->build();
        // Almacenar el valor del captcha en la sesión
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtener los datos binarios de la imagen del captcha
        $img_content = $builder->get();
        // Mostrar los datos binarios del captcha
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Para más información sobre las interfaces y parámetros, consulta https://github.com/webman-php/captcha
