# キャプチャ関連コンポーネント

## webman/captcha
プロジェクトリンク https://github.com/webman-php/captcha

### インストール
```php
composer require webman/captcha
```

### 使用方法

**`app/controller/LoginController.php` ファイルを作成**

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
     * キャプチャ画像を出力
     */
    public function captcha(Request $request)
    {
        // キャプチャクラスを初期化
        $builder = new CaptchaBuilder;
        // キャプチャ生成
        $builder->build();
        // キャプチャの値をセッションに保存
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // キャプチャ画像のバイナリデータを取得
        $img_content = $builder->get();
        // キャプチャのバイナリデータを出力
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * キャプチャをチェック
     */
    public function check(Request $request)
    {
        // POSTリクエストからcaptchaフィールドを取得
        $captcha = $request->post('captcha');
        // セッション内のcaptcha値と比較
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => '入力したキャプチャが正しくありません']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

**`app/view/login/index.html` テンプレートファイルを作成**

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
        <input type="submit" value="提出" />
    </form>
</body>
</html>
```

`http://127.0.0.1:8787/login` ページに入ると以下のような画面が表示されます：
  ![](../../assets/img/captcha.png)

### よくある設定パラメータ
```php
    /**
     * キャプチャ画像を出力
     */
    public function captcha(Request $request)
    {
        // キャプチャクラスを初期化
        $builder = new CaptchaBuilder;
        // キャプチャの長さ
        $length = 4;
        // 含まれる文字
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // キャプチャ生成
        $builder->build();
        // キャプチャの値をセッションに保存
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // キャプチャ画像のバイナリデータを取得
        $img_content = $builder->get();
        // キャプチャのバイナリデータを出力
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

その他のAPIおよびパラメータについては、 https://github.com/webman-php/captcha を参照してください。
