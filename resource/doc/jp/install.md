# 環境要件

* PHP >= 7.2
* [Composer](https://getcomposer.org/) >= 2.0


### 1. プロジェクトの作成

```php
composer create-project workerman/webman
```

### 2. 実行

webmanディレクトリに移動する

#### Windowsユーザー
`windows.bat`をダブルクリックするか、`php windows.php`を実行して起動します

> **ヒント**
> エラーが発生した場合は、おそらく関数が無効になっている可能性があります。 [無効な関数をチェック](others/disable-function-check.md)を参照して無効化を解除してください

#### Linuxユーザー
`debug`モードで実行（開発デバッグ用）

```php
php start.php start
```

`daemon`モードで実行（本番環境用）

```php
php start.php start -d
```

> **ヒント**
> エラーが発生した場合は、おそらく関数が無効になっている可能性があります。 [無効な関数をチェック](others/disable-function-check.md)を参照して無効化を解除してください

### 3. アクセス

ブラウザで `http://ipアドレス:8787` にアクセスします
