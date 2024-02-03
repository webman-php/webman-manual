# プロセスモニタリング
webmanにはデフォルトでモニタリングプロセスが付属しており、以下の2つの機能をサポートしています。
1. ファイルの更新を監視し、新しいビジネスコードを自動でリロードする（通常、開発中に使用されます）。
2. すべてのプロセスのメモリ使用量を監視し、あるプロセスが`php.ini`の`memory_limit`制限を超えそうになると、そのプロセスを安全に再起動します（ビジネスには影響しません）。

### モニタリング設定
構成ファイル `config/process.php` で`monitor`の構成を設定します。
```php

global $argv;

return [
    // ファイルの更新を検知し自動でリロード
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // これらのディレクトリを監視
            'monitorDir' => array_merge([    // 監視するディレクトリ
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // 監視するファイルの拡張子
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // ファイルモニタリングを有効にするか
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // メモリモニタリングを有効にするか
            ]
        ]
    ]
];
```
`monitorDir` は更新を監視するディレクトリを構成します（監視対象のファイルは多すぎないようにする必要があります）。
`monitorExtensions` は `monitorDir` の中で監視するファイルの拡張子を構成します。
`options.enable_file_monitor` が`true` の場合、ファイルの更新を監視する機能が有効になります（Linuxシステムでデバッグ実行する場合はデフォルトでファイルモニタリングが有効になります）。
`options.enable_memory_monitor` が`true` の場合、メモリ使用量の監視が有効になります（メモリ使用量の監視はWindowsシステムではサポートされていません）。

> **ヒント:**
> Windowsシステムでファイル更新モニタリングを有効にするには、`windows.bat` や `php windows.php` を実行する必要があります。
