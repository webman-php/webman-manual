# 로깅
로깅 클래스의 사용법은 데이터베이스 사용법과 유사합니다.
```php
use support\Log;
Log::channel('plugin.admin.default')->info('테스트');
```

만약 주 프로젝트의 로그 설정을 재사용하고 싶다면, 아래와 같이 사용할 수 있습니다.
```php
use support\Log;
Log::info('로그 내용');
// 주 프로젝트에 'test' 로그 설정이 있다고 가정한다면
Log::channel('test')->info('로그 내용');
```
