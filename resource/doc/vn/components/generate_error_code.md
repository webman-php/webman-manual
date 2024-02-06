# Component Generating Error Codes

## Miêu tả

Có thể tự động duy trì việc tạo mã lỗi dựa trên các quy tắc cụ thể được cung cấp.

> Thỏa thuận rằng tham số mã code được trả về trong dữ liệu, tất cả các mã code tùy chỉnh, số dương đại diện cho dịch vụ bình thường, số âm đại diện cho sự cố dịch vụ.

## Liên kết dự án

https://github.com/teamones-open/response-code-msg

## Cài đặt

```php
composer require teamones/response-code-msg
```

## Sử dụng

### File lớp ErrorCode trống

- Đường dẫn tập tin: ./support/ErrorCode.php

```php
<?php
/**
 * Tập tin được tạo tự động , xin đừng chỉnh sửa thủ công.
 * @Tác giả:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Tập tin cấu hình

Mã lỗi sẽ tự động tăng theo cấu hình được chỉ định dưới đây, ví dụ number_system = 201, start_min_number = 10000, vậy nên mã lỗi đầu tiên được tạo ra sẽ là -20110001.

- Đường dẫn tập tin: ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // File lớp ErrorCode
    "root_path" => app_path(), // Thư mục gốc mã nguồn hiện tại
    "system_number" => 201, // Định danh hệ thống
    "start_min_number" => 10000 // Phạm vi tạo mã lỗi, ví dụ 10000-99999
];
```

### Thêm mã code tự động vào start.php để khởi động

- Đường dẫn tập tin: ./start.php

```php
// Đặt sau Config::load(config_path(), ['route', 'container']);

// Tạo mã lỗi, chỉ tạo ở chế độ APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Sử dụng trong mã nguồn

Trong đoạn mã dưới đây, **ErrorCode::ModelAddOptionsError** là mã lỗi, trong đó **ModelAddOptionsError** cần người dùng tự viết theo nhu cầu hiện tại và bắt đầu với chữ cái viết hoa.

> Sau khi viết xong, bạn sẽ thấy rằng không thể sử dụng mã lỗi này ngay lập tức, mã lỗi sẽ được tạo ra sau lần khởi động tiếp theo. Chú ý rằng đôi khi cần khởi động hai lần.

```php
<?php
/**
 * Lớp dịch vụ liên quan đến điều hướng
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Nhập lớp ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * Thêm
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
            // Xuất thông tin lỗi
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Tập tin ./support/ErrorCode.php sau khi được tạo ra

```php
<?php
/**
 * Tập tin được tạo tự động , xin đừng chỉnh sửa thủ công.
 * @Tác giả:$Id$
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
