# キャプチャに関連するコンポーネント

## webman/captcha
プロジェクトの場所 https://github.com/webman-php/captcha

### インストール
```
composer require webman/captcha
```

### 使用法

**ファイル `app/controller/LoginController.php` を作成します**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * テストページ
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * キャプチャ画像を出力する
     */
    public function captcha(Request $request)
    {
        // キャプチャビルダーの初期化
        $builder = new CaptchaBuilder;
        // キャプチャを生成する
        $builder->build();
        // キャプチャの値をセッションに保存する
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // キャプチャ画像のバイナリデータを取得
        $img_content = $builder->get();
        // キャプチャのバイナリデータを出力
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * キャプチャをチェックする
     */
    public function check(Request $request)
    {
        // POSTリクエストからcaptchaフィールドを取得
        $captcha = $request->post('captcha');
        // セッションに保存されたcaptchaの値と比較する
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => '入力されたキャプチャが間違っています']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**テンプレートファイル `app/view/login/index.html` を作成します**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>キャプチャテスト</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="送信" />
    </form>
</body>
</html>
```

ページに移動 `http://127.0.0.1:8787/login` 画面は次のようになります：
  ![](../../assets/img/captcha.png)

### 一般的なパラメータの設定
```php
    /**
     * キャプチャ画像を出力する
     */
    public function captcha(Request $request)
    {
        // キャプチャビルダーの初期化
        $builder = new CaptchaBuilder;
        // キャプチャの長さ
        $length = 4;
        // 含まれる文字
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // キャプチャを生成する
        $builder->build();
        // キャプチャの値をセッションに保存する
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // キャプチャ画像のバイナリデータを取得
        $img_content = $builder->get();
        // キャプチャのバイナリデータを出力
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

詳細なAPIおよびパラメータについては、https://github.com/webman-php/captcha を参照してください。
