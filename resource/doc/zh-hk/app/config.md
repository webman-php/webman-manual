# 設定檔

插件的設定和普通的webman專案一樣，但通常插件的設定只對當前插件有效，對主專案通常無影響。
例如 `plugin.foo.app.controller_suffix` 的值只影響插件的控制器後綴，對主專案沒有影響。
例如 `plugin.foo.app.controller_reuse` 的值只影響插件是否重用控制器，對主專案沒有影響。
例如 `plugin.foo.middleware` 的值只影響插件的中間件，對主專案沒有影響。
例如 `plugin.foo.view` 的值只影響插件使用的視圖，對主專案沒有影響。
例如 `plugin.foo.container` 的值只影響插件使用的容器，對主專案沒有影響。
例如 `plugin.foo.exception` 的值只影響插件的異常處理類，對主專案沒有影響。

但是因為路由是全域的，所以插件設定的路由也是影響全域的。

## 獲取設定
取得某個插件設定的方式是 `config('plugin.{插件}.{具體的設定}');`，例如取得 `plugin/foo/config/app.php` 的所有設定方法為 `config('plugin.foo.app')`
同樣地，主專案或其他插件都可以使用 `config('plugin.foo.xxx')` 來取得 foo 插件的設定。

## 不支援的設定
應用插件不支援 server.php、session.php的設定，也不支援 `app.request_class`、`app.public_path`、`app.runtime_path` 的設定。
