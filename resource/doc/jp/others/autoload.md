# 自動読み込み

## Composerを使用してPSR-0の規格のファイルを読み込む
webmanは`PSR-4`の自動読み込み規格に従います。もし、貴方の業務が`PSR-0`の規格のコードライブラリを読み込む必要があれば、以下の手順を参考にしてください。

- `extend`ディレクトリを作成し、その中に`PSR-0`の規格に従ったコードライブラリを保存します。
- `composer.json`を編集し、`autoload`に以下の内容を追加します。

```js
"psr-0" : {
    "": "extend/"
}
```
最終的な結果は以下のようになります。
![](../../assets/img/psr0.png)

- `composer dumpautoload`を実行します。
- `php start.php restart`を実行してwebmanを再起動します（注意：再起動する必要があります）。

## Composerを使用して特定のファイルを読み込む

- `composer.json`を編集し、`autoload.files`に読み込みたいファイルを追加します。

```json
"files": [
    "./support/helpers.php",
    "./app/helpers.php"
]
```
- `composer dumpautoload`を実行します。
- `php start.php restart`を実行してwebmanを再起動します（注意：再起動する必要があります）。

> **ヒント**
> composer.jsonの`autoload.files`に設定されているファイルは、webmanが起動する前に読み込まれます。一方で、フレームワークの`config/autoload.php`で読み込まれるファイルは、webmanが起動した後に読み込まれます。
> composer.jsonの`autoload.files`で読み込まれるファイルを変更した場合、再起動が必要ですが、再読み込みでは反映されません。一方で、フレームワークの`config/autoload.php`で読み込まれるファイルは、変更後に再読み込みすることで即座に反映されます。

## フレームワークを使用して特定のファイルを読み込む

SPR規格に準拠しないファイルがある場合、`config/autoload.php`を設定してこれらのファイルを読み込むことができます。例：

```php
return [
    'files' => [
        base_path() . '/app/functions.php',
        base_path() . '/support/Request.php', 
        base_path() . '/support/Response.php',
    ]
];
```

> **ヒント**
> `autoload.php`で`support/Request.php`と`support/Response.php`の読み込み設定がされています。これは、`vendor/workerman/webman-framework/src/support/`にも同名のファイルがあるためです。`autoload.php`を使用することで、プロジェクトルートの`support/Request.php`および`support/Response.php`を優先的に読み込み、これらのファイルの内容をカスタマイズすることができます。もしカスタマイズが必要ない場合は、これらの設定を無視していただいて構いません。
