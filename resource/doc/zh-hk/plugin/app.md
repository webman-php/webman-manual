# 應用插件
每個應用插件都是一個完整的應用程式，其原始碼放置於`{主專案}/plugin`目錄下

> **提示**
> 使用指令`php webman app-plugin:create {插件名}` (需要webman/console>=1.2.16) 可在本地建立一個應用插件，
> 例如 `php webman app-plugin:create cms` 將建立如下目錄結構

```
plugin/
└── cms
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    └── public
```

我們可以看到每個應用插件都有著與webman相同的目錄結構和配置文件。事實上，開發一個應用插件與開發一個webman專案的體驗基本一致，只需要注意以下幾個方面。

## 命名空間
插件目錄及命名遵循PSR4規範，插件都放置於plugin目錄下，因此命名空間都以plugin開頭，例如`plugin\cms\app\controller\UserController`，這裡cms是插件的原始碼主目錄。

## URL訪問
應用插件的URL地址路徑都以`/app`開頭，例如`plugin\cms\app\controller\UserController`的URL地址是 `http://127.0.0.1:8787/app/cms/user`。

## 靜態檔案
靜態檔案放置於`plugin/{插件}/public`下，例如訪問`http://127.0.0.1:8787/app/cms/avatar.png`實際上是獲取`plugin/cms/public/avatar.png`檔案。

## 配置文件
插件的配置與普通webman專案一樣，不過插件的配置一般只對當前插件有效，對主專案一般無影響。
例如`plugin.cms.app.controller_suffix`的值只影響插件的控制器後綴，對主專案沒有影響。
例如`plugin.cms.app.controller_reuse`的值只影響插件是否複用控制器，對主專案沒有影響。
例如`plugin.cms.middleware`的值只影響插件的中間件，對主專案沒有影響。
例如`plugin.cms.view`的值只影響插件所使用的視圖，對主專案沒有影響。
例如`plugin.cms.container`的值只影響插件所使用的容器，對主專案沒有影響。
例如`plugin.cms.exception`的值只影響插件的異常處理類，對主專案沒有影響。

但由於路由是全局的，因此插件配置的路由也會影響全局。

## 獲取配置
獲取某個插件配置的方法為 `config('plugin.{插件}.{具體的配置}');`，例如獲取`plugin/cms/config/app.php`的所有配置方法為`config('plugin.cms.app')`。
同樣的，主專案或其他插件都可以用`config('plugin.cms.xxx')`來獲取cms插件的配置。

## 不支持的配置
應用插件不支持server.php，session.php配置，不支持`app.request_class`，`app.public_path`，`app.runtime_path`配置。

## 數據庫
插件可以配置自己的數據庫，例如`plugin/cms/config/database.php`內容如下
```php
return  [
    'default' => 'mysql',
    'connections' => [
        'mysql' => [ // mysql為連接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '數據庫',
            'username'    => '用戶名',
            'password'    => '密碼',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
        'admin' => [ // admin為連接名
            'driver'      => 'mysql',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'database'    => '數據庫',
            'username'    => '用戶名',
            'password'    => '密碼',
            'charset'     => 'utf8mb4',
            'collation'   => 'utf8mb4_general_ci',
        ],
    ],
];
```
引用方式為`Db::connection('plugin.{插件}.{連接名}');`，例如
```php
use support\Db;
Db::connection('plugin.cms.mysql')->table('user')->first();
Db::connection('plugin.cms.admin')->table('admin')->first();
```

如果想使用主專案的數據庫，則直接使用即可，例如
```php
use support\Db;
Db::table('user')->first();
// 假設主專案還配置了一個admin連接
Db::connection('admin')->table('admin')->first();
```

> **提示**
> thinkorm也是類似的用法

## Redis
Redis用法與數據庫類似，例如 `plugin/cms/config/redis.php`
```php
return [
    'default' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 0,
    ],
    'cache' => [
        'host' => '127.0.0.1',
        'password' => null,
        'port' => 6379,
        'database' => 1,
    ],
];
```
使用時
```php
use support\Redis;
Redis::connection('plugin.cms.default')->get('key');
Redis::connection('plugin.cms.cache')->get('key');
```

同樣的，如果想複用主專案的Redis配置
```php
use support\Redis;
Redis::get('key');
// 假設主專案還配置了一個cache連接
Redis::connection('cache')->get('key');
```

## 日誌
日誌類用法也與數據庫用法類似
```php
use support\Log;
Log::channel('plugin.admin.default')->info('test');
```

如果想複用主專案的日誌配置，直接使用
```php
use support\Log;
Log::info('日誌內容');
// 假設主專案有個test日誌配置
Log::channel('test')->info('日誌內容');
```

# 應用插件安裝與卸載
應用插件安裝時只需要將插件目錄複製到`{主專案}/plugin`目錄下即可，需要reload或restart才能生效。
卸載時直接刪除`{主專案}/plugin`下對應的插件目錄即可。