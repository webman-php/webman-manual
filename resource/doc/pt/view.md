## Visualização
Por padrão, o webman utiliza a sintaxe nativa do PHP como modelo, e, ao abrir o `opcache`, tem o melhor desempenho. Além do modelo nativo do PHP, o webman também fornece mecanismos de modelo [Twig](https://twig.symfony.com/doc/3.x/), [Blade](https://learnku.com/docs/laravel/8.x/blade/9377) e [think-template](https://www.kancloud.cn/manual/think-template/content).

## Habilitando o opcache
Ao usar a visualização, é altamente recomendável habilitar as opções `opcache.enable` e `opcache.enable_cli` no arquivo `php.ini` para garantir o melhor desempenho do mecanismo de modelo.

## Instalando o Twig
1. Instalação via composer

   `composer require twig/twig`
   
2. Modifique a configuração em `config/view.php` para:

   ```php
   <?php
   use support\view\Twig;
   
   return [
       'handler' => Twig::class
   ];
   ```

   > **Dica**
   > Outras opções de configuração podem ser passadas através de opções, por exemplo:

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

   `composer require psr/container ^1.1.1 webman/blade`

2. Modifique a configuração em `config/view.php` para:

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

2. Modifique a configuração em `config/view.php` para:

   ```php
   <?php
   use support\view\ThinkPHP;
   
   return [
       'handler' => ThinkPHP::class,
   ];
   ```

   > **Dica**
   > Outras opções de configuração podem ser passadas através de opções, por exemplo:

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
Crie o arquivo `app/controller/UserController.php` com o seguinte conteúdo:

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

Crie o arquivo `app/view/user/hello.html` com o seguinte conteúdo:

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
Modifique a configuração em `config/view.php` para:

```php
<?php
use support\view\Twig;

return [
    'handler' => Twig::class
];
```

`app/controller/UserController.php` com o seguinte conteúdo:

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

O arquivo `app/view/user/hello.html` com o seguinte conteúdo:

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

## Exemplo de motor de modelo Blade
Modifique a configuração em `config/view.php` para:

```php
<?php
use support\view\Blade;

return [
    'handler' => Blade::class
];
```

`app/controller/UserController.php` com o seguinte conteúdo:

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

O arquivo `app/view/user/hello.blade.php` com o seguinte conteúdo:

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

## Exemplo de motor de modelo ThinkPHP
Modifique a configuração em `config/view.php` para:

```php
<?php
use support\view\ThinkPHP;

return [
    'handler' => ThinkPHP::class
];
```

`app/controller/UserController.php` com o seguinte conteúdo:

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

O arquivo `app/view/user/hello.html` com o seguinte conteúdo:

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
Além de usar `view(template, data)` para atribuir modelos, também podemos atribuir modelos em qualquer local chamando `View::assign()`. Por exemplo:

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

`View::assign()` é muito útil em alguns cenários, como em um sistema em que cada página deve exibir informações do usuário conectado. Se todas as páginas tiverem que atribuir estas informações usando `view('template', ['user_info' => 'user info'])`, seria muito trabalhoso. Uma solução seria obter as informações do usuário em um middleware e então atribuí-las ao modelo usando `View::assign()`.

## Sobre o caminho do arquivo de visualização

#### Controladores
Quando os controladores chamam `view('template',[])`, os arquivos de visualização são pesquisados de acordo com as seguintes regras:

1. Se não houver múltiplos aplicativos, utilize os arquivos de visualização em `app/view/`.
2. Para [múltiplos aplicativos](multiapp.md), utilize os arquivos de visualização em `app/nome_do_aplicativo/view/`.

Resumindo, se `$request->app` estiver vazio, utilize os arquivos de visualização em `app/view/`; caso contrário, utilize os arquivos de visualização em `app/{$request->app}/view/`.

#### Funções de fechamento
Como `$request->app` está vazio, não pertence a nenhum aplicativo, então as funções de fechamento utilizam os arquivos de visualização em `app/view/`, por exemplo, a definição de rota em `config/route.php`:

```php
Route::any('/admin/user/get', function (Reqeust $reqeust) {
    return view('user', []);
});
```

Usará o arquivo `app/view/user.html` como modelo (quando usando o modelo blade, o arquivo de modelo deve ser `app/view/user.blade.php`).

#### Aplicativo especificado
Para reutilizar modelos em modos de aplicativos múltiplos, a função `view($template, $data, $app = null)` fornece o terceiro parâmetro `$app` para especificar qual aplicativo utilizar para os arquivos de visualização. Por exemplo, `view('user', [], 'admin')` utilizará os arquivos de visualização em `app/admin/view/`.

## Estendendo o twig

> **Nota**
> Este recurso requer webman-framework >= 1.4.8

Podemos estender a instância de visualização do twig fornecendo o callback `view.extension` nas configurações. Por exemplo, em `config/view.php`:

```php
<?php
use support\view\Twig;
return [
    'handler' => Twig::class,
    'extension' => function (Twig\Environment $twig) {
        $twig->addExtension(new your\namespace\YourExtension()); // Adicionar extensão
        $twig->addFilter(new Twig\TwigFilter('rot13', 'str_rot13')); // Adicionar filtro
        $twig->addFunction(new Twig\TwigFunction('function_name', function () {})); // Adicionar função
    }
];
```
## Extensão Blade
> **Observação**
> Este recurso requer webman-framework>=1.4.8
Da mesma forma, podemos estender a instância de visualização do Blade fornecendo um retorno de chamada para a configuração `view.extension`, como no exemplo abaixo em `config/view.php`:

```php
<?php
use support\view\Blade;
return [
    'handler' => Blade::class,
    'extension' => function (Jenssegers\Blade\Blade $blade) {
        // Adiciona diretivas ao blade
        $blade->directive('mydate', function ($timestamp) {
            return "<?php echo date('Y-m-d H:i:s', $timestamp); ?>";
        });
    }
];
```

## Utilização de componentes blade

> **Observação**
> Requer webman/blade>=1.5.2

Suponha que seja necessário adicionar um componente de Alerta.

**Crie `app/view/components/Alert.php`**
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

**Crie `app/view/components/alert.blade.php`**
```php
<div>
    <b style="color: red">hello blade component</b>
</div>
```

**`/config/view.php` possuirá um código semelhante ao abaixo**
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

Assim, o componente Alerta do Blade está configurado. O uso no template será semelhante ao seguinte
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


## Extensão think-template
O think-template utiliza `view.options.taglib_pre_load` para estender bibliotecas de tags, por exemplo
```php
<?php
use support\view\ThinkPHP;
return [
    'handler' => ThinkPHP::class,
    'options' => [
        'taglib_pre_load' => your\namspace\Taglib::class,
    ]
];
```

Para mais detalhes, consulte [Extensão de tags think-template](https://www.kancloud.cn/manual/think-template/1286424)
