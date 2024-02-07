# 自動生成錯誤碼組件

## 說明

能夠根據給定的規則自動維護錯誤碼的生成。

> 約定返回數據中 code 參數，所有自定義的 code ，正數代表服務正常，負數代表服務異常。

## 專案地址

https://github.com/teamones-open/response-code-msg

## 安裝

```php
composer require teamones/response-code-msg
```

## 使用

### 空 ErrorCode 類文件

- 文件路徑 ./support/ErrorCode.php

```php
<?php
/**
 * 自动生成的文件 ,請不要手動修改.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### 配置文件

錯誤碼會自動按照下面配置的參數自增生成，例如當前 system_number = 201，start_min_number = 10000，那麼生成的第一個錯誤碼就是 -20110001。

- 文件路徑 ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode 類文件
    "root_path" => app_path(), // 當前代碼根目錄
    "system_number" => 201, // 系統標識
    "start_min_number" => 10000 // 錯誤碼生成範圍 例如 10000-99999
];
```

### 在 start.php 中增加啟動自动生成錯誤碼代碼

- 文件路徑 ./start.php

```php
// 放在 Config::load(config_path(), ['route', 'container']); 後面

// 生成錯誤碼，僅APP_DEBUG模式下生成
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### 在代碼中使用

下面代碼中 **ErrorCode::ModelAddOptionsError** 為錯誤碼, 其中 **ModelAddOptionsError** 需要用戶自己根據當前需求語義化首字母大寫去書寫。

> 書寫完你會發現是無法使用的，再次重啟後會自动生成對應錯誤碼。注意有時候需要重啟兩次。

```php
<?php
/**
 * 導航相關操作 service 類
 */

namespace app\service;

use app\model\Demo as DemoModel;

// 引入ErrorCode類文件
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
            // 輸出錯誤信息
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### 生成後的 ./support/ErrorCode.php 文件

```php
<?php
/**
 * 自动生成的文件 ,請不要手動修改.
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
