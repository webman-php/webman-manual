# バリデーター
Composerには直接使用できる多くのバリデーターがあります。たとえば以下のようなものがあります：
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="think-validate"></a>
## バリデータ top-think/think-validate

### 説明
ThinkPHP公式のバリデータ

### プロジェクトURL
https://github.com/top-think/think-validate

### インストール
`composer require topthink/think-validate`

### はじめに

**`app/index/validate/User.php` を新規作成**

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
        'name.require' => '名称必須',
        'name.max'     => '名称は25文字を超えることはできません',
        'age.number'   => '年齢は数字でなければなりません',
        'age.between'  => '年齢は1から120の間でなければなりません',
        'email'        => 'メール形式が正しくありません',    
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
# バリデータ workerman/validation

### 説明
https://github.com/Respect/Validation の中国語版プロジェクト

### プロジェクトURL
https://github.com/walkor/validation

### インストール
 
```php
composer require workerman/validation
```

### はじめに

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
            'username' => v::alnum()->length(5, 64)->setName('ユーザ名'),
            'password' => v::length(5, 64)->setName('パスワード')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```

**jqueryを使用してアクセス**

```js
$.ajax({
    url : 'http://127.0.0.1:8787',
    type : "post",
    dataType:'json',
    data : {nickname:'汤姆', username:'tom cat', password: '123456'}
});
```

結果：

`{"code":500,"msg":"ユーザ名 は英字（a-z）と数字（0-9）のみを含むことができます"}`

説明：

`v::input(array $input, array $rules)` はデータを検証・収集するために使用され、データの検証に失敗すると`Respect\Validation\Exceptions\ValidationException`例外がスローされ、検証に成功すると検証後のデータ（配列）が返されます。

ビジネスコードが検証例外をキャッチしない場合、webmanフレームワークは自動的に検証例外をキャッチし、HTTPリクエストヘッダに基づいてjsonデータ（`{"code":500,"msg":"xxx"}`のような）または通常の例外ページを返します。返される形式がビジネス要件に準拠しない場合、開発者は`ValidationException`例外をキャッチし、必要なデータを返すことができます。以下の例のように：

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
                'username' => v::alnum()->length(5, 64)->setName('ユーザ名'),
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

// 個々の規則の検証
$number = 123;
v::numericVal()->validate($number); // true

// 複数の規則の連鎖検証
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// 最初の検証失敗理由を取得する
try {
    $usernameValidator->setName('ユーザ名')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // ユーザ名 は英字（a-z）と数字（0-9）のみを含むことができます
}

// すべての検証失敗理由を取得する
try {
    $usernameValidator->setName('ユーザ名')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // 結果
    // -  ユーザ名 は次の条件を満たさなければなりません
    //     - ユーザ名 は英字（a-z）と数字（0-9）のみを含むことができます
    //     - ユーザ名 は空白を含めることはできません
  
    var_export($exception->getMessages());
    // 結果
    // array (
    //   'alnum' => 'ユーザ名 は英字（a-z）と数字（0-9）のみを含むことができます',
    //   'noWhitespace' => 'ユーザ名 は空白を含めることはできません',
    // )
}

// カスタマイズされたエラーメッセージ
try {
    $usernameValidator->setName('ユーザ名')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'ユーザ名は英字と数字のみを含むことができます',
        'noWhitespace' => 'ユーザ名に空白を含めることはできません',
        'length' => 'lengthは規則に従いますので、この項目は表示されません'
    ]);
    // 結果
    // array(
    //    'alnum' => 'ユーザ名は英字と数字のみを含むことができます',
    //    'noWhitespace' => 'ユーザ名に空白を含めることはできません'
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
    ->assert($data); // validate() や check() も使用できる
  
// オプショナル検証
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// 否定規則
v::not(v::intVal())->validate(10); // false
``` 

### Validatorの3つの方法 `validate()` `check()` `assert()` の違い

`validate()`はbooleanを返し、例外をスローしません

`check()`は検証に失敗すると例外をスローし、`$exception->getMessage()`を使用して最初の検証失敗理由を取得します

`assert()`は検証に失敗すると例外をスローし、`$exception->getFullMessage()`を使用してすべての検証失敗理由を取得できます
  
### よく使用される検証規則のリスト

`Alnum()` 英数字のみを含むかどうかを検証

`Alpha()` 文字のみを含むかどうかを検証

`ArrayType()` 配列型かどうかを検証

`Between(mixed $minimum, mixed $maximum)` 2つの値の間にあるかどうかを検証。

`BoolType()` ブール型かどうかを検証

`Contains(mixed $expectedValue)` 特定の値を含むかどうかを検証

`ContainsAny(array $needles)` 定義された値を少なくとも1つ含むかどうかを検証

`Digit()` 数字のみを含むかどうかを検証

`Domain()` 有効なドメイン名かどうかを検証

`Email()` 有効なメールアドレスかどうかを検証

`Extension(string $extension)` 拡張子を検証

`FloatType()` 浮動小数点数型かどうかを検証

`IntType()` 整数型かどうかを検証

`Ip()` IPアドレスかどうかを検証

`Json()` JSONデータかどうかを検証

`Length(int $min, int $max)` 長さが指定範囲内かどうかを検証

`LessThan(mixed $compareTo)` 指定値より小さいかどうかを検証

`Lowercase()` 小文字文字のみを含むかどうかを検証

`MacAddress()` MACアドレスかどうかを検証

`NotEmpty()` 空でないかどうかを検証

`NullType()` nullかどうかを検証

`Number()` 数値かどうかを検証

`ObjectType()` オブジェクトかどうかを検証

`StringType()` 文字列型かどうかを検証

`Url()` URLかどうかを検証

追加の検証規則については、https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ を参照してください
  

### 追加情報

https://respect-validation.readthedocs.io/en/2.0/ を訪問してください
