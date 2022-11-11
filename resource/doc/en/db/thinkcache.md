
## ThinkCache

### InstallThinkCache  
`composer require psr/container ^1.1.1 webman/think-cache`

> [webman/think-cache](https://www.workerman.net/plugin/15) This is actually an automated installation of the `toptink/think-cache` plugin。
  
### configuration file

Configuration file for `config/thinkcache.php`

### Usage

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
### Think-CacheUsing the documentation

[ThinkCacheDocument Address](https://github.com/top-think/think-cache)
