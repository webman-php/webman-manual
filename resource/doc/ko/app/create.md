# 앱 플러그인 생성

## 고유 식별자

각 플러그인에는 고유한 앱 식별자가 있으며, 개발자는 개발하기 전에 식별자를 결정하고 식별자가 이미 사용 중이 아닌지 확인해야 합니다.
확인 주소: [앱 식별자 확인](https://www.workerman.net/app/check)

## 생성

`composer require webman/console` 명령을 실행하여 webman 명령줄을 설치합니다.

다음 명령을 사용하여 로컬에 앱 플러그인을 생성할 수 있습니다.

예: `php webman app-plugin:create {플러그인 식별자}`

예: `php webman app-plugin:create foo`

웹맨을 다시 시작합니다.

`http://127.0.0.1:8787/app/foo`를 방문하여 내용이 반환되면 생성이 성공한 것입니다.
