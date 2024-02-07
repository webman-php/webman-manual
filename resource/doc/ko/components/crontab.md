# crontab 스케줄러 구성 요소

## workerman/crontab

### 설명

`workerman/crontab`은 리눅스의 crontab과 유사하지만, `workerman/crontab`은 초 단위 스케줄을 지원합니다.

시간 설명:

```plaintext
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ 요일 (0 - 6) (일요일=0)
|   |   |   |   +------ 달 (1 - 12)
|   |   |   +-------- 월의 날짜 (1 - 31)
|   |   +---------- 시간 (0 - 23)
|   +------------ 분 (0 - 59)
+-------------- 초 (0-59) [생략 가능, 0부터 없을 경우 최소 시간 단위는 분]
```

### 프로젝트 주소

https://github.com/walkor/crontab

### 설치

```php
composer require workerman/crontab
```

### 사용

**단계 1: 프로세스 파일 `process/Task.php` 작성**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
        // 매 초 실행
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 5초 실행
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 분 실행
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 5분 실행
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 분 1초에 실행
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // 매일 7시 50분 실행, 여기서 초는 생략됨
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
    }
}
```

**단계 2: 웹맨과 함께 프로세스 파일 구성**

`config/process.php` 파일을 열고 다음과 같이 설정 추가

```php
return [
    ....기타 구성은 생략....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**단계 3: 웹맨 재시작**

> 참고: 스케줄러가 즉시 실행되지 않으며, 모든 스케줄러는 다음 분에 실행됩니다.

### 설명
crontab은 비동기적이지 않습니다. 예를 들어 task 프로세스에 A와 B 두 개의 스케줄러가 모두 1초마다 작업을 수행하더라도 A 작업에 10초가 걸린다면, B는 A 작업이 완료될 때까지 대기해야 하므로 B의 실행이 지연됩니다.
시간 간격에 민감한 비즈니스의 경우 민감한 스케줄러를 별도의 프로세스로 실행하여 다른 스케줄러에 영향을 받지 않도록 해야 합니다. 예를 들어, `config/process.php`를 다음과 같이 구성합니다.

```php
return [
    ....기타 구성은 생략....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```

민감한 시간 간격의 스케줄러를 `process/Task1.php`에, 다른 스케줄러를 `process/Task2.php`에 넣습니다.

### 더 보기
더 많은 `config/process.php` 구성 정보는 [Custom Processes](../process.md)를 참조하십시오.
