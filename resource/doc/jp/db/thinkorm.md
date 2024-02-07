## ThinkORM

### ThinkORMのインストール

`composer require -W webman/think-orm`

インストール後はrestart再起動が必要です（reloadは無効です）

> **ヒント**
> インストールに失敗した場合、おそらくcomposerプロキシを使用しているためです。`composer config -g --unset repos.packagist` を実行してcomposerプロキシを解除してみてください。

> [webman/think-orm](https://www.workerman.net/plugin/14) は、実際には`toptink/think-orm`を自動的にインストールするプラグインです。webmanのバージョンが`1.2`未満の場合、このプラグインを使用できません。その場合は、[手動でthink-ormをインストールして設定する](https://www.workerman.net/a/1289) 記事を参照してください。

### 設定ファイル
実際の状況に応じて`config/thinkorm.php` 設定ファイルを修正してください。

### 使用方法

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Db;

class FooController
{
    public function get(Request $request)
    {
        $user = Db::table('user')->where('uid', '>', 1)->find();
        return json($user);
    }
}
```

### モデルの作成

ThinkOrmモデルは `think\Model` を継承します。以下のようになります。
```
<?php
namespace app\model;

use think\Model;

class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $pk = 'id';

    
}
```

また、以下のコマンドを使用してthinkormベースのモデルを作成することもできます。
```
php webman make:model テーブル名
```

> **ヒント**
> このコマンドを使用するには、`webman/console` をインストールする必要があります。インストールコマンドは`composer require webman/console ^1.2.13`です。

> **注意**
> make:model コマンドは、親プロジェクトが`illuminate/database`を使用していることが検出された場合には、`illuminate/database`ベースのモデルファイルを作成し、thinkormベースのモデルではありません。この場合は、追加パラメータtpを使用して強制的にthink-ormのモデルを生成することができます。コマンドは次のようになります： `php webman make:model テーブル名 tp`（機能しない場合は`webman/console`をアップグレードしてください）。
