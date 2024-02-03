# 設定檔

插件的設定和一般的 webman 專案一樣，不過插件的設定通常僅對當前插件有效，對主專案通常沒有影響。
例如 `plugin.foo.app.controller_suffix` 的值僅影響插件的控制器後綴，對主專案沒有影響。
例如 `plugin.foo.app.controller_reuse` 的值僅影響插件是否複用控制器，對主專案沒有影響。
例如 `plugin.foo.middleware` 的值僅影響插件的中介軟體，對主專案沒有影響。
例如 `plugin.foo.view` 的值僅影響插件所使用的視圖，對主專案沒有影響。
例如 `plugin.foo.container` 的值僅影響插件所使用的容器，對主專案沒有影響。
例如 `plugin.foo.exception` 的值僅影響插件的異常處理類，對主專案沒有影響。

不過由於路由是全域的，因此插件配置的路由也會全域影響。

## 獲取設定
獲取某個插件設定的方法是 `config('plugin.{插件}.{具體的設定}');`，比如獲取 `plugin/foo/config/app.php` 的所有設定的方法是 `config('plugin.foo.app')`
同樣的，主專案或其他插件也可以用 `config('plugin.foo.xxx')` 來獲取 foo 插件的設定。

## 不支援的設定
應用插件不支援 server.php、session.php 設定，也不支援 `app.request_class`、`app.public_path`、`app.runtime_path` 設定。