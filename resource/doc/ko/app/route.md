## 라우팅 구성 파일
플러그인의 라우팅 구성 파일은 `plugin/플러그인명/config/route.php`에 있습니다.

## 기본 라우팅
플러그인 애플리케이션의 URL 주소 경로는 모두 `/app`로 시작합니다. 예를 들어 `plugin\foo\app\controller\UserController`의 URL 주소는 `http://127.0.0.1:8787/app/foo/user`입니다.

## 기본 라우팅 비활성화
특정 애플리케이션 플러그인의 기본 라우팅을 비활성화하려면 라우팅 구성에서 다음과 유사한 것을 설정하십시오.
```php
Route::disableDefaultRoute('foo');
```

## 404 오류 콜백 처리
특정 애플리케이션 플러그인에 대한 대체 처리를 설정하려면 두 번째 매개변수를 통해 플러그인 이름을 전달해야 합니다. 예를 들어,
```php
Route::fallback(function(){
    return redirect('/');
}, 'foo');
```
