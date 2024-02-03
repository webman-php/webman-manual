# webman イベントライブラリ webman-event

[![license](https://img.shields.io/github/license/Tinywan/webman-event)]()
[![webman-event](https://img.shields.io/github/v/release/tinywan/webman-event?include_prereleases)]()
[![webman-event](https://img.shields.io/badge/build-passing-brightgreen.svg)]()
[![webman-event](https://img.shields.io/github/last-commit/tinywan/webman-event/main)]()
[![webman-event](https://img.shields.io/github/v/tag/tinywan/webman-event?color=ff69b4)]()

イベントはミドルウェアと比較して、より精密な位置決め（つまり、細かい粒度）を持ち、一部のビジネスシーンの拡張に適しているという利点があります。例えば、通常、ユーザーが登録した後に一連の処理を行う必要があるケースがありますが、イベントシステムを使用すると、既存のコードを侵さずにログイン処理を拡張することができ、システムの結合度を低くし、同時にバグの可能性も低くします。

## プロジェクトアドレス

[https://github.com/Tinywan/webman-permission](https://github.com/Tinywan/webman-permission)

## 依存関係

- [symfony/event-dispatcher](https://github.com/symfony/event-dispatcher)

## インストール

```shell script
composer require tinywan/webman-event
```
## 設定 

イベント設定ファイル `config/event.php` の内容は以下の通りです。

```php
return [
   // イベントリスナー
   'listener'    => [],

   // イベントサブスクライバー
   'subscriber' => [],
];
```
### プロセス起動設定

`config/bootstrap.php` を開いて、以下の設定を追加します。

```php
return [
   // ここでは他の設定を省略しています ...
   webman\event\EventManager::class,
];
```
## はじめ方

### イベントの定義

イベントクラス `LogErrorWriteEvent.php`

```php
declare(strict_types=1);

namespace extend\event;

use Symfony\Contracts\EventDispatcher\Event;

class LogErrorWriteEvent extends Event
{
   const NAME = 'log.error.write';  // イベント名、イベントの一意な識別子

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
    * @desc: イベントの発生
    * @param LogErrorWriteEvent $event
    */
   public function onLogErrorWrite(LogErrorWriteEvent $event)
   {
       // 具体的なビジネスロジック
       var_dump($event->handle());
   }
}
```

イベントのサブスクライブ
```php
return [
   // イベントサブスクライバー
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

![結果を表示](./trigger.png)

## ライセンス

このプロジェクトは[Apache 2.0 ライセンス](LICENSE)のもとでライセンスされています。
