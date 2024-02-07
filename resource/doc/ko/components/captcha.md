# 인증 코드 관련 구성 요소

## webman/captcha
프로젝트 주소 https://github.com/webman-php/captcha

### 설치
```composer require webman/captcha```

### 사용

** 파일 만들기 `app/controller/LoginController.php`**

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
        // 세션에 있는 captcha 값과 비교
        if (strtolower($captcha) !== $request->session()->get('captcha')) {
            return json(['code' => 400, 'msg' => '잘못된 인증 코드 입력']);
        }
        return json(['code' => 0, 'msg' => 'ok']);
    }

}
```

** 템플릿 파일 만들기 `app/view/login/index.html`**

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

`http://127.0.0.1:8787/login` 페이지로 이동하면 아래와 같은 화면이 나타납니다:
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
        // 어떤 문자가 포함되는지
        $chars = '0123456789abcefghijklmnopqrstuvwxyz';
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

더 많은 API와 매개변수에 대한 정보는 https://github.com/webman-php/captcha 를 참조하세요.
