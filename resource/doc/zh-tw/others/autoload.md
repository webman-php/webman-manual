# 自動加載

## 利用composer載入PSR-0規範的文件
webman遵循`PSR-4`自動加載規範。如果你的業務需要加載`PSR-0`規範的程式庫，請參考以下操作。

- 新建 `extend` 目錄用於存放`PSR-0`規範的程式庫
- 編輯`composer.json`，在`autoload`下增加以下內容

```js
"psr-0" : {
    "": "extend/"
}
```
最終結果類似
![](../../assets/img/psr0.png)

- 執行 `composer dumpautoload`
- 執行 `php start.php restart` 重啟webman (注意，必須重啟才能生效) 

## 利用composer載入某些文件

- 編輯`composer.json`，在`autoload.files`下添加要載入的文件
```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- 執行 `composer dumpautoload`
- 執行 `php start.php restart` 重啟webman (注意，必須重啟才能生效) 

> **提示**
> composer.json裡`autoload.files`配置的文件在webman啟動前就會載入。而利用框架`config/autoload.php`載入的文件是在webman啟動後才載入的。
> composer.json裡`autoload.files`載入的文件更改後必須restart才能生效，reload不生效。而利用框架`config/autoload.php`載入的文件支持熱載入，更改後reload即可生效。

## 利用框架載入某些文件
有些文件可能不符合SPR規範，無法自動載入，我們可以通過配置`config/autoload.php`載入這些文件，例如：

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```

 > **提示**
 > 我們看到`autoload.php`裡設置了載入 `support/Request.php` `support/Response.php`兩個文件，這是因為在`vendor/workerman/webman-framework/src/support/`下也有兩個相同的文件，我們通過`autoload.php`優先載入項目根目錄下的`support/Request.php` `support/Response.php`，這樣允許我們可以定製這兩個文件的內容而不需要修改`vendor`中的文件。如果你不需要定製它們，則可以忽略這兩個配置。
