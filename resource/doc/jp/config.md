# 設定ファイル

## 位置
webmanの設定ファイルは`config/`ディレクトリにあり、プロジェクトでは`config()`関数を使用して対応する設定を取得できます。

## 設定の取得

すべての設定を取得する
```php
config();
```

`config/app.php`のすべての設定を取得する
```php
config('app');
```

`config/app.php`の`debug`設定を取得する
```php
config('app.debug');
```

設定が配列の場合、`.`を使って配列内の要素の値を取得することができます。例えば
```php
config('file.key1.key2');
```

## デフォルト値
```php
config($key, $default);
```
2番目のパラメータを使用してデフォルト値を渡し、設定が存在しない場合はデフォルト値を返します。
設定が存在せず、デフォルト値が設定されていない場合はnullを返します。

## カスタム設定
開発者は`config/`ディレクトリに独自の設定ファイルを追加することができます。例えば

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**設定を取得するときに使用**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## 設定の変更
webmanは設定の動的な変更をサポートしていません。すべての設定は対応する設定ファイルを手動で変更し、reloadまたはrestartを行う必要があります。

> **注意**
>`config/server.php`でのサーバーの設定と`config/process.php`でのプロセスの設定はreloadをサポートしていません。restartを行う必要があります。
