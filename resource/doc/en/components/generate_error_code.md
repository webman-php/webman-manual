# Automatic Error Code Generator

## Description

This component is able to automatically generate error codes based on given rules.

> The "code" parameter in the returned data is conventionally used, where all custom codes are positive numbers representing a functioning service, while negative numbers represent a service exception.

## Project Repository

https://github.com/teamones-open/response-code-msg

## Installation

```php
composer require teamones/response-code-msg
```

## Usage

### Empty ErrorCode Class File

- File path: ./support/ErrorCode.php

```php
<?php
/**
 * Automatically generated file, please do not modify manually.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Configuration File

The error codes will be automatically generated based on the following configuration parameters. For example, if the system_number is 201 and the start_min_number is 10000, the first generated error code will be -20110001.

- File path: ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode class file
    "root_path" => app_path(), // Current code root directory
    "system_number" => 201, // System identifier
    "start_min_number" => 10000 // Error code range (e.g. 10000-99999)
];
```

### Add Auto-generate Error Code Code in start.php

- File path: ./start.php

```php
// Place it after Config::load(config_path(), ['route', 'container']);

// Generate error codes, only in APP_DEBUG mode
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Usage in Code

In the following code, **ErrorCode::ModelAddOptionsError** is the error code, where **ModelAddOptionsError** needs to be written by the user based on the semantic meaning with capitalization for the first letter.

> After writing, you will find that it cannot be used. It will be generated after the next restart. Note that sometimes it needs to be restarted twice.

```php
<?php
/**
 * Navigation-related operation service class
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Import ErrorCode class file
use support\ErrorCode;

class Demo
{
    /**
     * Add
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
            // Output error message
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Generated ./support/ErrorCode.php File

```php
<?php
/**
 * Automatically generated file, please do not modify manually.
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
