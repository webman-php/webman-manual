## Visualizações
Por padrão, o webman usa a sintaxe nativa do PHP como modelo, e tem o melhor desempenho quando `opcache` está ligado. Além do modelo nativo do PHP, o webman também oferece os motores de modelo [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) e [think-template](https://www.kancloud.cn/manual/think-template/content).

## Habilitando o opcache
Ao usar visualizações, é altamente recomendável habilitar as opções `opcache.enable` e `opcache.enable_cli` no arquivo php.ini para que o mecanismo de visualização atinja o melhor desempenho. 

## Instalando o Twig
1. Instalação via composer

`composer require twig/twig`

2. Altere a configuração `config/view.php` para

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```
> **Dica**
> Outras opções de configuração podem ser passadas por meio de options, por exemplo  

```php
return [
    'handler' => Twig::class,
    'options' => [
        'debug' => false,
        'charset' => 'utf-8'
    ]
];
```


## Instalando o Blade
1. Instalação via composer

```
composer require psr/container ^1.1.1 webman/blade
```

2. Altere a configuração `config/view.php` para

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

## Instalando o think-template
1. Instalação via composer

`composer require topthink/think-template`

2. Altere a configuração `config/view.php` para

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class,
];
```
> **Dica**
> Outras opções de configuração podem ser passadas por meio de options, por exemplo

```php
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'view_suffix' => 'html',
        'tpl_begin' => '{',
        'tpl_end' => '}'
    ]
];
```

## Exemplo de motor de modelo PHP nativo
Crie o arquivo `app/controller/UserController.php` como abaixo

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

Crie o arquivo `app/view/user/hello.html` como abaixo

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello <?=htmlspecialchars($name)?>
</body>
</html>
```

## Exemplo de motor de modelo Twig

Altere a configuração `config/view.php` para
```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` como abaixo

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

O arquivo `app/view/user/hello.html` como abaixo

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{name}}
</body>
</html>
```

Para mais documentação, consulte [Twig](https://twig.symfony.com/doc/3.x/)

## Exemplo de modelo Blade
Altere a configuração `config/view.php` para
```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` como abaixo

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

O arquivo `app/view/user/hello.blade.php` como abaixo

> Observe que a extensão do modelo blade é `.blade.php`

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {{$name}}
</body>
</html>
```

Para mais documentação, consulte [Blade](https://learnku.com/docs/laravel/8.x/blade/9377)

## Exemplo de modelo think-template
Altere a configuração `config/view.php` para
```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` como abaixo

```php
<?php
namespace app\controller;

use support\Request;

class UserController
{
    public function hello(Request $request)
    {
        return view('user/hello', ['name' => 'webman']);
    }
}
```

O arquivo `app/view/user/hello.html` como abaixo

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>
hello {$name}
</body>
</html>
```

Para mais documentação, consulte [think-template](https://www.kancloud.cn/manual/think-template/content)

## Atribuição de modelos
Além de usar `view(template, data)` para atribuir modelos, também podemos atribuir modelos em qualquer lugar chamando `View::assign()`. Por exemplo:
```php
<?php
namespace app\controller;

use support\Request;
use support\View;

class UserController
{
    public function hello(Request $request)
    {
        View::assign([
            'name1' => 'value1',
            'name2'=> 'value2',
        ]);
        View::assign('name3', 'value3');
        return view('user/test', ['name' => 'webman']);
    }
}
```

`View::assign()` é muito útil em alguns cenários, como em sistemas em que cada página precisa exibir as informações do usuário que fez o login. Nesse caso, em vez de atribuir as informações do usuário a cada página usando `view('template', ['user_info' => 'user info'])`, podemos obter as informações do usuário por meio de um middleware e, em seguida, atribuir as informações do usuário ao modelo usando `View::assign()`.

## Sobre o caminho do arquivo de visualização
#### Controlador
Quando o controlador chama `view('nome_do_modelo', [])`, o arquivo de visualização é procurado conforme as regras abaixo:

1. Se não houver vários aplicativos, o arquivo de visualização correspondente é procurado em `app/view/`.
2. No caso de [vários aplicativos](multiapp.md), o arquivo de visualização correspondente é procurado em `app/nome_do_aplicativo/view/`.

Resumindo, se `$request->app` estiver vazio, o arquivo de visualização será procurado em `app/view/`; caso contrário, será procurado em `app/{$request->app}/view/`.

#### Função de fechamento
Como `$request->app` está vazio, não pertence a nenhum aplicativo, então a função de fechamento usará o arquivo de visualização em `app/view/`. Por exemplo, se um roteamento estiver definido em `config/route.php` assim:

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```

Esse roteamento usará o arquivo de visualização `app/view/user.html` como modelo (se for um modelo blade, será `app/view/user.blade.php`).

#### Especificando um aplicativo
Para reutilizar modelos em modo de aplicativo múltiplo, `view($template, $data, $app = null)` fornece um terceiro parâmetro `$app`, que pode ser usado para especificar qual diretório de modelo em um aplicativo deve ser usado. Por exemplo, `view('user', [], 'admin')` forçará o uso do arquivo de visualização em `app/admin/view/`.

## Estendendo o twig

> **Nota**
> Esta funcionalidade requer webman-framework>=1.4.8

Podemos estender a instância de visualização do twig, fornecendo uma chamada de retorno para a configuração `view.extension`, por exemplo, em `config/view.php`:

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new seu\namespace\SuaExtensao()); // Adiciona extensão
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Adiciona filtro
        $twig->addFunction(new Twig\TwigFunction('nome_funcao', function () {})); // Adiciona função
    }
];
```

## Estendendo o blade
> **Nota**
> Esta funcionalidade requer webman-framework>=1.4.8
Da mesma forma, podemos estender a instância de visualização do blade fornecendo uma chamada de retorno para a configuração `view.extension`, por exemplo, em `config/view.php`:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Adiciona diretiva ao blade
        $blade->directive('minhadatabr', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Usando componentes de blade

> **Observação
> Requer webman/blade>=1.5.2**

Suponha que seja necessário adicionar um componente de alerta.

**Crie um arquivo `app/view/components/Alert.php`**
```php
<?php

namespace app\view\components;

use Illuminate\View\Component;

class Alert extends Component
{
    
    public function __construct()
    {
    
    }
    
    public function render()
    {
        return view('components/alert')->rawBody();
    }
}
```

**Crie um arquivo `app/view/components/alert.blade.php`**
```
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` será semelhante ao código a seguir**

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        $blade->component('alert', app\view\components\Alert::class);
    }
];
```

Com isso, o componente de alerta do Blade está configurado. O uso no modelo é semelhante ao exemplo a seguir:
```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>webman</title>
</head>
<body>

<x-alert/>

</body>
</html>
```

## Estendendo think-template
O think-template usa `view.options.taglib_pre_load` para estender a biblioteca de tags, por exemplo

```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => seu\nome\espaço\Taglib::class,
    ]
];
```

Para mais detalhes, consulte [Extensão de tags think-template](https://www.kancloud.cn/manual/think-template/1286424)

