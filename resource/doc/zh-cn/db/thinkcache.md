
## ThinkCache

### 安装ThinkCache  
`composer require -W webman/think-cache`

安装后需要restart重启(reload无效)


> [webman/think-cache](https://www.workerman.net/plugin/15) 实际上是一个自动化安装`toptink/think-cache` 的插件。

> **注意**
> toptink/think-cache 不支持php8.1
  
### 配置文件

配置文件为 `config/thinkcache.php`

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
### Think-Cache使用文档

[ThinkCache文档地址](https://github.com/top-think/think-cache)
