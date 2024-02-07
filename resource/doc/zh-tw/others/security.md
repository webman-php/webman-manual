# 安全

## 執行使用者
建議將執行使用者設置為權限較低的使用者，例如與nginx執行使用者一致。在 `config/server.php` 中的`user`和`group`中設置執行使用者。類似地，自訂進程的使用者是透過`config/process.php`中的`user`和`group`來指定。需要注意的是，監控進程不要設置執行使用者，因為它需要高權限才能正常運作。

## 控制器規範
`controller`目錄或者子目錄下只能放置控制器檔案，禁止放置其他類別檔案，否則在未啟用[控制器後綴](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)時，類別檔案有可能會被 URL 非法訪問，造成不可預知的後果。例如 `app/controller/model/User.php` 實際上是 Model 類別，但是卻錯誤地放到了 `controller` 目錄下，在沒有啟用[控制器後綴](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)時，會導致用戶可以通過類似 `/model/user/xxx` 訪問 `User.php` 裡的任意方法。為了徹底杜絕這種情況，強烈建議使用[控制器後綴](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80)明確標記哪些是控制器檔案。

## XSS過濾
考慮通用性，webman沒有對請求進行XSS轉譯。webman強烈推薦在渲染時進行XSS轉譯，而不是在入庫前進行轉譯。並且twig、blade、think-template等模板會自動執行XSS轉譯，無需手動轉譯，非常方便。

> **提示** 如果你在入庫前進行XSS轉譯，很可能造成一些應用插件的不兼容問題

## 防止SQL注入
為了防止SQL注入，請儘量使用ORM，如 [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html)、[think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)，使用時請儘量不要自己組裝SQL。

## nginx代理
當你的應用需要暴露給外網使用者時，強烈建議在webman前增加一個nginx代理，這樣可以過濾一些非法HTTP請求，提高安全性。具體請參考[nginx代理](nginx-proxy.md)
