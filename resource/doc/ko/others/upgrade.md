# 업그레이드 방법

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **참고**
> 알리클라우드의 컴포저 프록시가 현재 컴포저 공식 소스로부터 데이터를 동기화하지 않기 때문에 최신 webman으로 업그레이드할 수 없습니다. 대신 다음 명령을 사용하여 공식 데이터 소스를 복원하세요: `composer config -g --unset repos.packagist`
