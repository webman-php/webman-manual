# Componentes relacionados con los códigos de verificación

## webman/captcha
Dirección del proyecto: https://github.com/webman-php/captcha

### Instalación
```php
composer require webman/captcha
```

### Uso

**Crear el archivo `app/controller/LoginController.php`**

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
     * Generar imagen de verificación
     */
    public function captcha(Request $request)
    {
        // Inicializar la clase de verificación
        $builder = new CaptchaBuilder;
        // Generar el código de verificación
        $builder->build();
        // Almacenar el valor de la verificación en la sesión
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtener los datos binarios de la imagen de verificación
        $img_content = $builder->get();
        // Devolver los datos binarios de la imagen de verificación
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * Verificar la verificación
     */
    public function check(Request $request)
    {
        // Obtener el campo de verificación de la solicitud POST
        $captcha = $request->post('captcha');
        // Comparar el valor de verificación en la sesión
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => 'El código de verificación ingresado no es correcto']);
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
    <title>Prueba de Verificación</title>  
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

Ir a la página `http://127.0.0.1:8787/login`, la interfaz será similar a la siguiente:
![](../../assets/img/captcha.png)

### Configuración de parámetros comunes
```php
    /**
     * Generar imagen de verificación
     */
    public function captcha(Request $request)
    {
        // Inicializar la clase de verificación
        $builder = new CaptchaBuilder;
        // Longitud de la verificación
        $length = 4;
        // Caracteres permitidos
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // Generar el código de verificación
        $builder->build();
        // Almacenar el valor de la verificación en la sesión
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // Obtener los datos binarios de la imagen de verificación
        $img_content = $builder->get();
        // Devolver los datos binarios de la imagen de verificación
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

Para obtener más información sobre la API y los parámetros, consulta https://github.com/webman-php/captcha
