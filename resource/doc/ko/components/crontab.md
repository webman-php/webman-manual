# 크론탭 스케줄러 컴포넌트

## workerman/crontab

### 설명

`workerman/crontab`은 리눅스의 크론탭과 유사하지만 `workerman/crontab`은 초 단위 스케줄을 지원합니다.

시간 설명 :

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ 요일 (0 - 6) (일요일=0)
|   |   |   |   +------ 월 (1 - 12)
|   |   |   +-------- 월의 일 (1 - 31)
|   |   +---------- 시 (0 - 23)
|   +------------ 분 (0 - 59)
+-------------- 초 (0-59) [생략 가능, 첫번째 0이 없으면 최소 시간 단위는 분]
```

### 프로젝트 주소

https://github.com/walkor/crontab
  
### 설치
 
```php
composer require workerman/crontab
```
  
### 사용

**단계 1 : 프로세스 파일 `process/Task.php` 생성**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // 매 초마다 실행
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 5초마다 실행
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 분마다 실행
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 5분마다 실행
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // 매 분의 첫 초마다 실행
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // 매일 7시 50분에 실행, 여기서 초는 생략되었음에 주의
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```
  
**단계 2 : 프로세스 파일을 webman과 함께 시작하도록 설정**
  
구성 파일 `config/process.php`을 열고 다음 구성을 추가합니다.

```php
return [
    ....다른 설정들은 생략....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```
  
**단계 3 : webman을 다시 시작**

> 참고 : 예약된 작업은 즉시 실행되지 않으며 모든 예약된 작업은 그 다음 분에 실행됩니다.

### 설명
크론탭은 비동기적이지 않습니다. 예를 들어 한 작업 프로세스에 A와 B 두 개의 타이머가 모두 1초마다 작업을 실행하도록 설정되었지만 A 작업이 10초가 걸린다면 B는 A가 실행을 완료할 때까지 대기해야 하므로 B의 실행이 지연됩니다.
시간 간격에 민감한 업무라면 민감한 예약된 작업을 별도의 프로세스에 넣어서 실행하도록하여 다른 예약된 작업에 영향을 받지 않도록 해야 합니다. 예를 들어 다음과 같이 `config/process.php`를 구성합니다.

```php
return [
    ....다른 설정들은 생략....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
민감한 시간 예약 작업은 `process/Task1.php`에 넣고 다른 예약된 작업은 `process/Task2.php`에 넣습니다.

### 더 읽어보기
`config/process.php` 구성에 대한 더 많은 설명은 [Custom Processes](../process.md)를 참조하세요.
