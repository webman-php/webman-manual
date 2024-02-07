# 打包

例如打包foo應用程式插件

* 在`plugin/foo/config/app.php`設定版本號(**重要**)
* 刪除`plugin/foo`中不需打包的文件，特別是`plugin/foo/public`下用於測試上傳功能的臨時文件
* 刪除資料庫、Redis配置，如果您的項目有自己獨立的資料庫、Redis配置，這些配置應該在首次訪問應用程式時觸發安裝引導程序(需要自行實現)，讓管理員手動填寫並生成。
* 恢復其他需要還原原貌的文件
* 完成以上操作後進入`{主項目}/plugin/`目錄，使用命令 `zip -r foo.zip foo` 生成foo.zip
