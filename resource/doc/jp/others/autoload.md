# オートロード

## Composerを使用してPSR-0準拠のファイルをロードする
Webmanは`PSR-4`自動ロード規則に従います。ビジネスで`PSR-0`規則のコードライブラリをロードする必要がある場合は、以下の手順を参考にしてください。

- `extend`ディレクトリを作成し、`PSR-0`規則のコードライブラリを保存します。
- `composer.json`を編集し、以下の内容を`autoload`の下に追加します。

```js
"psr-0" : {
    "": "extend/"
}
```
最終的な結果は以下のようになります
![](../../assets/img/psr0.png)

- `composer dumpautoload`を実行します。
- `php start.php restart`を実行して、webmanを再起動します（注意：再起動する必要があります）。

## コンポーザーを使用して特定のファイルをロードする

- `composer.json`を編集し、`autoload.files`にロードするファイルを追加します。
```
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```

- `composer dumpautoload`を実行します。
- `php start.php restart`を実行して、webmanを再起動します（注意：再起動する必要があります）。

> **注意**
> `composer.json`の`autoload.files`で設定されたファイルは、webmanの起動前にロードされます。一方、フレームワークの`config/autoload.php`を使用してロードされるファイルは、webmanの起動後にロードされます。
> `composer.json`の`autoload.files`でロードされたファイルを変更した場合、再起動する必要がありますが、リロードでは効果がありません。一方、フレームワークの`config/autoload.php`を使用してロードされたファイルは、リロード後に変更が有効になります。

## フレームワークを使用して特定のファイルをロードする
一部のファイルはSPR規則に準拠しておらず、自動的にロードすることができません。そのような場合は、`config/autoload.php`を設定してこれらのファイルをロードできます。例えば：

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php',
        base_path() . '/support/Response.php',
    ]
];
```
 > **注意**
 > `autoload.php`で`support/Request.php` `support/Response.php`ファイルのロードが設定されていることがわかります。これは、`vendor/workerman/webman-framework/src/support/`にも同じ名前のファイルがあるためです。プロジェクトルートの`support/Request.php` `support/Response.php`を優先してロードするために、`autoload.php`を使用しています。このようにすることで、これらのファイルをカスタマイズする必要があっても、`vendor`内のファイルを変更する必要がありません。カスタマイズする必要がない場合は、これらの設定を無視できます。
