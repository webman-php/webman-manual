# 앱 플러그인 만들기

## 고유 식별자

각 플러그인에는 고유한 앱 식별자가 있으며, 개발자는 개발하기 전에 식별자를 결정하고 해당 식별자가 이미 사용 중이 아닌지 확인해야합니다.
확인 주소 : [앱 식별자 확인](https://www.workerman.net/app/check)

## 생성

`composer require webman/console` 명령을 실행하여 webman 명령줄을 설치하십시오.

명령어 `php webman app-plugin:create {플러그인 식별자}`를 사용하여 로컬에서 앱 플러그인을 생성할 수 있습니다.

예 : `php webman app-plugin:create foo`

webman을 다시 시작하십시오.

`http://127.0.0.1:8787/app/foo`에 액세스하면 내용이 반환되면 성공적으로 생성되었음을 의미합니다.
