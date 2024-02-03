# webman框架

webman框架是一個基於workerman的高性能PHP框架，旨在為開發者提供快速、可擴展和高性能的Web應用程式開發體驗。

## 特性

- **基於workerman**：利用workerman的高性能網路庫，提供穩定、快速的網路通信能力。
- **高性能**：採用非同步非阻塞的事件驅動模型，處理大量並發連接，提高伺服器性能和吞吐量。
- **靈活可擴展**：支持自定義事件、回調函數和中間件，方便開發者靈活擴展框架功能。
- **簡單易用**：採用簡潔的API設計和註釋詳盡的文件，降低學習成本，提升開發效率。
- **支持WebSocket**：內建對WebSocket協議的支持，輕鬆實現實時通訊功能。
- **支持協程**：通過Swoole擴展支持協程，提供更強大的並發能力。

## 快速開始

安裝webman框架：

```bash
composer require workerman/workerman
composer require workerman/webman
```

創建一個簡單的HTTP伺服器：

```php
use Webman\App;

require __DIR__ . '/vendor/autoload.php';

$app = new App(__DIR__);

$http = new Workerman\Worker('http://0.0.0.0:2345');

$http->count = 4;

$http->onMessage = function ($connection, $request) use ($app) {
    $app->run($request, $connection);
};

Workerman\Worker::runAll();
```

以上就是webman框架的簡單介紹，更多詳細內容請參考官方文件。