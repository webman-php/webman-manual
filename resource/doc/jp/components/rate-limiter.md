# レート制限

webman レート制限、アノテーションによる制限をサポート。
apcu、redis、memory ドライバをサポート。

## ソースコード

https://github.com/webman-php/limiter

## インストール

```
composer require webman/limiter
```

## 使用方法

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
        // デフォルトは IP 制限、デフォルトの単位時間は 1 秒
        return '各 IP は 1 秒あたり最大 10 リクエスト';
    }

    #[Limit(limit: 100, ttl: 60, key: Limit::UID)]
    public function search(): string
    {
        // key: Limit::UID、ユーザー ID で制限、session('user.id') が空でない必要あり
        return '各ユーザーは 60 秒あたり最大 100 回検索';
    }

    #[Limit(limit: 1, ttl: 60, key: Limit::SID, message: '1 人あたり 1 分に 1 通のみメール送信可')]
    public function sendMail(): string
    {
        // key: Limit::SID、session_id で制限
        return 'メール送信成功';
    }

    #[Limit(limit: 100, ttl: 24*60*60, key: 'coupon', message: '本日のクーポンは配布終了、明日またお越しください')]
    #[Limit(limit: 1, ttl: 24*60*60, key: Limit::UID, message: '各ユーザーは 1 日 1 回のみクーポン取得可')]
    public function coupon(): string
    {
        // key: 'coupon'、カスタム key でグローバル制限、1 日最大 100 枚のクーポン
        // ユーザー ID でも制限、各ユーザーは 1 日 1 回のみクーポン取得可
        return 'クーポン送信成功';
    }

    #[Limit(limit: 5, ttl: 24*60*60, key: [UserController::class, 'getMobile'], message: '各携帯番号は 1 日最大 5 通の SMS')]
    public function sendSms2(): string
    {
        // key が変数の場合、[クラス, 静的メソッド] で key を取得、例: [UserController::class, 'getMobile'] は UserController::getMobile() の戻り値を key として使用
        return 'SMS 送信成功';
    }

    /**
     * カスタム key、携帯番号を取得、静的メソッドである必要あり
     * @return string
     */
    public static function getMobile(): string
    {
        return request()->get('mobile');
    }

    #[Limit(limit: 1, ttl: 10, key: Limit::IP, message: 'レート制限', exception: RuntimeException::class)]
    public function testException(): string
    {
        // 超過時のデフォルト例外は support\limiter\RateLimitException、exception パラメータで変更可
        return 'ok';
    }

}
```

**説明**

* 固定ウィンドウアルゴリズムを使用
* ttl のデフォルト単位時間は 1 秒
* ttl で単位時間を設定、例: `ttl:60` は 60 秒
* デフォルトの制限次元は IP（デフォルト `127.0.0.1` は制限なし、下記設定参照）
* 内蔵: IP 制限、UID 制限（`session('user.id')` が空でない必要）、SID 制限（`session_id` で制限）
* nginx プロキシ使用時、IP 制限には `X-Forwarded-For` ヘッダを渡す、[nginx プロキシ](../others/nginx-proxy.md) 参照
* 超過時は `support\limiter\RateLimitException` をトリガー、`exception:xx` でカスタム例外クラス指定可
* 超過時のデフォルトエラーメッセージは `Too Many Requests`、`message:xx` でカスタムメッセージ指定可
* デフォルトエラーメッセージは [多言語](translation.md) でも変更可、Linux 参考:

```
composer require symfony/translation
mkdir resource/translations/zh_CN/ -p
echo "<?php
return [
    'Too Many Requests' => '请求频率受限'
];" > resource/translations/zh_CN/messages.php
php start.php restart
```

## API

コード内で直接レート制限を呼び出す場合の例:

```php
<?php
namespace app\controller;

use RuntimeException;
use support\limiter\Limiter;

class UserController {

    public function sendSms(string $mobile): string
    {
        // ここでは mobile を key として使用
        Limiter::check($mobile, 5, 24*60*60, '各携帯番号は 1 日最大 5 通の SMS');
        return 'SMS 送信成功';
    }
}
```

## 設定

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
    // これらの IP はレート制限対象外（key が Limit::IP の場合のみ有効）
    'ip_whitelist' => [
        '127.0.0.1',
    ],
    'exception' => RateLimitException::class
];
```

* **enable**: レート制限を有効にするか
* **driver**: `auto`、`apcu`、`memory`、`redis` のいずれか、`auto` は `apcu`（優先）と `memory` から自動選択
* **stores**: redis 設定、`connection` は `config/redis.php` の対応する key
* **ip_whitelist**: ホワイトリストの IP はレート制限対象外（key が `Limit::IP` の場合のみ有効）

## driver 選択

**memory**

* 紹介
  拡張不要、最高パフォーマンス。

* 制限
  現在のプロセスのみ有効、プロセス間でデータ共有なし、クラスター制限非対応。

* 適用シーン
  Windows 開発環境；厳密な制限が不要な業務；CC 攻撃対策。

**apcu**

* 拡張インストール
  apcu 拡張が必要、php.ini で設定:

```
apc.enabled=1
apc.enable_cli=1
```

php.ini の場所は `php --ini` で確認

* 紹介
  高パフォーマンス、マルチプロセス共有対応。

* 制限
  クラスター非対応

* 適用シーン
  あらゆる開発環境；本番単機制限；クラスターで厳密な制限が不要な場合；CC 攻撃対策。

**redis**

* 依存
  redis 拡張と Redis コンポーネントが必要、インストール:

```
composer require -W webman/redis illuminate/events
```

* 紹介
  apcu より低パフォーマンス、単機・クラスター両方の精密制限に対応

* 適用シーン
  開発環境；本番単機；クラスター環境
