# 인증 코드 관련 구성 요소

## webman/captcha
프로젝트 주소 https://github.com/webman-php/captcha

### 설치
```
composer require webman/captcha
```

### 사용

**`app/controller/LoginController.php` 파일 생성**

```php
<?php
namespace app\controller;

use support\Request;
use Webman\Captcha\CaptchaBuilder;

class LoginController
{
    /**
     * 테스트 페이지
     */
    public function index(Request $request)
    {
        return view('login/index');
    }
    
    /**
     * 인증 코드 이미지 출력
     */
    public function captcha(Request $request)
    {
        // 인증 코드 클래스 초기화
        $builder = new CaptchaBuilder;
        // 인증 코드 생성
        $builder->build();
        // 인증 코드 값 세션에 저장
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // 인증 코드 이미지 이진 데이터 가져오기
        $img_content = $builder->get();
        // 인증 코드 이진 데이터 출력
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }

    /**
     * 인증 코드 확인
     */
    public function check(Request $request)
    {
        // 포스트 요청에서 captcha 필드 가져오기
        $captcha = $request->post('captcha');
        // 세션에서 captcha 값 비교
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => '입력한 인증 코드가 올바르지 않습니다']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

**`app/view/login/index.html` 템플릿 파일 생성**

```html
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>인증 코드 테스트</title>  
</head>
<body>
    <form method="post" action="/login/check">
       <img src="/login/captcha" /><br>
        <input type="text" name="captcha" />
        <input type="submit" value="제출" />
    </form>
</body>
</html>
```

`http://127.0.0.1:8787/login` 페이지로 이동하면 다음과 같이 보입니다:
  ![](../../assets/img/captcha.png)

### 일반적인 매개변수 설정
```php
    /**
     * 인증 코드 이미지 출력
     */
    public function captcha(Request $request)
    {
        // 인증 코드 클래스 초기화
        $builder = new CaptchaBuilder;
        // 인증 코드 길이
        $length = 4;
        // 포함할 문자
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
        // PhraseBuilder를 사용하여 새로운 CaptchaBuilder 생성
        $builder = new PhraseBuilder($length, $chars);
        $captcha = new CaptchaBuilder(null, $builder);
        // 인증 코드 생성
        $builder->build();
        // 인증 코드 값 세션에 저장
        $request->session()->set('captcha', strtolower($builder->getPhrase()));
        // 인증 코드 이미지 이진 데이터 가져오기
        $img_content = $builder->get();
        // 인증 코드 이진 데이터 출력
        return response($img_content, 200, ['Content-Type' => 'image/jpeg']);
    }
```

더 많은 API 및 매개변수에 대한 정보는 https://github.com/webman-php/captcha 를 참조하세요.
