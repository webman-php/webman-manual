# webman イベントライブラリ webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

イベントはミドルウェアよりも精密なロケーション（つまり、より細かい粒度）であり、特定のビジネスシーンの拡張により適しています。たとえば、通常、ユーザーの登録やログイン後に一連の操作を行う必要がありますが、イベントシステムを使用すると、既存のコードに侵入することなくログイン操作の拡張が可能になり、システムの結合度が低くなり、バグの可能性も低くなります。

## プロジェクトのリンク

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## 依存関係

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## インストール

```shell script
composer require tinywan/webman-event
```
## 設定 

イベントの設定ファイル `config/event.php` の内容は次のとおりです。

```php
return [
    // イベントリスナー
    'listener'    => [],

    // イベントサブスクライバー
    'subscriber' => [],
];
```
### プロセス起動の設定

 `config/bootstrap.php` を開き、次の設定を追加します。

```php
return [
    // 他の設定は省略されています...
    webman\event\EventManager::class,
];
```
## クイックスタート

### イベントの定義

イベントクラス `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
    const NAME = 'log.error.write';  // イベント名、イベントのユニークな識別子

    /** @var array */
    public array $log;

    public function __construct(array $log)
    {
        $this->log = $log;
    }

    public function handle()
    {
        return $this->log;
    }
}
```

### イベントのリスニング
```php
return [
    // イベントリスナー
    'listener'    => [
        \extend\event\LogErrorWriteEvent::NAME  => \extend\event\LogErrorWriteEvent::class,
    ],
];
```

### イベントのサブスクライブ

サブスクライブクラス `LoggerSubscriber.php`

```php
namespace extend\event\subscriber;

use extend\event\LogErrorWriteEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LoggerSubscriber implements EventSubscriberInterface
{
    /**
     * @desc: メソッドの説明
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            LogErrorWriteEvent::NAME => 'onLogErrorWrite',
        ];
    }

    /**
     * @desc: イベントのトリガー
     * @param LogErrorWriteEvent $event
     */
    public function onLogErrorWrite(LogErrorWriteEvent $event)
    {
        // いくつかの具体的なビジネスロジック
        var_dump($event->handle());
    }
}
```

イベントのサブスクライブ
```php
return [
    // イベントのサブスクライブ
    'subscriber' => [
        \extend\event\subscriber\LoggerSubscriber::class,
    ],
];
```

### イベントトリガー

`LogErrorWriteEvent` イベントをトリガーします。

```php
$error = [
    'errorMessage' => 'エラーメッセージ',
    'errorCode' => 500
];
EventManager::trigger(new LogErrorWriteEvent($error),LogErrorWriteEvent::NAME);
```

実行結果

![イベントトリガー結果](./trigger.png)

## ライセンス

このプロジェクトは [Apache 2.0 ライセンス](LICENSE)のもとでライセンスされています。
