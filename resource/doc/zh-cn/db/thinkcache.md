# think-cache

think-cache是从thinkphp框架抽离出的一个组件，并添加了连接池功能，自动支持协程和非协程环境。

> **注意**
> 当前手册为 webman v2 版本，如果您使用的是webman v1版本，请查看 [v1版本手册](/doc/webman-v1/db/thinkcache.html)

## 安装
`composer require -W webman/think-cache`

安装后需要restart重启(reload无效)

###配置文件

配置文件为 `config/think-cache.php`

### 使用

  ```php
  <?php
  namespace app\controller;
    
  use support\Request;
  use support\think\Cache;
  
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
## 提供的接口
```php
// 设置缓存
Cache::set('val','value',600);
// 判断缓存是否设置
Cache::has('val');
// 获取缓存
Cache::get('val');
// 删除缓存
Cache::delete('val');
// 清除缓存
Cache::clear();
// 读取并删除缓存
Cache::pull('val');
// 不存在则写入
Cache::remember('val',10);

// 对于数值类型的缓存数据可以使用
// 缓存增+1
Cache::inc('val');
// 缓存增+5
Cache::inc('val',5);
// 缓存减1
Cache::dec('val');
// 缓存减5
Cache::dec('val',5);

// 使用缓存标签
Cache::tag('tag_name')->set('val','value',600);
// 删除某个标签下的缓存数据
Cache::tag('tag_name')->clear();
// 支持指定多个标签
Cache::tag(['tag1','tag2'])->set('val2','value',600);
// 删除多个标签下的缓存数据
Cache::tag(['tag1','tag2'])->clear();

// 使用多种缓存类型
$redis = Cache::store('redis');

$redis->set('var','value',600);
$redis->get('var');
```


