# 打包

例如打包foo應用程式插件

* 在`plugin/foo/config/app.php`中設置版本號(**重要**)
* 刪除`plugin/foo`中不需要打包的文件，特別是`plugin/foo/public`下的測試上傳功能的臨時文件
* 刪除數據庫、Redis配置，如果您的項目有自己獨立的數據庫、Redis配置，這些配置應該在首次訪問應用程式時觸發安裝引導程式(需要自行實現)，讓管理員手動填寫並生成。
* 恢復其他需要恢復原貌的文件
* 完成以上操作後進入`{主項目}/plugin/`目錄，使用命令 `zip -r foo.zip foo` 生成foo.zip