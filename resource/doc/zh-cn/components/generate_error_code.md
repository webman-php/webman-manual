# 自动生成错误码组件

## 说明

能够根据给定的规则自动维护错误码的生成。

> 约定返回数据中 code 参数，所有自定义的 code ，正数代表服务正常，负数代表服务异常。

## 项目地址

https://github.com/teamones-open/response-code-msg

## 安装

```php
composer require teamones/response-code-msg
```

## 使用

### 空 ErrorCode 类文件

- 文件路径 ./support/ErrorCode.php

```php
<?php
/**
 * 自动生成的文件 ,请不要手动修改.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### 配置文件

错误码会自动按照下面配置的参数自增生成，例如当前 system_number = 201，start_min_number = 10000，那么生成的第一个错误码就是 -20110001。

- 文件路径 ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode 类文件
    "root_path" => app_path(), // 当前代码根目录
    "system_number" => 201, // 系统标识
    "start_min_number" => 10000 // 错误码生成范围 例如 10000-99999
];
```

### 在start.php中增加启动自动生成错误码代码

- 文件路径 ./start.php

```php
// 放在 Config::load(config_path(), ['route', 'container']); 后面

// 生成错误码，仅APP_DEBUG模式下生成
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### 在代码中使用

下面代码中 **ErrorCode::ModelAddOptionsError** 为错误码, 其中 **ModelAddOptionsError** 需要用户自己根据当前需求语义化首字母大写去书写。

> 书写完你会发现是无法使用的，再下次重启后会自动生成对应错误码。注意有时候需要重启两次。

```php
<?php
/**
 * 导航相关操作 service 类
 */

namespace app\service;

use app\model\Demo as DemoModel;

// 引入ErrorCode类文件
use support\ErrorCode;

class Demo
{
    /**
     * 添加
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
            // 输出错误信息
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### 生成后的 ./support/ErrorCode.php 文件

```php
<?php
/**
 * 自动生成的文件 ,请不要手动修改.
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



