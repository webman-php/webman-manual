# Componente di generazione automatica dei codici di errore

## Descrizione

In grado di generare automaticamente i codici di errore in base alle regole date.

> Convenzione sul parametro di ritorno dei dati `code`, tutti i codici personalizzati, i numeri positivi indicano un servizio normale, i numeri negativi indicano un'eccezione del servizio.

## Indirizzo del progetto

https://github.com/teamones-open/response-code-msg

## Installazione

```php
composer require teamones/response-code-msg
```

## Utilizzo

### File di classe ErrorCode vuoto

- Percorso del file: ./support/ErrorCode.php

```php
<?php
/**
 * File generato automaticamente, si prega di non modificare manualmente.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### File di configurazione

Il codice di errore verrà generato automaticamente in base ai parametri di configurazione di seguito. Ad esempio, se `system_number` è 201 e `start_min_number` è 10000, il primo codice di errore generato sarà -20110001.

- Percorso del file: ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // File di classe ErrorCode
    "root_path" => app_path(), // Percorso radice del codice attuale
    "system_number" => 201, // Identificativo del sistema
    "start_min_number" => 10000 // Range di generazione del codice di errore, ad esempio 10000-99999
];
```

### Aggiunta del codice di avvio automatico degli errori in start.php

- Percorso del file: ./start.php

```php
// Aggiungi dopo Config::load(config_path(), ['route', 'container']);

// Genera codice di errore, solo in modalità APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Utilizzo nel codice

Nel seguente codice, **ErrorCode::ModelAddOptionsError** è il codice di errore, in cui **ModelAddOptionsError** deve essere scritto dall'utente in base alle esigenze attuali inizializzando la lettera maiuscola della semantica.

> Dopo averlo scritto, noterai che non può essere utilizzato e verrà generato al riavvio successivo. Presta attenzione che a volte potrebbero essere necessari due riavvii.

```php
<?php
/**
 * Classe di servizio per operazioni correlate alla navigazione
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Includi il file di classe ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * Aggiungi
     * @param $data
     * @return array|mixed
     * @throws \eccezione
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
            // Mostra messaggio di errore
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### File ./support/ErrorCode.php generato

```php
<?php
/**
 * File generato automaticamente, si prega di non modificare manualmente.
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
