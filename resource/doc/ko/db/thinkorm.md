## ThinkORM

### ThinkORM 설치

`composer require -W webman/think-orm`

설치 후에는 다시 시작해야 합니다(reload는 작동하지 않음)

> **팁**
> 만약 설치에 실패한다면, composer 프록시를 사용하고 있을 수 있습니다, `composer config -g --unset repos.packagist` 명령어를 실행하여 composer 프록시를 해제해보세요.

> [webman/think-orm](https://www.workerman.net/plugin/14)은 사실상 `toptink/think-orm`을 자동으로 설치하는 플러그인입니다. 만약 webman 버전이 `1.2`보다 낮다면 플러그인을 사용할 수 없으므로 [수동으로 think-orm 설치 및 구성](https://www.workerman.net/a/1289) 문서를 참조하십시오.

### 설정 파일
실제 상황에 맞게 설정 파일 `config/thinkorm.php`를 수정합니다.

### 사용

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

### 모델 생성

ThinkOrm 모델은 `think\Model`을 상속받아 만듭니다. 아래와 같이 작성됩니다.
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

또한 다음 명령어를 사용하여 thinkorm을 기반으로 모델을 생성할 수 있습니다.
```
php webman make:model 테이블명
```

> **팁**
> 이 명령어를 사용하려면 `webman/console`을 설치해야 합니다. 설치 명령어는 `composer require webman/console ^1.2.13` 입니다.

> **주의**
> make:model 명령어는 주 프로젝트가 `illuminate/database`를 사용하는 것을 감지하면 `illuminate/database`를 기반으로 모델 파일을 생성합니다. 이럴 경우 tp라는 추가 매개변수를 사용하여 강제로 think-orm 모델을 생성할 수 있습니다. 명령어는 다음과 같습니다. `php webman make:model 테이블명 tp` (작동하지 않는 경우 `webman/console`를 업그레이드 하십시오)
