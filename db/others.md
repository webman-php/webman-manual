# 使用其它数据库
除了[illuminate/database](https://github.com/illuminate/database)，在webman中你可以使用其它数据库组件。比如 [ThinkORM](https://www.kancloud.cn/manual/think-orm/1257998)

## ThinkORM

- 安装ThinkORM  
  `composer require topthink/think-orm`
  
- 新建配置文件 `config/thinkorm.php` 内容如下：
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
  
  新建`support/bootstrap/db/Thinkphp.php`，内容如下：
  
  ```php
  <?php
  namespace support\bootstrap\db;
    
  use Webman\Bootstrap;
  use think\facade\Db;
    
  class Thinkphp implements Bootstrap
  {
      // 进程启动时调用
      public static function start($worker)
      {
            Db::setConfig(config('thinkorm'));
      }
  }
  ```

- 进程启动配置

  打开`config/bootstrap.php`，加入如下配置：
  ```php
  return [
      // 这里省略了其它配置 ...
      support\bootstrap\db\Thinkphp::class,
  ];
  ```
  > 进程启动时会执行一遍`config/bootstrap.php`里配置的所有类的start方法。我们利用start方法来初始化ThinkORM的配置，业务就直接可以使用ThinkORM了。

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