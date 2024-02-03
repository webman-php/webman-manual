# 設定檔

## 位置
webman的設定檔位於`config/`目錄下，專案中可以透過`config()`函數來獲取相應的設定。

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

若設定是陣列，可以透過`.`來獲取陣列內部元素的值，例如
```php
config('file.key1.key2');
```

## 默認值
```php
config($key, $default);
```
透過第二個參數傳遞默認值，若設定不存在則返回默認值。
若設定不存在且未設置默認值則返回null。

## 自定義設定
開發者可以在`config/`目錄下新增自己的設定檔，例如

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
config('payment.secret');
```

## 更改設定
webman不支持動態修改設定，所有設定必須手動修改相應的設定檔，並重新載入或重新啟動。

> **注意**
> 伺服器設定`config/server.php`以及進程設定`config/process.php`不支持重新載入，需要重新啟動才能生效。