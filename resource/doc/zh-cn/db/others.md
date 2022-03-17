# 使用其它数据库
除了[illuminate/database](https://github.com/illuminate/database)，在webman中你可以使用其它数据库组件。比如ThinkPHP的 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)

## ThinkORM

### 安装ThinkORM

`composer require webman/think-orm`

> [webman/think-orm](https://www.workerman.net/plugin/14) 实际上是一个自动化安装`toptink/think-orm` 的插件，如果你的webman版本低于`1.2`无法使用插件请参考文章[手动安装并配置think-orm](https://www.workerman.net/a/1289)。

### 配置文件
根据实际情况修改配置文件 `config/thinkorm.php`

### 使用

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class Foo
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### ThinkOrm文档

更多请参考[topthink/think-orm文档](https://www.kancloud.cn/manual/think-orm/1257998)

  
## ThinkCache

安装ThinkCache  
`composer require webman/think-cache`

> [webman/think-cache](https://www.workerman.net/plugin/15) 实际上是一个自动化安装`toptink/think-cache` 的插件。
  
### 配置文件

配置文件为 `config/thinkcache.php`

### 使用

  ```php
  <?php
  namespace app\controller;
    
  use support\Request;
  use think\facade\Cache;
  
  class User
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

## Medoo数据库插件

参考 
