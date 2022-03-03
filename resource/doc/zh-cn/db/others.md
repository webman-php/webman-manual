# 使用其它数据库
除了[illuminate/database](https://github.com/illuminate/database)，在webman中你可以使用其它数据库组件。比如ThinkPHP的 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)

## ThinkORM

- 安装ThinkORM

  `composer require webman/think-orm`

  > [webman/think-orm](https://www.workerman.net/plugin/14) 实际上是一个自动化安装`toptink/think-orm` 的插件，如果你的webman版本低于`1.2`无法使用插件请参考文章[手动安装并配置think-orm](https://www.workerman.net/a/1289)。

- 根据实际情况修改配置文件 `config/thinkorm.php` 

- 使用

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
  
  更多请参考[topthink/think-orm文档](https://www.kancloud.cn/manual/think-orm/1257998)

  
## ThinkCache

- 安装ThinkCache  
  `composer require topthink/think-cache`
  
- 配置文件 `config/thinkcache.php` 内容如下：

  ```php
  <?php
  return [
      'default'	=>	'file',
      'stores'	=>	[
          'file'	=>	[
              'type'   => 'File',
              // 缓存保存目录
              'path'   => runtime_path().'/cache/',
              // 缓存前缀
              'prefix' => '',
              // 缓存有效期 0表示永久缓存
              'expire' => 0,
          ],
          'redis'	=>	[
              'type'   => 'redis',
              'host'   => '127.0.0.1',
              'port'   => 6379,
              'prefix' => '',
              'expire' => 0,
          ],
      ],
  ];
  ```
- 建立ThinkCache初始化文件
  
  新建`support/bootstrap/ThinkCache.php`，内容如下：
  
  ```php
  <?php
  namespace support\bootstrap;
  
  use Webman\Bootstrap;  
  use think\facade\Cache;
  
  class ThinkCache implements Bootstrap
  {
      // 进程启动时调用
      public static function start($worker)
      {
          // 配置
          Cache::config(config('thinkcache'));
      }
  }

  ```

- 进程启动配置

  打开`config/bootstrap.php`，加入如下配置：
  ```php
  return [
      // 这里省略了其它配置 ...
      support\bootstrap\ThinkCache::class,
  ];
  ```

- 使用

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
Think-Cache使用文档

  [ThinkCache文档地址](https://github.com/top-think/think-cache)