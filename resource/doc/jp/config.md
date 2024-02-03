# 設定ファイル

## 位置
webmanの設定ファイルは`config/`ディレクトリにあり、プロジェクトでは`config()`関数を使用して対応する設定を取得できます。

## 設定の取得

すべての設定を取得する
```php
config();
```

`config/app.php`にあるすべての設定を取得する
```php
config('app');
```

`config/app.php`にある`debug`の設定を取得する
```php
config('app.debug');
```

設定が配列の場合、`.`を使用して配列内の要素の値を取得できます。例えば
```php
config('file.key1.key2');
```

## デフォルト値
```php
config($key, $default);
```
`config`は第2引数を使用してデフォルト値を渡し、設定が存在しない場合はデフォルト値を返します。
設定が存在せず、デフォルト値が設定されていない場合はnullを返します。

## カスタム設定
開発者は`config/`ディレクトリに独自の設定ファイルを追加できます。例えば

**config/payment.php**

```php
<?php
return [
    'key' => '...',
    'secret' => '...'
];
```

**設定を取得する際の使用例**
```php
config('payment');
config('payment.key');
config('payment.key');
```

## 設定の変更
webmanは動的な設定変更をサポートしておらず、すべての設定は対応する設定ファイルを手動で変更し、reloadまたはrestartを行う必要があります。

> **注意**
> サーバー設定(`config/server.php`)およびプロセス設定(`config/process.php`)はreloadをサポートしておらず、restartが必要です。
