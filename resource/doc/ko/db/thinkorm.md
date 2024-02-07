## ThinkORM

### ThinkORM 설치

`composer require -W webman/think-orm`

설치 후에는 restart 재시작해야 함(reload는 유효하지 않음)

> **팁**
> 설치에 실패한 경우 composer 프록시를 사용했을 수 있습니다. `composer config -g --unset repos.packagist` 명령을 실행하여 composer 프록시를 취소해 보십시오.

> [webman/think-orm](https://www.workerman.net/plugin/14)은 실제로`toptink/think-orm`을 자동으로 설치하는 플러그인입니다. 웹맨 버전이 `1.2`보다 낮으면 플러그인을 사용할 수 없으므로 [수동으로 think-orm을 설치하고 구성하는 방법](https://www.workerman.net/a/1289)을 참조하십시오.

### 구성 파일
실제 상황에 맞게 구성 파일 `config/thinkorm.php`을 수정합니다.

### 사용법

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

ThinkOrm 모델은 `think\Model`을 상속받습니다. 다음과 같이 유사합니다.
```php
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

아래 명령을 사용하여 thinkorm을 기반으로 모델을 생성할 수도 있습니다.
```php
php webman make:model 테이블명
```

> **팁**
> 이 명령은 `webman/console`을 설치해야 합니다. 설치 명령은 `composer require webman/console ^1.2.13` 입니다.

> **주의**
> 만약 make:model 명령이 주 프로젝트에서 `illuminate/database`를 사용하는 것을 감지하면, thinkorm이 아닌 `illuminate/database`를 기반으로 모델 파일을 생성하게 됩니다. 이 경우에는 추가 매개변수 tp를 사용하여 think-orm의 모델을 강제로 생성할 수 있습니다. 명령은 다음과 같습니다. `php webman make:model 테이블명 tp` (적용되지 않으면 `webman/console`을 업그레이드하십시오)
