# Componente de Geração Automática de Códigos de Erro

## Descrição

Capaz de gerar automaticamente códigos de erro com base em regras predefinidas.

> Convenção do parâmetro de código nos dados de retorno, todos os códigos personalizados, números positivos representam um serviço normal, números negativos representam uma exceção de serviço.

## Endereço do Projeto

https://github.com/teamones-open/response-code-msg

## Instalação

```php
composer require teamones/response-code-msg
```

## Utilização

### Arquivo da Classe ErrorCode vazio

- Caminho do arquivo ./support/ErrorCode.php

```php
<?php
/**
 * Arquivo gerado automaticamente, por favor, não modifique manualmente.
 * @Author:$Id$
 */
namespace support;

class ErrorCode
{
}
```

### Arquivo de Configuração

Os códigos de erro serão gerados automaticamente conforme os parâmetros de configuração abaixo. Por exemplo, se system_number = 201 e start_min_number = 10000, o primeiro código de erro gerado será -20110001.

- Caminho do arquivo ./config/error_code.php

```php
<?php

return [
    "class" => new \support\ErrorCode(), // Arquivo da classe ErrorCode
    "root_path" => app_path(), // Diretório raiz do código atual
    "system_number" => 201, // Identificação do sistema
    "start_min_number" => 10000 // Intervalo de geração de códigos de erro, por exemplo, 10000-99999
];
```

### Adicionando código de geração automática de erros em start.php

- Caminho do arquivo ./start.php

```php
// Coloque após Config::load(config_path(), ['route', 'container']);

// Gera códigos de erro, apenas em modo APP_DEBUG
if (config("app.debug")) {
    $errorCodeConfig = config('error_code');
    (new \teamones\responseCodeMsg\Generate($errorCodeConfig))->run();
}
```

### Utilização no Código

No código abaixo, **ErrorCode::ModelAddOptionsError** representa o código de erro, onde **ModelAddOptionsError** deve ser escrito pelo usuário de acordo com a semântica das necessidades atuais em letras maiúsculas.

> Após escrever, você descobrirá que não pode usá-lo diretamente, ele será gerado na próxima reinicialização. Esteja ciente de que às vezes é necessário reiniciar duas vezes.

```php
<?php
/**
 * Classe de serviço para operações relacionadas à navegação
 */

namespace app\service;

use app\model\Demo as DemoModel;

// Importa o arquivo da classe ErrorCode
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
            // Exibir mensagem de erro
            throw_http_exception($e->getMessage(), ErrorCode::ModelAddOptionsError);
        }
        return [];
    }
}
```

### Arquivo ./support/ErrorCode.php gerado depois da geração

```php
<?php
/**
 * Arquivo gerado automaticamente, por favor, não modifique manualmente.
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
