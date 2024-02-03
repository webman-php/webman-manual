# 自動加載

## 利用 Composer 加載符合 PSR-0 規範的檔案
webman 遵循 `PSR-4` 自動加載規範。如果您的業務需要加載 `PSR-0` 規範的程式庫，請參考以下操作。

- 新建 `extend` 目錄用於存放 `PSR-0` 規範的程式庫。
- 編輯 `composer.json`，在 `autoload` 下增加以下內容

```js
"psr-0" : {
    "": "extend/"
}
```
最終結果類似
![](../../assets/img/psr0.png)

- 執行 `composer dumpautoload`
- 執行 `php start.php restart` 重啟 webman（注意，必須重啟才能生效）

## 利用 Composer 加載某些檔案

- 編輯 `composer.json`，在 `autoload.files` 下添加要加載的檔案
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- 執行 `composer dumpautoload`
- 執行 `php start.php restart` 重啟 webman（注意，必須重啟才能生效）

> **提示**
> composer.json 裡 `autoload.files` 配置的檔案在 webman 啟動前就會加載。而利用框架 `config/autoload.php` 加載的檔案是在 webman 啟動後才加載的。
> composer.json 裡 `autoload.files` 加載的檔案更改後必須 restart 才能生效，reload 不生效。而利用框架 `config/autoload.php` 加載的檔案支持熱加載，更改後 reload 即可生效。

## 利用框架加載某些檔案
有些檔案可能不符合 SPR 規範，無法自動加載，我們可以通過配置 `config/autoload.php` 加載這些檔案，例如：
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
> 我們看到 `autoload.php` 裡設置了加載 `support/Request.php` 和 `support/Response.php` 兩個檔案，這是因為在 `vendor/workerman/webman-framework/src/support/` 下也有兩個相同的檔案，我們通過 `autoload.php` 優先加載專案根目錄下的 `support/Request.php` 和 `support/Response.php`，這樣允許我們可以定製這兩個檔案的內容而不需要修改 `vendor` 中的檔案。如果您不需要定製它們，則可以忽略這兩個配置。