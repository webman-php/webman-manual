# Regulamento de Desenvolvimento de Plugins de Aplicativos

## Requisitos para Plugins de Aplicativos
* Os plugins não podem conter código, ícones, imagens ou qualquer conteúdo que viole direitos autorais.
* O código-fonte do plugin deve ser completo e não pode ser criptografado.
* Os plugins devem ter funcionalidades completas e não podem ser simples.
* Deve ser fornecida uma documentação completa e uma introdução de funcionalidades.
* Os plugins não podem conter submercados.
* Não é permitido incluir qualquer texto ou link promocional dentro do plugin.

## Identificação do Plugin de Aplicativo
Cada plugin de aplicativo tem uma identificação única, composta por letras. Essa identificação afeta o diretório de origem do plugin, o namespace da classe e o prefixo da tabela do banco de dados do plugin.

Por exemplo, se o desenvolvedor usar "foo" como identificação do plugin, o diretório de origem do plugin será `{diretório principal}/plugin/foo`, o namespace correspondente do plugin será `plugin\foo` e o prefixo da tabela será `foo_`.

Como a identificação é única em toda a rede, os desenvolvedores precisam verificar se a identificação está disponível antes de iniciar o desenvolvimento, o que pode ser feito no seguinte endereço: [Verificação de Identificação do Aplicativo](https://www.workerman.net/app/check).

## Banco de Dados
* O nome das tabelas deve consistir em letras minúsculas de `a` a `z` e sublinhados`_`.
* As tabelas de dados do plugin devem usar o prefixo da identificação do plugin, por exemplo, a tabela de artigos do plugin "foo" seria `foo_article`.
* A chave primária da tabela deve ser 'id'.
* Deve-se usar o mecanismo de armazenamento InnoDB de forma consistente.
* Use o conjunto de caracteres utf8mb4_general_ci de forma consistente.
* Pode-se usar o ORM do Laravel ou do Think-ORM.
* É recomendado o uso de campos de tempo tipo DateTime.

## Padrões de Código

#### Regras PSR
O código deve seguir as regras de carregamento PSR4.

#### Nomenclatura de Classes
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Nomenclatura de Atributos e Métodos
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Métodos que não exigem autenticação
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Obter comentários
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Comentários
Atributos e métodos de classes devem incluir comentários, incluindo resumo, parâmetros e tipo de retorno.

#### Indentação
O código deve usar 4 espaços para indentação, em vez de tabulação.

#### Controle de Fluxo
Após palavras-chave de controle de fluxo (if, for, while, foreach, etc.), deve haver um espaço, e as chaves de início do bloco de código de controle de fluxo devem estar na mesma linha que a declaração de abertura.
```php
foreach ($users as $uid => $user) {

}
```

#### Nomenclatura de Variáveis Temporárias
Recomenda-se o uso de nomenclatura em camel case com a primeira letra minúscula (não é obrigatório).

```php
$articleCount = 100;
```
