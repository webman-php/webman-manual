# コントローラー

PSR4規格に基づき、コントローラークラスの名前空間は `plugin\{プラグイン識別子}` で始まります。例えば、

コントローラーファイル `plugin/foo/app/controller/FooController.php` を新規作成します。

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

`http://127.0.0.1:8787/app/foo/foo` にアクセスすると、ページに `hello index` が返されます。

`http://127.0.0.1:8787/app/foo/foo/hello` にアクセスすると、ページに `hello webman` が返されます。


## URLアクセス
アプリのプラグインのURLアドレスは常に `/app` で始まり、その後にプラグインの識別子、そして具体的なコントローラーとメソッドが続きます。
例えば、`plugin\foo\app\controller\UserController` のURLアドレスは `http://127.0.0.1:8787/app/foo/user` です。
