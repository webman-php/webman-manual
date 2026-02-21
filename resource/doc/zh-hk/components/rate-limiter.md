# 限流器

webman 限流器，支援註解限流。
支援 apcu、redis、memory 驅動。

## 原始碼地址

https://github.com/webman-php/limiter

## 安裝

```
composer require webman/limiter
```

## 使用

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\annotation\Limit;

class UserController
{

    #[Limit(limit: 10)]
    public function index(): string
    {
        // 預設為 IP 限流，預設單位時間為 1 秒
        return '每個 IP 每秒最多 10 個請求';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID，以用戶 ID 為維度進行限流，要求 session('user.id') 不為空
        return '每個用戶 60 秒最多 100 次搜尋';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: '每人每分鐘只能發 1 次郵件')]
    public function sendMail(): string
    {
        // key: Limit::SID，以 session_id 為維度進行限流
        return '郵件發送成功';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: '今天的優惠券已經發完，請明天再來')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: '每個用戶每天只能領取一次優惠券')]
    public function coupon(): string
    {
        // key: 'coupon'，這裡 coupon 為自訂 key，也就是全域以 coupon 為 key 進行限流，每天最多發 100 張優惠券
        // 同時以用戶 ID 為維度進行限流，每個用戶每天只能領取一次優惠券
        return '優惠券發送成功';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: '每個手機號一天最多 5 條短訊')]
    public function sendSms2(): string
    {
        // 當 key 為變數時，可以使用 [類, 靜態方法] 的方式取得 key，例如 [UserController::class, 'getMobile'] 會呼叫 UserController 的 getMobile() 方法的返回值為 key
        return '短訊發送成功';
    }

    /**
     * 自訂 key，取得手機號，必須是靜態方法
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: '頻率受限', exception: RuntimeException::class)]
    public function testException(): string
    {
        // 超限預設異常為 support\limiter\RateLimitException，可透過 exception 參數更改
        return 'ok';
    }

}
```

**說明**

* 演算法為固定視窗演算法
* ttl 預設單位時間間隔為 1 秒
* 可透過 ttl 設定單位時間間隔，例如 `ttl:60` 為 60 秒
* 預設限流維度為 IP 限流（預設 `127.0.0.1` 不限流，參見下面配置部分）
* 內建 IP 限流、UID 限流（要求 `session('user.id')` 不為空）、SID 限流（根據 `session_id` 限流）
* 若使用 nginx 代理，IP 限流時需傳遞 `X-Forwarded-For` 頭，參見 [nginx 代理](../others/nginx-proxy.md)
* 超限時會觸發 `support\limiter\RateLimitException` 異常，可透過 `exception:xx` 自訂異常類
* 超限觸發異常時，錯誤訊息預設為 `Too Many Requests`，可透過 `message:xx` 自訂錯誤訊息
* 預設錯誤訊息也可透過 [多語言](translation.md) 修改，Linux 參考以下指令：

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '請求頻率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## 介面

有時開發者想直接在程式碼中呼叫限流器，參考如下程式碼：

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // 這裡 mobile 作為 key
        Limiter::check($mobile, 5, 24*60*60, '每個手機號一天最多 5 條短訊');
        return '短訊發送成功';
    }
}
```

## 配置

**config/plugin/webman/limiter/app.php**

```
<?php

use support\limiter\RateLimitException;

return [
    'enable' => true,
    'driver' => 'auto', // auto, apcu, memory, redis
    'stores' => [
        'redis' => [
            'connection' => 'default',
        ]
    ],
    // 這些 IP 的請求不做頻率限制（只有在 key 為 Limit::IP 時有效）
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: 是否開啟限流
* **driver**: `auto`、`apcu`、`memory`、`redis` 其中一個值，使用 `auto` 時會自動在 `apcu`（優先）和 `memory` 中選一個值
* **stores**: `redis` 配置，`connection` 對應 `config/redis.php` 中對應的 `key`
* **ip_whitelist**: 白名單的 IP 不會被限流（只在 key 為 `Limit::IP` 時有效）

## driver 選擇

**memory**

* 介紹
  無需安裝任何擴充，效能最佳。

* 使用限制
  限流只對目前進程有效，多個進程間不共用限流資料，同時也不支援叢集限流。

* 適用場景
  Windows 開發環境；不需要嚴格限流的業務；抵禦 CC 攻擊時。

**apcu**

* 安裝擴充
  需安裝 apcu 擴充，並在 php.ini 中設定：

```
apc.enabled=1
apc.enable_cli=1
```

若不知道 php.ini 位置，可透過指令 `php --ini` 尋找 php.ini 的位置。

* 介紹
  效能非常好，支援多進程共用限流資料。

* 使用限制
  不支援叢集

* 適用場景
  任何開發環境；線上單機限流場景；叢集不需要嚴格限流的場景；抵禦 CC 攻擊。

**redis**

* 依賴
  需安裝 redis 擴充，並安裝 Redis 元件，安裝指令：

```
composer require -W webman/redis illuminate/events
```

* 介紹
  效能低於 apcu，支援單機也支援叢集精確限流。

* 適用場景
  開發環境；線上單機環境；叢集環境。
