# Composant de génération automatique des codes d'erreur

## Description

Peut générer automatiquement des codes d'erreur en fonction des règles spécifiées.

> Dans les données renvoyées, le paramètre code est convenu ; tous les codes personnalisés, les valeurs positives représentent un service normal, les valeurs négatives représentent une anomalie de service.

## Adresse du projet

https://github.com/teamones-open/response-code-msg

## Installation

```php
composer require teamones/response-code-msg
```

## Utilisation

### Fichier de classe ErrorCode vide

- Chemin du fichier : ./support/ErrorCode.php

```php
<?php
/**
 * Fichier généré, veuillez ne pas modifier manuellement.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Fichier de configuration

Les codes d'erreur seront automatiquement générés en fonction des paramètres configurés ci-dessous. Par exemple, si system_number = 201 et start_min_number = 10000, le premier code d'erreur généré sera -20110001.

- Chemin du fichier : ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // Fichier de classe ErrorCode
    "root_path" => app_path(), // Répertoire racine de code actuel
    "system_number" => 201, // Identifiant du système
    "start_min_number" => 10000 // Plage de génération des codes d'erreur, par exemple 10000-99999
];
```

### Ajout du démarrage automatique de la génération de codes d'erreur dans start.php

- Chemin du fichier : ./start.php

```php
// Placé après Config::load(config_path(), ['route', 'container']);

// Générer les codes d'erreur, uniquement en mode APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Utilisation dans le code

Dans le code ci-dessous, **ErrorCode::ModelAddOptionsError** est un code d'erreur, où **ModelAddOptionsError** doit être écrit en majuscules en fonction de la signification requise.

> Vous constaterez qu'il est impossible de l'utiliser une fois écrit, il sera généré après le redémarrage suivant. Notez que parfois il est nécessaire de redémarrer deux fois.

```php
<?php
/**
 * Classe de service pour les opérations liées à la navigation
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Inclure le fichier de classe ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * Ajouter
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
            // Afficher le message d'erreur
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Fichier ./support/ErrorCode.php après génération

```php
<?php
/**
 * Fichier généré, veuillez ne pas modifier manuellement.
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
