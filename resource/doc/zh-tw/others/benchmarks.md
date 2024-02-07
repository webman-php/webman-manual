# 壓力測試

## 壓測結果受哪些因素影響？
* 壓力機到伺服器的網路延遲（建議內網或本機壓測）
* 壓力機到伺服器的頻寬（建議內網或本機壓測）
* 是否開啟HTTP keep-alive（建議開啟）
* 並發數是否足夠（外網壓測要盡量開啟更大的並發）
* 服務端處理進程數是否合理（helloworld業務進程數建議與CPU數相同，資料庫業務進程數建議為CPU的四倍及以上）
* 業務自身性能（例如是否使用了外網資料庫）

## HTTP keep-alive是什麼？
HTTP Keep-Alive 機制是一種在單個TCP連接上發送多個HTTP請求和響應的技術，它對性能測試結果影響很大，關閉 keep-alive 後 QPS 可能成倍下降。
目前瀏覽器都是默認開啟 keep-alive 的，也就是瀏覽器訪問某一個 HTTP 地址後會將連接暫時保留不關閉，下一次請求時複用這個連接，用來提高性能。
壓測時建議開啟 keep-alive。

## 壓測時如何開啟HTTP keep-alive？
如果是用 ab 程序壓測需要加 -k 參數，例如 `ab -n100000 -c200 -k http://127.0.0.1:8787/`。
apipost 需要在返回頭中返回 gzip 頭才能開啟 keep-alive（apipost 的 bug，參考下面）。
其它壓測程式一般會默認開啟。

## 為什麼通過外網壓測QPS很低？
外網延遲很大導致 QPS 很低，是正常現象。例如壓測 baidu 頁面 QPS 可能只有幾十。
建議內網或者本機壓測，排除網路延遲影響。
如果一定要在外網壓測，可以通過增加並發數來增加吞吐量（需保證頻寬充足）。

## 為什麼經過nginx代理後性能下降？
nginx 運行需要消耗系統資源。同時，nginx 和 webman 之間的通訊也需要消耗一定的資源。
然而，系統的資源是有限的，webman 無法獲取到所有的系統資源，因此，整個系統的性能可能會有所下降是正常現象。
為了盡可能減少 nginx 代理帶來的性能影響，可以考慮關閉 nginx 日誌（`access_log off;`），
開啟 nginx 到 webman 之間的 keep-alive，參考[nginx代理](nginx-proxy.md)。

另外https和http相比會耗費更多資源，因為https需要進行SSL/TLS握手，數據加密解密，包的尺寸變大佔用更多頻寬，這些會導致性能下降。
壓測如果用的是短連結（不開啟HTTP keep-alive），每次請求都需要額外的SSL/TLS握手通訊，性能會大幅下降。建議壓測https 開啟HTTP keep-alive。

## 如何知道系統已經達到性能極限？
一般來說CPU達到100%時說明系統性能已經達到極限。如果CPU還有空閑說明還沒達到極限，這時候可以適當增加並發提高QPS。
如果增加並發無法提高QPS則可能是webman進程數不夠，請適當增加webman進程。如果仍然無法提高考慮頻寬是否足夠。

## 為什麼我壓測結果是webman性能低於go的gin框架？
[techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2)壓測顯示webman不管在純文本、數據庫查詢、數據庫更新等所有指標都高於gin近一倍左右。
如果你的結果不一樣，可能是因為你在webman中使用了ORM帶來了較大的性能損失，可嘗試 webman+原生PDO 與 gin+原生SQL 比較。

## webman中使用ORM性能會損失多少？
以下是一組壓測數據

**環境**
伺服器阿里雲4核 4G，從10萬條記錄中隨機查詢一條數據json返回。

**如果使用原生PDO**
webman QPS 為1.78萬

**如果使用laravel的Db::table()**
webman QPS降到 0.94萬QPS

**如果使用laravel的Model**
webmanQPS降到 0.72萬QPS

thinkORM結果類似，區別不大。

> **提示**
> 雖然使用ORM性能會有所下降，但是對於大部分業務來說已經足夠使用。我們應該在開發效率、可維護性、性能等多個指標中找到一個平衡點，而不是一味追求性能。

## 為什麼用apipost壓測QPS很低？
apipost的壓力測試模塊有bug，如果服務端不返回gzip頭則無法保持keep-alive，導致性能大幅下降。
解決辦法返回時將數據壓縮並添加gzip頭，例如
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```
除此之外，apipost一些情況下無法打出滿意的壓力，這表現為同樣的並發，使用apipost要比ab低50%左右的QPS。
壓測建議用ab、wrk或其它專業的壓測軟體而不是apipost。

## 設置合適的進程數
webman默認開啟CPU*4的進程數。實際上無網路IO的helloworld業務壓測進程數開成與CPU核數一致性能最優，因為可以減少進程切換開銷。
如果是帶數據庫、redis等阻塞IO業務，進程數可設置為CPU的3-8倍，因為這時需要更多的進程提高並發，而進程切換開銷相對與阻塞IO則基本可以忽略。

## 壓力測試一些參考範圍

**雲伺服器 4核 4G 16進程 本機/內網壓測**

| - | 開啟keep-alive | 未開啟keep-alive |
|--|-----|-----|
| hello world | 8-16萬QPS | 1-3萬QPS |
| 資料庫單查詢 | 1-2萬QPS | 1萬QPS |

[**第三方techempower壓測數據**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

## 壓測命令範例

**ab**
```plaintext
# 100000請求 200並發 開啟keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100000請求 200並發 未開啟keep-alive
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```plaintext
# 200 並發壓測10秒 開啟keep-alive(默認)
wrk -c 200 -d 10s http://example.com
```
