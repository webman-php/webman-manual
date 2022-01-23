# 使用其它数据库
除了[illuminate/database](https://github.com/illuminate/database)，在webman中你可以使用其它数据库组件。比如ThinkPHP的 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)

## ThinkORM

- 安装ThinkORM  
  `composer require topthink/think-orm`
  
- 配置文件 `config/thinkorm.php` 内容如下：
    ```php
  <?php
  return [
      'default'    =>    'mysql',
      'connections'    =>    [
          'mysql'    =>    [
              // 数据库类型
              'type'        => 'mysql',
              // 服务器地址
              'hostname'    => '127.0.0.1',
              // 数据库名
              'database'    => 'test',
              // 数据库用户名
              'username'    => 'root',
              // 数据库密码
              'password'    => '',
              // 数据库连接端口
              'hostport'    => '3306',
              // 数据库连接参数
              'params'      => [],
              // 数据库编码默认采用utf8
              'charset'     => 'utf8',
              // 数据库表前缀
              'prefix'      => '',
              // 断线重连
              'break_reconnect' => true,
              // 关闭SQL监听日志
              'trigger_sql' => false,
          ],
      ],
  ];
    ```
- 建立数据库初始化文件
  
  新建`support/bootstrap/ThinkOrm.php`，内容如下：
  
  ```php
  <?php
  namespace support\bootstrap;
  
  use Webman\Bootstrap;
  use Workerman\Timer;
  use think\facade\Db;
  
  class ThinkOrm implements Bootstrap
  {
      // 进程启动时调用
      public static function start($worker)
      {
          // 配置
          Db::setConfig(config('thinkorm'));
          // 维持mysql心跳
          if ($worker) {
              Timer::add(55, function () {
                  $connections = config('thinkorm.connections', []);
                  foreach ($connections as $key => $item) {
                      if ($item['type'] == 'mysql') {
                          Db::connect($key)->query('select 1');
                      }
                  }
              });
          }
      }
  }

  ```

- 进程启动配置

  打开`config/bootstrap.php`，加入如下配置：
  ```php
  return [
      // 这里省略了其它配置 ...
      support\bootstrap\ThinkOrm::class,
  ];
  ```
  > 进程启动时会执行一遍`config/bootstrap.php`里配置的类的start方法。我们利用start方法来初始化ThinkORM的配置，业务就直接可以使用ThinkORM了。

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
- ThinkORM使用文档

  [ThinkORM文档地址](https://www.kancloud.cn/manual/think-orm/1257998)
  
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