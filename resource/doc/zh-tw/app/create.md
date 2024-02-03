# 建立應用程式插件

## 唯一標識

每個插件都有一個獨特的應用程式標識，開發者在開發之前需要想好標識並確認該標識尚未被佔用。
檢查網址 [應用程式標識檢查](https://www.workerman.net/app/check)

## 建立

執行 `composer require webman/console` 安裝 webman 命令列。

使用指令 `php webman app-plugin:create {插件標識}` 可以在本地建立一個應用程式插件。

例如 `php webman app-plugin:create foo`

重新啟動 webman。

訪問 `http://127.0.0.1:8787/app/foo`，若有回傳內容則表示建立成功。