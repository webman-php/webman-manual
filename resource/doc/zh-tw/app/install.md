# 安裝

應用程式插件的安裝有兩種方式：

## 在插件市場安裝
進入 [官方管理後台webman-admin](https://www.workerman.net/plugin/82) 的應用程式插件頁面，點擊安裝按鈕以安裝相應的應用程式插件。

## 源碼包安裝
從應用市場下載應用程式插件壓縮包，解壓縮並將解壓縮的目錄上傳至`{主項目}/plugin/`目錄下（如果plugin目錄不存在，需要手動創建），執行 `php webman app-plugin:install 插件名` 完成安裝。

例如，下載的壓縮包名稱為ai.zip，解壓縮到 `{主項目}/plugin/ai`，執行 `php webman app-plugin:install ai` 完成安裝。

# 卸載

同樣，應用程式插件的卸載也有兩種方式：

## 在插件市場卸載
進入 [官方管理後台webman-admin](https://www.workerman.net/plugin/82) 的應用程式插件頁面，點擊卸載按鈕以卸載相應的應用程式插件。

## 源碼包安裝
執行 `php webman app-plugin:uninstall 插件名` 完成卸載，完成後手動刪除`{主項目}/plugin/`目錄下相應的插件目錄。
