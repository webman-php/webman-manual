# 創建應用程式插件

## 唯一標識

每個插件都有一個獨特的應用標識，開發者在開發之前需要選擇一個標識，並檢查該標識是否已被使用。可到以下地址檢查[應用標識檢查](https://www.workerman.net/app/check)

## 創建

執行`composer require webman/console` 安裝webman命令列

使用命令`php webman app-plugin:create {插件標識}`可以在本地創建一個應用程式插件

例如 `php webman app-plugin:create foo`

重啟webman

訪問 `http://127.0.0.1:8787/app/foo` 如果有返回內容則說明創建成功。
