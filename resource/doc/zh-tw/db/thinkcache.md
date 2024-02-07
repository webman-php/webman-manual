## ThinkCache

### 安裝ThinkCache  
`composer require -W webman/think-cache`

安裝後需要restart重啟(reload無效)


> [webman/think-cache](https://www.workerman.net/plugin/15) 實際上是一個自動安裝`toptink/think-cache` 的插件。

> **注意**
> toptink/think-cache 不支持php8.1
  
### 設定檔

設定檔位於 `config/thinkcache.php`

### 使用

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

### Think-Cache使用文件

[ThinkCache文檔地址](https://github.com/top-think/think-cache)
