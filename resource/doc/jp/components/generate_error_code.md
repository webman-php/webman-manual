# エラーコードコンポーネントの自動生成

## 説明

指定されたルールに従って自動的にエラーコードを生成および管理できます。

> レスポンスデータにはcodeパラメータが含まれており、すべてのカスタムコードについて、正の値がサービスの正常性を、負の値がサービスの異常を示します。

## プロジェクトのアドレス

https://github.com/teamones-open/response-code-msg

## インストール

```php
composer require teamones/response-code-msg
```

## 使用方法

### 空のErrorCodeクラスファイル

- ファイルパス ./support/ErrorCode.php

```php
<?php
/**
 * 自動生成ファイル、手動で変更しないでください。
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### 設定ファイル

エラーコードは、以下に設定されたパラメータに従って自動的に増分されます。たとえば、現在の"system_number"が201であり、"start_min_number"が10000である場合、生成される最初のエラーコードは-20110001です。

- ファイルパス ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode クラスファイル
    "root_path" => app_path(), // 現在のコードのルートディレクトリ
    "system_number" => 201, // システム識別子
    "start_min_number" => 10000 // エラーコード生成範囲 たとえば10000-99999
];
```

### start.phpに自動エラーコード生成の起動コードを追加

- ファイルパス ./start.php

```php
// Config::load(config_path(), ['route', 'container']); の後に配置してください。

// エラーコードを生成、APP_DEBUGモードのみ生成
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### コード内での使用

以下のコードの **ErrorCode::ModelAddOptionsError** はエラーコードであり、**ModelAddOptionsError** はユーザーが現在の要求に基づいてセマンティックに適した大文字で記述する必要があります。

> 一度記述しても使えないことに気づくと、次の再起動後に対応するエラーコードが自動生成されます。ときには2回の再起動が必要な場合もあります。

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
 * 自動生成ファイル、手動で変更しないでください。
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
