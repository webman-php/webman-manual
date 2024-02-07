# 設定檔案

## 位置
webman的配置檔案位於`config/`目錄下，專案中可以通過`config()`函數來獲取相應的設定。

## 獲取設定

獲取所有設定
```php
config();
```

獲取`config/app.php`裡的所有設定
```php
config('app');
```

獲取`config/app.php`裡的`debug`設定
```php
config('app.debug');
```

如果設定是陣列，可以通過`.`來獲取陣列內部元素的值，例如
```php
config('file.key1.key2');
```

## 默認值
```php
config($key, $default);
```
config透過第二個參數傳遞默認值，如果設定不存在則返回默認值。
設定不存在且沒有設置默認值則返回null。

## 自定義設定
開發者可以在`config/`目錄下添加自己的設定檔案，例如

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**使用時獲取設定**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## 更改設定
webman不支持動態修改設定，所有設定必須手動修改對應的設定檔案，並reload或restart重啟

> **注意**
> 伺服器設定`config/server.php`以及處理程序設定`config/process.php`不支持reload，需要restart重啟才能生效
