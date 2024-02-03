# 編程須知

## 操作系統
webman同時支援linux系統和windows系統下運行。但是由於workerman在windows下無法支持多進程設置以及守護進程，因此windows系統僅建議用於開發環境開發調試使用，正式環境請使用linux系統。

## 啟動方式
**linux系統**用命令 `php start.php start`(debug調試模式) `php start.php start -d`(守護進程模式) 啟動
**windows系統**執行 `windows.bat`或者使用命令 `php windows.php` 啟動，按ctrl c 停止。windows系統不支持stop reload status reload connections等命令。

## 常駐記憶體
webman是常駐記憶體的框架，一般來說，php文件載入記憶體後便會被復用，不會再次從磁盤讀取(模版文件除外)。所以正式環境業務代碼或配置變更後需要執行`php start.php reload`才能生效。如果是更改進程相關配置或者安裝了新的composer包需要重啓`php start.php restart`。

> 為了方便開發，webman自帶一個monitor自定義進程用於監控業務文件更新，當有業務文件更新時會自動執行reload。此功能只在workerman以debug方式運行(啟動時不加`-d`)才啟用。windows用戶需要執行`windows.bat`或者`php windows.php`才能啟用。

## 關於輸出語句
在傳統php-fpm項目裡，使用`echo` `var_dump`等函數輸出數據會直接顯示在頁面裡，而在webman中，這些輸出往往顯示在終端上，並不會顯示在頁面中(模版文件中的輸出除外)。

## 不要執行`exit` `die`語句
執行die或者exit會使得進程退出並重啓，導致當前請求無法被正確響應。

## 不要執行`pcntl_fork`函數
`pcntl_fork`用戶創建一個進程，這在webman中是不允許的。