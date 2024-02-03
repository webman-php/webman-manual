# 設定檔

插件的配置與普通 webman 專案相同，但通常插件的設定僅對當前插件有效，對主專案通常不會產生影響。
例如 `plugin.foo.app.controller_suffix` 的值僅影響插件的控制器後綴，對主專案無影響。
例如 `plugin.foo.app.controller_reuse` 的值僅影響插件是否重複使用控制器，對主專案無影響。
例如 `plugin.foo.middleware` 的值僅影響插件的中間件，對主專案無影響。
例如 `plugin.foo.view` 的值僅影響插件所使用的視圖，對主專案無影響。
例如 `plugin.foo.container` 的值僅影響插件所使用的容器，對主專案無影響。
例如 `plugin.foo.exception` 的值僅影響插件的異常處理類別，對主專案無影響。

然而因為路由是全域性的，所以插件配置的路由也會對全域產生影響。

## 取得設定
取得某個插件的設定方法為 `config('plugin.{插件}.{具體的配置}');`，例如取得 `plugin/foo/config/app.php` 的所有配置方法為 `config('plugin.foo.app')`。
同樣地，主專案或其他插件也可以使用 `config('plugin.foo.xxx')` 來取得 foo 插件的配置。

## 不支援的設定
應用插件不支援 `server.php`，`session.php` 配置，不支援 `app.request_class`，`app.public_path`，`app.runtime_path` 配置。