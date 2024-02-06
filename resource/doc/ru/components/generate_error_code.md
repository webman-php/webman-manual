# Компонент генерации кодов ошибок

## Описание

Этот компонент автоматически генерирует коды ошибок согласно заданным правилам.

> По соглашению, параметр code возвращаемых данных представляет собой пользовательский код. Положительное число означает успешность работы сервиса, а отрицательное - исключительную ситуацию.

## Ссылка на проект

https://github.com/teamones-open/response-code-msg

## Установка

```php
composer require teamones/response-code-msg
```

## Использование

### Пустой файл класса `ErrorCode`

- Путь к файлу: ./support/ErrorCode.php

```php
<?php
/**
 * Автоматически сгенерированный файл. Не редактировать вручную.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Файл конфигурации

Коды ошибок будут автоматически увеличиваться в соответствии с заданными параметрами. Например, если system_number = 201, start_min_number = 10000, то первый сгенерированный код ошибки будет -20110001.

- Путь к файлу: ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // Файл класса ErrorCode
    "root_path" => app_path(), // Текущий корневой каталог кода
    "system_number" => 201, // Идентификатор системы
    "start_min_number" => 10000 // Диапазон генерации кодов ошибок, например, 10000-99999
];
```

### Добавление кода для автоматической генерации ошибок в start.php

- Путь к файлу: ./start.php

```php
// Добавить это после Config::load(config_path(), ['route', 'container']);

// Генерация кодов ошибок только в режиме отладки APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Использование в коде

В приведенном ниже коде **ErrorCode::ModelAddOptionsError** - это код ошибки, при этом **ModelAddOptionsError** должен быть написан пользователем с учетом семантики и с заглавной буквы.

> После написания вы обнаружите, что код не работает, после следующей перезагрузки будет сгенерирован соответствующий код ошибки. Обратите внимание, иногда может потребоваться две перезагрузки.

```php
<?php
/**
 * Класс сервиса для операций навигации
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
            // Вывод сообщения об ошибке
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Сгенерированный файл ./support/ErrorCode.php посл генерации

```php
<?php
/**
 * Автоматически сгенерированный файл. Не редактировать вручную.
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
