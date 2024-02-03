# 실행 과정

## 프로세스 시작 과정

php start.php start를 실행하면 다음과 같은 프로세스가 실행됩니다.

1. 구성(config/) 디렉토리 아래의 구성을 로드합니다.
2. Worker의 관련 설정을 설정합니다. `pid_file`, `stdout_file`, `log_file`, `max_package_size` 등을 설정합니다.
3. webman 프로세스를 생성하고 포트(기본값 8787)를 수신 대기합니다.
4. 구성에 따라 사용자 정의 프로세스를 생성합니다.
5. webman 프로세스와 사용자 정의 프로세스가 시작된 후 아래 논리를 실행합니다(onWorkerStart 안에서 실행됨)：
  ①  `config/autoload.php`에 설정된 파일을 로드합니다. 예: `app/functions.php`
  ②  `config/middleware.php`(config/plugin/*/*/middleware.php를 포함)에 설정된 미들웨어를 로드합니다.
  ③  `config/bootstrap.php`(config/plugin/*/*/bootstrap.php를 포함)에 설정된 클래스의 start 메서드를 실행하여 일부 모듈을 초기화합니다. 예: Laravel 데이터베이스 초기화 연결
  ④  `config/route.php`(config/plugin/*/*/route.php를 포함)에 정의된 라우트를 로드합니다.

## 요청 처리 과정

1. 요청 URL이 public 디렉토리에 있는 정적 파일에 해당하는 지 여부를 확인하고 해당하는 경우 파일을 반환합니다(요청 종료). 해당하지 않는 경우 2로 이동합니다.
2. URL에 따라 특정 라우트가 일치하는 지 확인하고 일치하는 경우 4로 이동합니다. 일치하지 않는 경우 3로 이동합니다.
3. 기본 라우트가 비활성화되었는지 확인하고 비활성화된 경우 404를 반환합니다(요청 종료). 비활성화되지 않은 경우 4로 이동합니다.
4. 요청에 해당하는 컨트롤러의 미들웨어를 찾아 순서대로 미들웨어 전처리 작업(양파 모델의 요청 단계)을 실행하고 컨트롤러 비즈니스 로직을 실행한 후 미들웨어 후처리 작업(양파 모델의 응답 단계)을 실행하여 요청을 종료합니다. ([미들웨어 온양파 모델](https://www.workerman.net/doc/webman/middleware.html#%E4%B8%AD%E9%97%B4%E4%BB%B6%E6%B4%8B%E8%91%B1%E6%A8%A1%E5%9E%8B)참조)
