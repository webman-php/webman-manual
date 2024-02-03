# 目錄結構
```
.
├── app                           應用目錄
│   ├── controller                控制器目錄
│   ├── model                     模型目錄
│   ├── view                      視圖目錄
│   ├── middleware                中間件目錄
│   │   └── StaticFile.php        自帶靜態文件中間件
|   └── functions.php             業務自定義函數寫到這個文件裡
|
├── config                        配置目錄
│   ├── app.php                   應用配置
│   ├── autoload.php              這裡配置的文件會被自動加載
│   ├── bootstrap.php             進程啟動時onWorkerStart時運行的回調配置
│   ├── container.php             容器配置
│   ├── dependence.php            容器依賴配置
│   ├── database.php              數據庫配置
│   ├── exception.php             異常配置
│   ├── log.php                   日誌配置
│   ├── middleware.php            中間件配置
│   ├── process.php               自定義進程配置
│   ├── redis.php                 redis配置
│   ├── route.php                 路由配置
│   ├── server.php                端口、進程數等伺服器配置
│   ├── view.php                  視圖配置
│   ├── static.php                靜態文件開關及靜態文件中間件配置
│   ├── translation.php           多語言配置
│   └── session.php               session配置
├── public                        靜態資源目錄
├── process                       自定義進程目錄
├── runtime                       應用的運行時目錄，需要可寫權限
├── start.php                     服務啟動文件
├── vendor                        composer安裝的第三方類庫目錄
└── support                       類庫適配(包括第三方類庫)
    ├── Request.php               請求類
    ├── Response.php              響應類
    ├── Plugin.php                插件安裝卸載腳本
    ├── helpers.php               助手函數(業務自定義函數請寫到app/functions.php)
    └── bootstrap.php             進程啟動後初始化腳本
```