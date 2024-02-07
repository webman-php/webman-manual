## ThinkCache

### Instalação do ThinkCache  
`composer require -W webman/think-cache`

Após a instalação, é necessário reiniciar (reload não é válido)

> [webman/think-cache](https://www.workerman.net/plugin/15) é na verdade um plugin de instalação automática do `toptink/think-cache`.

> **Nota**
> toptink/think-cache não suporta o PHP 8.1

### Arquivo de Configuração

O arquivo de configuração está localizado em `config/thinkcache.php`

### Uso

```php
<?php
namespace app\controller;
  
use support\Request;
use think\facade\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```
  
### Documentação de Uso do Think-Cache

[Endereço da documentação do ThinkCache](https://github.com/top-think/think-cache)
