# 실행 프로세스

## 프로세스 시작 흐름

`php start.php start`를 실행한 후의 실행 흐름은 다음과 같습니다.

1. config/ 아래의 설정을 로드합니다.
2. Worker의 관련 설정을 설정합니다. 예: `pid_file`, `stdout_file`, `log_file`, `max_package_size` 등
3. webman 프로세스를 생성하고 포트를 듣습니다(기본 8787).
4. 설정에 따라 사용자 지정 프로세스를 생성합니다.
5. webman 프로세스 및 사용자 지정 프로세스가 시작된 후에 다음 로직을 실행합니다(모두 onWorkerStart에서 실행됨)：
   ① `config/autoload.php`에 설정된 파일을 로드합니다. 예: `app/functions.php`
   ② `config/middleware.php` (포함된 `config/plugin/*/*/middleware.php`)에 설정된 미들웨어를 로드합니다.
   ③ `config/bootstrap.php` (포함된 `config/plugin/*/*/bootstrap.php`)에 설정된 클래스의 start 메소드를 실행하여 일부 모듈을 초기화합니다. 예: 라라벨 데이터베이스 초기화 연결
   ④ `config/route.php` (포함된 `config/plugin/*/*/route.php`)에 정의된 라우트를 로드합니다.

## 요청 처리 흐름
1. 요청 URL이 public 아래의 정적 파일에 해당하는지 여부를 확인하고 해당되면 파일을 반환합니다(요청 종료). 해당되지 않으면 2로 이동합니다.
2. URL에 따라 특정 라우트에 일치하는지 확인하고 미치하는 경우 4로 이동하고, 미치하지 않으면 3로 이동합니다.
3. 기본 라우트가 닫혀 있는지 확인하고 닫혀 있다면 404를 반환합니다(요청 종료). 닫혀 있지 않다면 4로 이동합니다.
4. 요청에 해당하는 컨트롤러의 미들웨어를 찾아 미들웨어의 전 처리를 순서대로 실행합니다(양파 모델 요청 단계), 컨트롤러 업무 로직을 실행하고, 미들웨어의 후 처리를 실행합니다(양파 모델 응답 단계)하여 요청을 종료합니다. (참고: [미들웨어 양파 모델](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B))
