# プロセスモニタリング
webmanには、プロセスモニタリングを行うモニタリングプロセスが付属しており、以下の2つの機能をサポートします。
1. ファイルの更新を監視して自動的に新しいビジネスコードをリロードする（通常は開発時に使用されます）
2. すべてのプロセスのメモリ使用量を監視し、あるプロセスが`php.ini`の`memory_limit`制限を超えそうになると、そのプロセスを自動的に安全に再起動します（ビジネスに影響を与えません）

## モニタリングの設定
構成ファイル `config/process.php` 内の `monitor` 設定
```php
global $argv;

return [
    // ファイルの更新を検出して自動的にリロード
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // これらのディレクトリを監視
            'monitorDir' => array_merge([    // 監視すべきディレクトリ
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // これらの拡張子を持つファイルが監視されます
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // ファイル監視を有効にするかどうか
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // メモリ監視を有効にするかどうか
            ]
        ]
    ]
];
```
`monitorDir` は、どのディレクトリの更新を監視するかを設定するために使用されます（監視対象のファイルは多すぎないようにすることが推奨されます）。
`monitorExtensions` は、`monitorDir`ディレクトリ内で監視するべきファイルの拡張子を設定します。
`options.enable_file_monitor` の値が`true`の場合、ファイル更新監視が有効になります（Linuxシステムではデバッグモードで実行された場合、デフォルトでファイル監視が有効になります）。
`options.enable_memory_monitor` の値が`true`の場合、メモリ使用量の監視が有効になります（メモリ使用量の監視はWindowsシステムではサポートされていません）。

> **注意**
> Windowsシステムでは、`windows.bat`または`php windows.php`を実行する必要があります。これにより、ファイルの更新監視が有効になります。
