# バリデータ
Composerには、次のようなバリデータが直接使えるものがたくさんあります。
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## バリデータ top-think/think-validate

### 説明
ThinkPHP公式のバリデータです。

### プロジェクトのURL
https://github.com/top-think/think-validate

### インストール
`composer require topthink/think-validate`

### クイックスタート

**新規作成： `app/index/validate/User.php`**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];

    protected $message  =   [
        'name.require' => '名前は必須です',
        'name.max'     => '名前は25文字以下で入力してください',
        'age.number'   => '年齢は数字で入力してください',
        'age.between'  => '年齢は1から120の間で入力してください',
        'email'        => 'メールアドレスのフォーマットが間違っています',    
    ];

}
```
  
**使用方法**
```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

<a name="respect-validation"></a>
## バリデータ workerman/validation

### 説明
プロジェクトはhttps://github.com/Respect/Validationの中国語版です。

### プロジェクトのURL
https://github.com/walkor/validation
  
  
### インストール
 
```php
composer require workerman/validation
```

### クイックスタート

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        $data = v::input($request->post(), [
            'nickname' => v::length(1, 64)->setName('ニックネーム'),
            'username' => v::alnum()->length(5, 64)->setName('ユーザー名'),
            'password' => v::length(5, 64)->setName('パスワード')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**jQueryを使ってアクセス**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'汤姆', username:'tom cat', password: '123456'}
  });
  ```
  
結果：

`{"code":500,"msg":"ユーザー名はアルファベット（a-z）および数字（0-9）のみを含むことができます"}`

説明：

`v::input(array $input, array $rules)` はデータを検証し収集するために使用されます。データの検証に失敗した場合、`Respect\Validation\Exceptions\ValidationException`例外がスローされ、検証に成功した場合は検証されたデータ（配列）が返されます。

ビジネスコードが検証例外をキャッチしていない場合、webmanフレームワークは自動的に検証例外をキャッチし、HTTPリクエストヘッダーに応じてJSONデータ（`{"code":500, "msg":"xxx"}`のような）または通常の例外ページを返します。返された形式がビジネス要件に合わない場合、開発者は`ValidationException`例外を自分でキャッチし、必要なデータを返すことができます。次の例のように：

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class IndexController
{
    public function index(Request $request)
    {
        try {
            $data = v::input($request->post(), [
                'username' => v::alnum()->length(5, 64)->setName('ユーザー名'),
                'password' => v::length(5, 64)->setName('パスワード')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

### バリデータ機能ガイド

```php
use Respect\Validation\Validator as v;

// 個別のルールの検証
$number = 123;
v::numericVal()->validate($number); // true

// 複数のルールを連鎖して検証
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 最初の失敗した検証の理由を取得
try {
    $usernameValidator->setName('ユーザー名')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // ユーザー名はアルファベット（a-z）および数字（0-9）のみを含むことができます
}

// 失敗した検証の理由をすべて取得
try {
    $usernameValidator->setName('ユーザー名')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 出力される内容
    // -  ユーザー名が次の規則に従う必要があります
    //     - ユーザー名はアルファベット（a-z）および数字（0-9）のみを含むことができます
    //     - ユーザー名は空白を含んではいけません
  
    var_export($exception->getMessages());
    // 出力される内容
    // array (
    //   'alnum' => 'ユーザー名はアルファベット（a-z）および数字（0-9）のみを含むことができます',
    //   'noWhitespace' => 'ユーザー名は空白を含んではいけません',
    // )
}

// カスタムエラーメッセージ
try {
    $usernameValidator->setName('ユーザー名')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'ユーザー名はアルファベットと数字のみを含むことができます',
        'noWhitespace' => 'ユーザー名は空白を含んではいけません',
        'length' => 'lengthの規則により、このメッセージは表示されません'
    ]);
    // 出力される内容 
    // array(
    //    'alnum' => 'ユーザー名はアルファベットと数字のみを含むことができます',
    //    'noWhitespace' => 'ユーザー名は空白を含んではいけません'
    // )
}

// オブジェクトの検証
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// 配列の検証
$data = [
    'parentKey' => [
        'field1' => 'value1',
        'field2' => 'value2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // check() や validate() でも使用可能

// オプション検証
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 否定ルール
v::not(v::intVal())->validate(10); // false
```
  
### バリデータの3つのメソッド `validate()` `check()` `assert()` の違い

`validate()`は真偽値を返し、例外をスローしません

`check()`は検証に失敗した場合に例外をスローし、`$exception->getMessage()`で最初の検証の失敗理由を得ることができます。

`assert()`は検証に失敗した場合に例外をスローし、`$exception->getFullMessage()`ですべての検証失敗の理由を取得できます
  
  
### 一般的なバリデータのルールのリスト

`Alnum()` アルファベットと数字のみを含む

`Alpha()` アルファベットのみを含む

`ArrayType()` 配列のタイプ

`Between(mixed $minimum, mixed $maximum)` 入力が他の2つの値の間にあるかを検証します。

`BoolType()` ブール値であることを検証

`Contains(mixed $expectedValue)` 入力が特定の値を含んでいるかを検証

`ContainsAny(array $needles)` 入力が少なくとも1つの定義された値を含んでいるかを検証

`Digit()` 入力が数字のみを含むかを検証

`Domain()` 有効なドメインであることを検証

`Email()` 有効なメールアドレスであることを検証

`Extension(string $extension)` 拡張子を検証

`FloatType()` 浮動小数点数であることを検証

`IntType()` 整数であることを検証

`Ip()` IPアドレスであることを検証

`Json()` JSONデータであることを検証

`Length(int $min, int $max)` 長さが指定範囲内にあることを検証

`LessThan(mixed $compareTo)` 長さが指定値よりも小さいことを検証

`Lowercase()` 小文字であることを検証

`MacAddress()` MACアドレスであることを検証

`NotEmpty()` 空でないことを検証

`NullType()` nullであることを検証

`Number()` 数値であることを検証

`ObjectType()` オブジェクトであることを検証

`StringType()` 文字列であることを検証

`Url()` URLであることを検証
  
その他の検証ルールについては、https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ を参照してください。
  
### その他の情報

https://respect-validation.readthedocs.io/en/2.0/ をご覧ください。
