## ThinkCache

### Instalação do ThinkCache  
`composer require -W webman/think-cache`

Após a instalação é necessário reiniciar (reload não é válido)


> [webman/think-cache](https://www.workerman.net/plugin/15) é na realidade um plugin para instalar automaticamente o `toptink/think-cache`.

> **Atenção**
> toptink/think-cache não suporta o PHP 8.1
  
### Arquivo de Configuração

O arquivo de configuração está em `config/thinkcache.php`

### Utilização

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

[Link para a documentação do ThinkCache](https://github.com/top-think/think-cache)
