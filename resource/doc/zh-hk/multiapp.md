# 多應用
有時一個項目可能分為多個子項目，例如一個商城可能分為商城主項目、商城API接口、商城管理後台3個子項目，他們都使用相同的資料庫配置。

webman允許你這樣規劃app目錄：
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
當訪問地址 `http://127.0.0.1:8787/shop/{控制器}/{方法}` 時訪問`app/shop/controller`下的控制器與方法。

當訪問地址 `http://127.0.0.1:8787/api/{控制器}/{方法}` 時訪問`app/api/controller`下的控制器與方法。

當訪問地址 `http://127.0.0.1:8787/admin/{控制器}/{方法}` 時訪問`app/admin/controller`下的控制器與方法。

在webman中，甚至可以這樣規劃app目錄。
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
這樣當地址訪問 `http://127.0.0.1:8787/{控制器}/{方法}` 時訪問的是`app/controller`下的控制器與方法。當路徑裡以api或者admin開頭時訪問的是相對應目錄裡的控制器與方法。

多應用時類的命名空間需符合`psr4`，例如`app/api/controller/FooController.php` 檔案類似如下：

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## 多應用中間件配置
有時候你想為不同應用配置不同的中間件，例如`api`應用可能需要一個跨域中間件，`admin`需要一個檢查管理員登錄的中間件，則配置`config/midlleware.php`可能類似下面這樣：
```php
return [
    // 全局中間件
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // api應用中間件
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // 應用中間件
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> 以上中間件可能並不存在，這裡僅僅是作為示例講述如何按應用配置中間件

中間件執行順序為 `全局中間件`->`應用中間件`。

中間件開發參考[中間件章節](middleware.md)

## 多應用異常處理配置
同樣的，你想為不同的應用配置不同的異常處理類，例如`shop`應用裡出現異常你可能想提供一個友好的提示頁面；`api`應用裡出現異常時你想返回的並不是一個頁面，而是一個JSON字符串。為不同應用配置不同的異常處理類的配置檔`config/exception.php`類似如下：
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> 不同於中間件，每個應用只能配置一個異常處理類。

> 以上異常處理類可能並不存在，這裡僅僅是作為示例講述如何按應用配置異常處理

異常處理開發參考[異常處理章節](exception.md)