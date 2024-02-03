# コンテキスト

`support\Context`クラスは、リクエストのコンテキストデータを格納するために使用され、リクエストが完了すると対応するコンテキストデータが自動的に削除されます。つまり、コンテキストデータの寿命はリクエストの寿命に従います。`support\Context`はFiber、Swoole、Swowのコルーチン環境をサポートしています。

詳細については[webmanコルーチン](./fiber.md)を参照してください。

# インターフェース
コンテキストは以下のインターフェースを提供します。

## コンテキストデータの設定
```php
Context::set(string $name, $mixed $value);
```

## コンテキストデータの取得
```php
Context::get(string $name = null);
```

## コンテキストデータの削除
```php
Context::delete(string $name);
```

> **注意**
> フレームワークはリクエスト終了後に自動的にContext::destroy()インターフェースを呼び出してコンテキストデータを破棄します。ビジネス側ではContext::destroy()を手動で呼び出すことはできません。

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
**コルーチンを使用する場合**、**リクエストに関連する状態データ**をグローバル変数や静的変数に格納してはいけません。これはグローバルデータの汚染を引き起こす可能性があるため、正しい方法はそれらを格納および取得するためにContextを使用することです。
