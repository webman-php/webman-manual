# 設定檔案

## 位置
webman的配置檔案位於`config/`目錄下，項目中可以通過`config()`函數來獲取對應的配置。

## 獲取配置

獲取所有配置
```php
config();
```

獲取`config/app.php`裡的所有配置
```php
config('app');
```

獲取`config/app.php`裡的`debug`配置
```php
config('app.debug');
```

如果配置是陣列，可以通過`.`來獲取陣列內部元素的值，例如
```php
config('file.key1.key2');
```

## 預設值
```php
config($key, $default);
```
config透過第二個參數傳遞預設值，如果配置不存在則返回預設值。
配置不存在且沒有設置預設值則返回null。


## 自定義配置
開發者可以在`config/`目錄下添加自己的配置檔案，例如

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**獲取配置時使用**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## 更改配置
webman不支持動態修改配置，所有配置必須手動修改對應的配置檔案，並且重新載入或重新啟動

> **注意**
> 伺服器配置`config/server.php`以及進程配置`config/process.php`不支持重新載入，需要重新啟動才能生效
