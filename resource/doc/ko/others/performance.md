# webman 성능

### 전통적인 프레임워크 요청 처리 과정

1. nginx/apache가 요청을 받음
2. nginx/apache가 요청을 php-fpm에 전달
3. php-fpm이 환경을 초기화하여 변수 목록을 생성
4. php-fpm이 각 확장/모듈의 RINIT을 호출
5. php-fpm이 디스크에서 php 파일을 읽음 (opcache를 사용하여 피함 가능)
6. php-fpm이 어휘 분석, 구문 분석 및 opcodes로 컴파일 (opcache를 사용하여 피함 가능)
7. php-fpm이 opcodes 실행, 8. 9. 10. 11을 포함함
8. 프레임워크 초기화, 예를 들어 컨테이너, 컨트롤러, 라우터, 미들웨어 등의 클래스를 인스턴스화
9. 프레임워크가 데이터베이스에 연결하고 권한을 확인하며 레디스에 연결
10. 프레임워크가 비즈니스 로직을 실행
11. 프레임워크가 데이터베이스와 레디스 연결을 닫음
12. php-fpm이 리소스를 해제하고 모든 클래스 정의, 인스턴스, 기호 테이블을 파괴
13. php-fpm이 각 확장/모듈의 RSHUTDOWN 메서드를 순서대로 호출
14. php-fpm이 결과를 nginx/apache에 전달
15. nginx/apache가 결과를 클라이언트에 반환

### webman의 요청 처리 과정
1. 프레임워크가 요청을 받음
2. 프레임워크가 비즈니스 로직을 실행
3. 프레임워크가 결과를 클라이언트에 반환

맞습니다, nginx에서 프록시를 사용하지 않는 경우에는 프레임워크에는 이러한 3단계만 있습니다. 이것은 이미 PHP 프레임워크의 극한이라고 말할 수 있습니다. 이로 인해 webman의 성능은 전통적인 프레임워크의 수십 배 이상입니다.

더 많은 정보는 [성능 테스트](benchmarks.md)를 참조하세요.
