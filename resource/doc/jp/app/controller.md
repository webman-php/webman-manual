# コントローラー

PSR4規格に従い、コントローラークラスの名前空間は`plugin\{プラグイン識別子}`で始まります。例えば、

新しいコントローラーファイル `plugin/foo/app/controller/FooController.php` を作成します。

```php
<?php
namespace plugin\foo\app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

`http://127.0.0.1:8787/app/foo/foo` を訪問すると、ページは `hello index` を返します。

`http://127.0.0.1:8787/app/foo/foo/hello` を訪問すると、ページは `hello webman` を返します。

## URLへのアクセス
アプリケーションのプラグインのURLアドレスのパスはすべて`/app`で始まり、それに続いてプラグインの識別子、そして具体的なコントローラーとメソッドが続きます。
例えば、`plugin\foo\app\controller\UserController`のURLアドレスは `http://127.0.0.1:8787/app/foo/user` です。
