# 安全

## 執行使用者
建議將執行使用者設置為權限較低的使用者，例如與 nginx 執行使用者一致。在 `config/server.php` 中設置執行使用者的 `user` 和 `group`。類似地，自訂程序的使用者是透過 `config/process.php` 中的 `user` 和 `group` 指定的。需注意的是，監控程序不應設置執行使用者，因為它需要高權限才能正常運作。

## 控制器規範
`controller` 目錄或其子目錄只能放置控制器檔案，嚴禁放置其他類別檔案，否則在未啟用 [控制器後綴](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) 時，類別檔案可能會被非法訪問，導致無法預料的後果。例如 `app/controller/model/User.php` 實際上是模型類別，但卻錯誤地放置於 `controller` 目錄下，在未啟用 [控制器後綴](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) 時，使用者可以通過類似 `/model/user/xxx` 訪問 `User.php` 中的任意方法。為了徹底杜絕這種情況，強烈建議使用 [控制器後綴](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) 明確標記哪些是控制器檔案。

## XSS過濾
考慮通用性，webman 沒有對請求進行 XSS 轉義。webman 強烈建議在渲染時進行 XSS 轉義，而不是在入庫前進行轉義。此外，twig、blade、think-tmplate 等模板會自動執行 XSS 轉義，無需手動轉義，非常方便。

> **提示**
> 如果在入庫前進行 XSS 轉義，很可能造成一些應用插件的不相容問題。

## 防止SQL注入
為了防止 SQL 注入，請盡量使用 ORM，如 [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html)、[think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html)，使用時請盡量不要自行組裝 SQL。

## nginx代理
當您的應用需要暴露給外網使用者時，強烈建議在 webman 前增加一個 nginx 代理，這樣可以過濾一些非法 HTTP 請求，提高安全性。具體請參考 [nginx代理](nginx-proxy.md)。