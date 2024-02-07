# 모델 관계

이 문서는 Webman의 모델 관계에 대한 자세한 내용을 담고 있습니다.

## 1. 일대다 관계

일대다 관계는 한 모델이 여러 다른 모델을 가질 수 있는 관계를 의미합니다. 우리는 다음과 같이 모델 간의 관계를 설정할 수 있습니다.

```php
use Workerman\Model;

class User extends Model
{
    public function posts()
    {
        return $this->hasMany('App\Models\Post');
    }
}
```

위의 코드에서 `User` 모델은 여러 개의 `Post` 모델을 가지고 있습니다. 

## 2. 일대일 관계

일대일 관계는 한 모델이 다른 하나의 모델과 일대일로 연결되어 있는 관계를 의미합니다.

```php
use Workerman\Model;

class User extends Model
{
    public function phone()
    {
        return $this->hasOne('App\Models\Phone');
    }
}
```

위의 코드에서 `User` 모델은 하나의 `Phone` 모델과 연결되어 있습니다.

## 3. 다대다 관계

다대다 관계는 서로 다른 모델 간에 다대다로 연결되어 있는 관계를 의미합니다.

```php
use Workerman\Model;

class User extends Model
{
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role');
    }
}
```

위의 코드에서 `User` 모델은 여러 개의 `Role` 모델과 다대다로 연결되어 있습니다.

이처럼 Webman은 모델 간의 다양한 관계를 쉽게 설정할 수 있도록 지원하고 있습니다. 자세한 내용은 공식 문서를 참조하시기 바랍니다.
