# 應用程式插件開發規範

## 應用程式插件要求
* 插件不能包含侵權的程式碼、圖示、圖片等
* 插件源碼需保證是完整的代碼，並且不能加密
* 插件必須具備完整的功能，不能只有簡單的功能
* 必須提供完整的功能介紹、文件
* 插件不能包含子市場
* 插件內不能包含任何文字或推廣連結

## 應用程式插件標識
每個應用程式插件都有一個唯一標識，由字母組成。這個標識會影響應用程式插件所在的源碼目錄名稱、類的命名空間以及插件資料庫表前綴。

假設開發者以foo為插件標識，那麼插件的源碼目錄就是`{主項目}/plugin/foo`，相應的插件命名空間為`plugin\foo`，資料表前綴為`foo_`。

由於標識在整個網路上是唯一的，開發者在開發前需要檢測標識是否可用，檢測網址[應用標識檢測](https://www.workerman.net/app/check)。

## 資料庫
* 表名由小寫字母`a-z`以及底線`_`組成
* 插件資料表應以插件標識為前綴，例如foo插件的article表為`foo_article`
* 表主鍵應該為id索引
* 儲存引擎統一使用innodb引擎
* 字元集統一使用utf8mb4_general_ci
* 資料庫ORM使用laravel或者think-orm都可以
* 時間欄位建議使用DateTime

## 代碼規範

#### PSR規範
代碼應符合PSR4載入規範

#### 類別命名以大寫開頭的駝峰式
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### 類別屬性及方法以小寫開頭的駝峰式
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * 不需要認證的方法
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * 獲取評論
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### 註釋
類別屬性以及函式必須包含註釋，包括概述、參數、返回類型

#### 縮排
代碼應該使用4個空格符來縮排，而不是使用定位符

#### 流程控制
流程控制關鍵字(if for while foreach等)後面緊跟一個空格，流程控制程式碼開始花括號應該與結束圓括號在同一行。
```php
foreach ($users as $uid => $user) {

}
```

#### 臨時變數名稱
建議以小寫開頭的駝峰式命名 (不強制)

```php
$articleCount = 100;
```
