# Componente de geração automática de códigos de erro

## Descrição

Capaz de gerar automaticamente códigos de erro com base em regras fornecidas.

> Convenção do parâmetro de retorno de dados "code", todos os códigos personalizados, números positivos representam um serviço normal, e números negativos representam uma exceção de serviço.

## Endereço do projeto

https://github.com/teamones-open/response-code-msg

## Instalação

```php
composer require teamones/response-code-msg
```

## Uso

### Arquivo de Classe ErrorCode vazio

- Caminho do arquivo: ./support/ErrorCode.php

```php
<?php
/**
 * Arquivo gerado automaticamente, não modifique manualmente.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Arquivo de configuração

Os códigos de erro serão automaticamente gerados com base nos parâmetros configurados abaixo. Por exemplo, se system_number = 201 e start_min_number = 10000, o primeiro código de erro gerado seria -20110001.

- Caminho do arquivo: ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // Arquivo da classe ErrorCode
    "root_path" => app_path(), // Diretório raiz do código atual
    "system_number" => 201, // Identificação do sistema
    "start_min_number" => 10000 // Faixa de geração de códigos de erro, por exemplo, 10000-99999
];
```

### Adicionar código de geração automática de erro em start.php

- Caminho do arquivo: ./start.php

```php
// Coloque após Config::load(config_path(), ['route', 'container']);

// Gerar códigos de erro, apenas em modo APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Uso no código

No código abaixo, **ErrorCode::ModelAddOptionsError** é um código de erro, onde **ModelAddOptionsError** precisa ser escrito pelo usuário de acordo com a semântica da demanda atual em maiúsculas.

> Você notará que não poderá usá-lo imediatamente, será gerado automaticamente após o próximo reinício. Esteja ciente de que às vezes pode ser necessário reiniciar duas vezes.

```php
<?php
/**
 * Classe de serviço de operações relacionadas à navegação
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Importar arquivo da classe ErrorCode
use support\ErrorCode;

class Demo
{
    /**
     * Adicionar
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
            // Imprimir mensagem de erro
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Arquivo ./support/ErrorCode.php gerado após a geração

```php
<?php
/**
 * Arquivo gerado automaticamente, não modifique manualmente.
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
