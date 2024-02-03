# 오류 코드 컴포넌트 자동 생성

## 설명

지정된 규칙에 따라 오류 코드를 자동으로 유지보수할 수 있습니다.

> 반환 데이터의 코드 매개변수가 규정되어 있으며, 모든 사용자 정의 코드는 양수는 서비스 정상을 나타내고, 음수는 서비스 이상을 나타냅니다.

## 프로젝트 주소

https://github.com/teamones-open/response-code-msg

## 설치

```php
composer require teamones/response-code-msg
```

## 사용

### Empty ErrorCode class file

- 파일 경로 ./support/ErrorCode.php

```php
<?php
/**
 * 자동 생성된 파일, 수동으로 수정하지 마십시오.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### 설정 파일

오류 코드는 자동으로 아래의 설정 매개변수에 따라 증분적으로 생성됩니다. 예를 들어, 현재 시스템 번호(system_number)가 201이고, start_min_number가 10000이면, 생성된 첫 번째 오류 코드는 -20110001입니다.

- 파일 경로 ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode 클래스 파일
    "root_path" => app_path(), // 현재 코드 루트 디렉토리
    "system_number" => 201, // 시스템 식별자
    "start_min_number" => 10000 // 오류 코드 생성 범위 예: 10000-99999
];
```

### start.php에 자동 오류 코드 생성을 시작하는 코드 추가

- 파일 경로 ./start.php

```php
// Config::load(config_path(), ['route', 'container']) 이후에 배치

// 오류 코드 생성, 오직 APP_DEBUG 모드에서만 생성
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### 코드에서 사용

다음 코드에서 **ErrorCode::ModelAddOptionsError**는 오류 코드이며, **ModelAddOptionsError**는 사용자가 현재 요구에 따라 의미론적으로 대문자로 작성해야 합니다.

> 작성이 완료되면 사용할 수 없으며, 다음 번 재시작 후에 해당 오류 코드가 자동으로 생성됩니다. 때로는 두 번 재부팅해야 할 수도 있습니다.

```php
<?php
/**
 * 네비게이션 관련 작업 서비스 클래스
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCode 클래스 파일 포함
use support\ErrorCode;

class Demo
{
    /**
     * 추가
     * @param $data
     * @return array|mixed
     * @throws \exception
     */
    public function add($data): array
    {
        try {
            $demo = new DemoModel();
            foreach ($data as $key => $value) {
                $demo->$key = $value;
            }

            $demo->save();

            return $demo->getData();
        } catch (\Throwable $e) {
            // 오류 정보 출력
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### 생성 후의 ./support/ErrorCode.php 파일

```php
<?php
/**
 * 자동 생성된 파일, 수동으로 수정하지 마십시오.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
    const LoginNameOrPasswordError = -20110001;
    const UserNotExist = -20110002;
    const TokenNotExist = -20110003;
    const InvalidToken = -20110004;
    const ExpireToken = -20110005;
    const WrongToken = -20110006;
    const ClientIpNotEqual = -20110007;
    const TokenRecordNotFound = -20110008;
    const ModelAddUserError = -20110009;
    const NoInfoToModify = -20110010;
    const OnlyAdminPasswordCanBeModified = -20110011;
    const AdminAccountCannotBeDeleted = -20110012;
    const DbNotExist = -20110013;
    const ModelAddOptionsError = -20110014;
    const UnableToDeleteSystemConfig = -20110015;
    const ConfigParamKeyRequired = -20110016;
    const ExpiryCanNotGreaterThan7days = -20110017;
    const GetPresignedPutObjectUrlError = -20110018;
    const ObjectStorageConfigNotExist = -20110019;
    const UpdateNavIndexSortError = -20110020;
    const TagNameAttNotExist = -20110021;
    const ModelUpdateOptionsError = -20110022;
}
```
