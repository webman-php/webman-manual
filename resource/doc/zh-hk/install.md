# 環境要求

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. 創建項目

```php
composer create-project workerman/webman
```

### 2. 運行

進入webman目錄   

#### Windows用戶
雙擊 `windows.bat` 或者運行 `php windows.php` 啟動

> **提示**
> 如果有錯誤，很可能是有函數被禁用，參考[函數禁用檢查](others/disable-function-check.md)解除禁用

#### Linux用戶
`debug`方式運行（用於開發調試）
 
```php
php start.php start
```

`daemon`方式運行（用於正式環境）

```php
php start.php start -d
```

> **提示**
> 如果有錯誤，很可能是有函數被禁用，參考[函數禁用檢查](others/disable-function-check.md)解除禁用

### 3.訪問

瀏覽器訪問 `http://ip地址:8787`
