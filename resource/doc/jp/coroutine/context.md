`support\Context`クラスは、リクエストのコンテキストデータを格納するために使用され、リクエストが完了すると、対応するコンテキストデータは自動的に削除されます。つまり、コンテキストデータのライフサイクルはリクエストのライフサイクルに従います。`support\Context` はFiber、Swoole、Swowのコルーチン環境をサポートしています。

詳細については、[webman协程](./fiber.md)を参照してください。

# インターフェース
コンテキストは以下のインターフェースを提供しています。

## コンテキストデータを設定
```php
Context::set(string $name, mixed $value);
```

## コンテキストデータを取得
```php
Context::get(string $name = null);
```

## コンテキストデータを削除
```php
Context::delete(string $name);
```

> **注意**
> フレームワークは、リクエスト終了後に`Context::destroy()`インターフェースを自動的に呼び出してコンテキストデータを破棄します。ビジネスコードで`Context::destroy()`を手動で呼び出してはいけません。

# 例
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        return Context::get('name');
    }
}
```

# 注意
**コルーチンを使用する場合**、**リクエストに関連する状態データ**をグローバル変数や静的変数に保存してはいけません。これはグローバルなデータの汚染を引き起こす可能性があります。正しい方法は、それらを保存およびアクセスする際に、Contextを使用することです。
