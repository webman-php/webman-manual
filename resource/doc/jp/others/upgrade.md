# アップグレード方法

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **注意**
> Alibaba CloudのComposerプロキシがComposer公式リポジトリからのデータ同期を停止しているため、現在最新のwebmanにアップグレードすることはできません。以下のコマンドを使用して、`composer config -g --unset repos.packagist` Composerの公式データソースを復元してください。
