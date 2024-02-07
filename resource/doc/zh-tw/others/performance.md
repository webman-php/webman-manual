# webman性能


### 傳統框架請求處理流程

1. nginx/apache收到請求
2. nginx/apache將請求傳遞給php-fpm
3. php-fpm初始化環境，如創建變量列表
4. php-fpm調用各個擴展/模塊的RINIT
5. php-fpm磁碟讀取php文件(使用opcache可避免)
6. php-fpm詞法分析、語法分析、編譯成opcode(使用opcache可避免)
7. php-fpm執行opcode 包括8.9.10.11
8. 框架初始化，如實例化各種類，包括如容器、控制器、路由、中間件等。
9. 框架連接數據庫並權限驗證，連接redis
10. 框架執行業務邏輯
11. 框架關閉數據庫、redis連接
12. php-fpm釋放資源、銷毀所有類定義、實例、銷毀符號表等
13. php-fpm順序調用各個擴展/模塊的RSHUTDOWN方法
14. php-fpm將結果轉發給nginx/apache
15. nginx/apache將結果返回給客戶端


### webman的請求處理流程
1. 框架接收請求
2. 框架執行業務邏輯
3. 框架將結果返回給客戶端

沒錯，在沒有nginx反代的情況下，框架只有這3步。可以說這已經是php框架的極致，這使得webman性能是傳統框架的幾倍甚至數十倍。

更多參考 [壓力測試](benchmarks.md)
