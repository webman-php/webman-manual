# 程式設計需知

## 作業系統
webman同時支援在Linux系統和Windows系統運行。但由於workerman在Windows下無法支援多進程設定以及守護進程，因此建議僅將Windows系統用於開發環境和調試，正式環境應使用Linux系統。

## 啟動方式
**在Linux系統**中使用命令 `php start.php start`(debug調試模式) 或 `php start.php start -d`(守護進程模式) 來啟動。
**在Windows系統**中執行`windows.bat`或者使用命令 `php windows.php` 來啟動，按下ctrl c 停止。Windows系統不支援stop、reload、status、reload connections等命令。

## 常駐記憶體
webman是常駐記憶體的框架，一般來說，PHP檔案載入記憶體後便會被復用，不會再次從硬碟讀取(模版檔案除外)。因此在正式環境當中，業務代碼或配置變更後需要執行`php start.php reload`才能生效。若是更改進程相關配置或安裝新的composer包需要重新啟動`php start.php restart`。

> 為了方便開發，webman自帶一個monitor自定義進程用於監控業務檔案更新，當有業務檔案更新時會自動執行reload。此功能僅在workerman以debug方式運行時(啟動時不加`-d`)才啟用。Windows用戶需要執行`windows.bat`或者`php windows.php`才能啟用。

## 關於輸出語句
在傳統的php-fpm專案中，使用`echo`、`var_dump`等函數輸出資料會直接顯示在頁面上，而在webman中，這些輸出往往顯示在終端上，並不會顯示在頁面中(模版檔案中的輸出除外)。

## 不要執行`exit` `die`語句
執行die或者exit會導致進程退出並重啟，使得目前的請求無法被正確響應。

## 不要執行`pcntl_fork`函數
在webman中不允許使用`pcntl_fork`函數來創造新的進程。
