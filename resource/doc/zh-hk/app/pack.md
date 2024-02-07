# 打包

例如打包foo應用插件

* 設置`plugin/foo/config/app.php`裡版本號(**重要**)
* 刪除`plugin/foo`裡不需要打包的文件，尤其是`plugin/foo/public`下測試上傳功能的臨時文件
* 刪除資料庫、Redis配置，如果你的項目有自己獨立的資料庫、Redis配置，這些配置應該是在首次訪問應用時觸發安裝引導程序(需要自行實現)，讓管理員手動填寫並生成。
* 恢復其他需要恢復原貌的文件
* 完成以上操作後進入`{主項目}/plugin/`目錄，使用命令 `zip -r foo.zip foo` 生成foo.zip
