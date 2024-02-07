## ThinkCache

### Установка ThinkCache  
`composer require -W webman/think-cache`

После установки требуется перезапустить (reload не подходит)


> [webman/think-cache](https://www.workerman.net/plugin/15) фактически является плагином для автоматической установки `toptink/think-cache`.

> **Внимание**
> toptink/think-cache не поддерживает php8.1
  
### Файл настроек

Файл настроек находится здесь: `config/thinkcache.php`

### Использование

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

### Документация по использованию Think-Cache

[Ссылка на документацию по ThinkCache](https://github.com/top-think/think-cache)
