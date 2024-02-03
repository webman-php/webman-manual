# 程式設計注意事項

## 作業系統
webman 同時支援 Linux 和 Windows 作業系統。但由於 Workerman 無法在 Windows 上支援多進程設定和守護進程，因此建議僅在開發環境下使用 Windows 進行開發和除錯，正式環境請使用 Linux 作業系統。

## 啟動方式
**在 Linux 系統** 中，使用命令 `php start.php start`（調試模式）或 `php start.php start -d`（守護進程模式）來啟動。  
**在 Windows 系統** 中，執行 `windows.bat` 或使用命令 `php windows.php` 來啟動，按下 Ctrl + C 來停止。Windows 系統不支援 stop、reload、status、reload connections 等指令。

## 常駐記憶體
webman 是一個常駐記憶體的框架，一般來說，PHP 檔案載入記憶體後會被複用，不會再次從磁碟讀取（模板檔案除外）。因此，在正式環境中，當商業程式碼或配置變更後，需要執行 `php start.php reload` 才能生效。如需更改進程相關配置或安裝新的 Composer 套件，需要重新啟動 `php start.php restart`。

> 為了方便開發，webman 預設附帶一個監視自定義進程，用於監控商業檔案更新。當有商業檔案更新時，會自動執行 reload。此功能僅在 Workerman 以調試方式運行（啟動時不加 `-d`）時啟用。Windows 使用者需要執行 `windows.bat` 或者 `php windows.php` 才能使用此功能。

## 關於輸出語句
在傳統的 PHP-FPM 專案中，使用 `echo`、`var_dump` 等函數輸出資料會直接顯示在頁面上，而在 webman 中，這些輸出通常會顯示在終端機上，並不會顯示在頁面中（模板檔案中的輸出除外）。

## 請勿執行 `exit`、`die` 語句
執行 die 或者 exit 會導致進程退出並重啟，導致目前的請求無法得到正確回應。

## 請勿執行 `pcntl_fork` 函數
在 webman 中，不允許使用 `pcntl_fork` 創建新的進程。