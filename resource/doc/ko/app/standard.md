# 응용 프로그램 플러그인 개발 규격

## 응용 프로그램 플러그인 요구 사항
* 플러그인은 저작권 침해 코드, 아이콘, 이미지 등을 포함해서는 안 됩니다.
* 플러그인 소스 코드는 완전한 코드여야하며 암호화해서는 안 됩니다.
* 플러그인은 완전한 기능을야하며 간단한 기능이 아니어야 합니다.
* 완전한 기능 소개 및 문서를 제공해야 합니다.
* 플러그인에는 하위 시장을 포함해서는 안 됩니다.
* 플러그인 내에는 어떠한 텍스트나 홍보 링크도 포함해서는 안 됩니다.

## 응용 프로그램 플러그인 식별
각 응용 프로그램 플러그인은 문자로 된 고유한 식별자를 갖습니다. 이 식별자는 알파벳으로 이루어져 있습니다. 이 식별자는 응용 프로그램 플러그인이 위치한 소스 코드 디렉토리 이름, 클래스 네임스페이스, 플러그인 데이터베이스 테이블 접두어에 영향을 미칩니다.

개발자가 'foo'를 플러그인 식별자로 사용하는 경우, 플러그인 소스 코드의 디렉토리는 `{main_project}/plugin/foo`가 되며, 해당 플러그인의 네임스페이스는 `plugin\foo`가 되고, 테이블 접두어는 `foo_`가 됩니다.

이 식별자는 전세계적으로 고유하기 때문에, 개발자는 개발을 시작하기 전에 식별자의 사용 가능 여부를 확인해야 합니다. 확인은 [애플리케이션 식별 확인](https://www.workerman.net/app/check)을 통해 할 수 있습니다.

## 데이터베이스
* 테이블 이름은 소문자 알파벳 `a-z`와 밑줄 `_`로 구성되어야 합니다.
* 플러그인 데이터 테이블은 플러그인 식별자를 접두어로 사용해야 합니다. 예를 들어 'foo' 플러그인의 article 테이블은 `foo_article`이어야 합니다.
* 테이블 기본 키는 index여야 합니다.
* 저장 엔진은 모두 innodb 엔진을 사용해야 합니다.
* 문자 세트는 모두 utf8mb4_general_ci를 사용해야 합니다.
* 데이터베이스 ORM은 laravel 또는 think-orm을 사용할 수 있습니다.
* 시간 필드는 DateTime을 권장합니다.

## 코드 규격

#### PSR 규격
코드는 PSR4로드 규격을 준수해야 합니다.

#### 대문자로 시작하는 카멜 케이스 클래스 및 속성 이름
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### 소문자로 시작하는 카멜 케이스 속성 및 메소드 이름
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * 인증이 필요없는 메소드
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * 댓글 가져오기
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### 주석
클래스 속성 및 함수는 요약, 매개변수, 반환 유형을 포함해야 합니다.

#### 들여쓰기
들여쓰기는 4개의 공백으로 하고 탭을 사용해서는 안 됩니다.

#### 흐름 제어
흐름 제어 키워드(if for while foreach 등) 뒤에는 공백이 한 칸 있어야 하고, 흐름 제어 코드 시작 중괄호는 종료 괄호와 같은 줄에 있어야 합니다.
```php
foreach ($users as $uid => $user) {

}
```

#### 임시 변수 이름
소문자로 시작하는 카멜 케이스 이름을 권장합니다(강제는 아님)
```php
$articleCount = 100;
```
