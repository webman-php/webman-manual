# webman是甚麼

webman是一款基於[workerman](https://www.workerman.net)開發的高性能HTTP服務框架。webman用於取代傳統的php-fpm架構，提供超高性能可擴展的HTTP服務。你可以用webman開發網站，也可以開發HTTP接口或者微服務。

除此之外，webman還支持自定義進程，可以做workerman能做的任何事情，例如websocket服務、物聯網、遊戲、TCP服務、UDP服務、unix socket服務等等。

# webman理念
**以最小內核提供最大的擴展性與最強的性能。**

webman僅提供最核心的功能(路由、中間件、session、自定義進程接口)。其餘功能全部複用composer生態，這意味著你可以在webman裡使用最熟悉的功能組件，例如在數據庫方面開發者可以選擇使用Laravel的`illuminate/database`，也可以是ThinkPHP的`ThinkORM`，還可以是其他組件如`Medoo`。在webman裡集成他們是非常容易的事情。

# webman具有以下特點

1、高穩定性。webman基於workerman開發，workerman一直是業界bug極少的高穩定性socket框架。

2、超高性能。webman性能高於傳統php-fpm框架10-100倍左右，比go的gin echo等框架性能高一倍左右。

3、高複用。無需修改，可以複用絕大部分composer組件及類庫。

4、高擴展性。支持自定義進程，可以做workerman能做的任何事情。

5、超級簡單易用，學習成本極低，代碼書寫與傳統框架沒有區別。

6、使用最為寬鬆友好的MIT開源協議。

# 專案地址
GitHub: https://github.com/walkor/webman **不要吝嗇你的小星星哦**

碼雲: https://gitee.com/walkor/webman **不要吝嗇你的小星星哦**

# 第三方權威壓測數據

![](../assets/img/benchmark1.png)

帶數據庫查詢業務，webman單機吞吐量達到39萬QPS，比傳統php-fpm架構的laravel框架高出近80倍。

![](../assets/img/benchmarks-go.png)

帶數據庫查詢業務，webman比同類型go語言的web框架性能高一倍左右。

以上數據來自[techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
