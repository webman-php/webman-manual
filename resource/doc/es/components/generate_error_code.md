# Componente de generación automática de códigos de error

## Descripción

Puede mantener automáticamente la generación de códigos de error según las reglas especificadas.

> En los datos devueltos, el parámetro de código se establece según el código personalizado. Los números positivos representan un servicio normal, mientras que los números negativos representan una excepción en el servicio.

## Dirección del proyecto

https://github.com/teamones-open/response-code-msg

## Instalación

```php
composer require teamones/response-code-msg
```

## Uso

### Archivo de clase ErrorCode vacío

- Ruta del archivo: ./support/ErrorCode.php

```php
<?php
/**
 * Archivo generado, no modificar manualmente.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Archivo de configuración

Los códigos de error se generarán automáticamente de acuerdo con los parámetros configurados a continuación. Por ejemplo, si system_number = 201 y start_min_number = 10000, el primer código de error generado será -20110001.

- Ruta del archivo: ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // Archivo de clase ErrorCode
    "root_path" => app_path(), // Directorio raíz del código actual
    "system_number" => 201, // Identificación del sistema
    "start_min_number" => 10000 // Rango de generación de códigos de error, por ejemplo, 10000-99999
];
```

### Agregar código para generar los códigos de error automáticamente en start.php

- Ruta del archivo: ./start.php

```php
// Colocar después de Config::load(config_path(), ['route', 'container']);

// Generar códigos de error, solo en modo APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Uso en el código

En el siguiente código, **ErrorCode::ModelAddOptionsError** es el código de error, donde **ModelAddOptionsError** debe ser escrito por el usuario en función de los requisitos actuales de manera que refleje el significado con las iniciales en mayúsculas.

> Después de escribirlo, es posible que no se pueda usar, pero se generará el código correspondiente después de reiniciar la próxima vez. Tenga en cuenta que a veces es necesario reiniciar dos veces.

```php
<?php
/**
 * Clase de servicio para operaciones relacionadas con la navegación
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Incluir archivo de clase ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * Agregar
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
            // Imprimir mensaje de error
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Archivo ./support/ErrorCode.php generado posteriormente

```php
<?php
/**
 * Archivo generado, no modificar manualmente.
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
