# Генератор компонентов ошибок

## Объяснение

Способен автоматически поддерживать генерацию кодов ошибок в соответствии с заданными правилами.

> Согласно возвращаемым данным, параметр code, все пользовательские коды, положительные числа обозначают нормальное функционирование службы, а отрицательные числа обозначают аномальное функционирование службы.

## Адрес проекта

https://github.com/teamones-open/response-code-msg

## Установка

```php
composer require teamones/response-code-msg
```

## Использование

### Пустой файл класса ErrorCode

- Путь к файлу ./support/ErrorCode.php

```php
<?php
/**
 * Сгенерированный файл, пожалуйста, не изменяйте его вручную.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Файл конфигурации

Коды ошибок будут автоматически генерироваться в соответствии с параметрами, указанными ниже. Например, если system_number = 201, start_min_number = 10000, то первый сгенерированный код ошибки будет равен -20110001.

- Путь к файлу ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // Файл класса ErrorCode
    "root_path" => app_path(), // Текущий корневой каталог кода
    "system_number" => 201, // Идентификатор системы
    "start_min_number" => 10000 // Диапазон генерации кодов ошибок, например, 10000-99999
];
```

### Добавление автоматической генерации кодов ошибок в start.php

- Путь к файлу ./start.php

```php
// Разместить после Config::load(config_path(), ['route', 'container']);

// Генерация кодов ошибок, только в режиме APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Использование в коде

Код **ErrorCode::ModelAddOptionsError** в приведенном ниже коде представляет собой код ошибки, где **ModelAddOptionsError** должен быть написан пользователем в соответствии с текущими требованиями с заглавной буквы.

> После написания вы заметите, что он не работает, и он будет сгенерирован после следующей перезагрузки. Обратите внимание, иногда может потребоваться дважды перезагрузить.

```php
<?php
/**
 * Класс сервиса для операций с навигацией
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Подключение файла класса ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * Добавление
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
            // Вывод информации об ошибке
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Созданный файл ./support/ErrorCode.php после генерации

```php
<?php
/**
 * Сгенерированный файл, пожалуйста, не изменяйте его вручную.
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
