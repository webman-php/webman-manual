## ThinkCache

### ThinkCache 설치

`composer require -W webman/think-cache`

설치 후에는 restart를 해야 함(reload는 적용되지 않음)

> [webman/think-cache](https://www.workerman.net/plugin/15)은 사실상 `toptink/think-cache`를 자동으로 설치하는 플러그인입니다.

> **주의**
> toptink/think-cache는 PHP 8.1을 지원하지 않습니다.

### 설정 파일

설정 파일은 `config/thinkcache.php`에 있습니다.

### 사용

```php
<?php
namespace app\controller;

use support\Request;
use think\facade\Cache;

class UserController
{
    public function db(Request $request)
    {
        $key = 'test_key';
        Cache::set($key, rand());
        return response(Cache::get($key));
    }
}
```

### Think-Cache 사용 문서

[ThinkCache 문서 링크](https://github.com/top-think/think-cache)
