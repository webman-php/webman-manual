＃自動エラーコードコンポーネント

## 説明

与えられた規則に従って自動的にエラーコードを維持できます。

> 返されるデータには、コードパラメータが含まれており、すべてのカスタムコードは、正の値はサービスが正常であることを示し、負の値はサービスの異常を示します。

## プロジェクトのアドレス

https://github.com/teamones-open/response-code-msg

## インストール

```php
composer require teamones/response-code-msg
```

## 使用法

### 空のErrorCodeクラスファイル

- ファイルパス ./support/ErrorCode.php

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

### 設定ファイル

エラーコードは、次の構成パラメータに従って自動的に増分生成されます。たとえば、現在の system_number = 201、start_min_number = 10000 ならば、生成される最初のエラーコードは-20110001 です。

- ファイルパス ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode クラスファイル
    "root_path" => app_path(), // 現在のコードのルートディレクトリ
    "system_number" => 201, // システム識別子
    "start_min_number" => 10000 // エラーコード生成範囲 例：10000-99999
];
```

### start.php にエラーコード自動生成の起動コードを追加

- ファイルパス ./start.php

```php
// Config::load(config_path(), ['route', 'container']); の後に配置

// エラーコードを生成する、APP_DEBUGモードでのみ生成
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### コード内での使用

以下のコード **ErrorCode::ModelAddOptionsError** はエラーコードであり、その中の **ModelAddOptionsError** は、ユーザーが現在の要求に基づいて助动生成されたエラーコードを意味ありげな最初の文字を大文字で書く必要があります。

> 書き終わると、使用できないことに気づきますが、次回の再起動後に対応するエラーコードが自動的に生成されます。時々、2回再起動する必要があります。

```php
<?php
/**
 * ナビゲーション関連の操作サービスクラス
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCodeクラスファイルをインポート
use support\ErrorCode;

class Demo
{
    /**
     * 追加
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
            // エラーメッセージを出力
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### 生成された ./support/ErrorCode.php ファイル

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
