# Automatische Fehlercode-Komponente

## Erläuterung

Es ist möglich, Fehlercodes automatisch gemäß der angegebenen Regeln zu generieren.

> Es wird vereinbart, dass der Parameter "code" in den Rückgabedaten alle benutzerdefinierten Codes enthält. Positive Zahlen stehen für einen normalen Service, negative Zahlen für einen fehlerhaften Service.

## Projektadresse

https://github.com/teamones-open/response-code-msg

## Installation

```php
composer require teamones/response-code-msg
```

## Verwendung

### Leere ErrorCode-Klassen-Datei

- Dateipfad ./support/ErrorCode.php

```php
<?php
/**
 * Automatisch generierte Datei, bitte nicht manuell ändern.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Konfigurationsdatei

Fehlercodes werden automatisch gemäß den konfigurierten Parametern generiert. Zum Beispiel, wenn system_number = 201 und start_min_number = 10000, wird der erste generierte Fehlercode -20110001 sein.

- Dateipfad ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // ErrorCode-Klassen-Datei
    "root_path" => app_path(), // Aktuelles Stammverzeichnis des Codes
    "system_number" => 201, // Systemkennung
    "start_min_number" => 10000 // Fehlercode-Generierungsbereich, z.B. 10000-99999
];
```

### Hinzufügen des automatischen Fehlercode-Generierungscode in start.php

- Dateipfad ./start.php

```php
// Nach Config::load(config_path(), ['route', 'container']) einfügen

// Fehlercode generieren, nur im APP_DEBUG-Modus generieren
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Verwendung im Code

Im folgenden Code steht **ErrorCode::ModelAddOptionsError** für einen Fehlercode, wobei **ModelAddOptionsError** vom Benutzer gemäß den aktuellen semantischen Anforderungen in Großbuchstaben geschrieben werden muss.

> Sie werden feststellen, dass Sie diesen nicht verwenden können. Nach einem Neustarten wird der entsprechende Fehlercode generiert. Beachten Sie, dass Sie manchmal zweimal neu starten müssen.

```php
<?php
/**
 * Navigationsbezogener Service-Klasse
 */

namespace app\service;

use app\model\Demo as DemoModel;

// ErrorCode-Klassen-Datei einbinden
use support\ErrorCode;

class Demo
{
    /**
     * Hinzufügen
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
            // Fehlermeldung ausgeben
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Generierte Datei ./support/ErrorCode.php nach der Generierung

```php
<?php
/**
 * Automatisch generierte Datei, bitte nicht manuell ändern.
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
