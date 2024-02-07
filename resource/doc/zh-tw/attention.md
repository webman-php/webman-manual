# 程式設計必知

## 作業系統
webman同時支援Linux系統和Windows系統下的執行。但由於workerman在Windows下無法支援多進程設置以及守護進程，因此建議僅將Windows系統用於開發環境開發調試使用，正式環境請使用Linux系統。

## 啟動方式
**Linux系統**使用指令 `php start.php start` (debug調試模式) `php start.php start -d` (守護進程模式) 啟動。
**Windows系統**執行`windows.bat`或者使用指令 `php windows.php`啟動，按下ctrl c停止。Windows系統不支援stop reload status reload connections等命令。

## 常駐記憶體
webman是常駐記憶體的框架，一般來說，PHP檔案載入記憶體後便會被複用，不會再次從硬碟讀取（模版檔案除外）。所以在正式環境中，業務代碼或配置變更後需要執行`php start.php reload`才能生效。如果是更改進程相關配置或者安裝了新的composer包需要重啟`php start.php restart`。

> 為了方便開發，webman自帶一個monitor自定義進程用於監控業務檔案更新，當有業務檔案更新時會自動執行reload。此功能僅在workerman以debug方式運行（啟動時不加`-d`）時啟用。Windows使用者需要執行`windows.bat`或者`php windows.php`才能啟用。

## 關於輸出語句
在傳統的php-fpm項目中，使用`echo` `var_dump`等函數輸出數據會直接顯示在頁面上，而在webman中，這些輸出往往顯示在終端上，並不會顯示在頁面中（模版檔案中的輸出除外）。

## 不要執行`exit` `die`語句
執行die或者exit會使得進程退出並重啟，導致當前請求無法被正確響應。

## 不要執行`pcntl_fork`函數
`pcntl_fork`用戶創建一個進程，這在webman中是不允許的。
