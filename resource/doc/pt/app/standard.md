# Normas de desenvolvimento de plugins de aplicativos

## Requisitos para plugins de aplicativos
* Os plugins não podem conter código, ícones, imagens ou qualquer conteúdo que viole direitos autorais.
* O código-fonte do plugin deve ser completo e não pode ser criptografado.
* Os plugins devem oferecer funcionalidades completas e não podem fornecer apenas funcionalidades simples.
* Deve ser fornecida uma documentação completa e uma introdução detalhada das funcionalidades do plugin.
* Os plugins não podem incluir submercados.
* Não podem conter qualquer texto ou links de promoção dentro do plugin.

## Identificação do plugin de aplicativo
Cada plugin de aplicativo tem uma identificação única, composta por letras. Esta identificação afeta o nome do diretório onde o código do plugin está localizado, o namespace da classe e o prefixo da tabela do banco de dados do plugin.

Por exemplo, se o desenvolvedor usar "foo" como identificação de plugin, o diretório onde o código do plugin está localizado será `{main_project}/plugin/foo`, o namespace correspondente será `plugin\foo`, e o prefixo da tabela será `foo_`.

Como a identificação é única em toda a Internet, os desenvolvedores devem verificar a disponibilidade da identificação antes de começar o desenvolvimento, por meio do endereço [Verificação de Identificação de Aplicativo](https://www.workerman.net/app/check).

## Banco de dados
* Os nomes das tabelas devem consistir em letras minúsculas de `a` a `z` e sublinhados `_`.
* As tabelas de dados do plugin devem ter o prefixo da identificação do plugin. Por exemplo, a tabela de artigos do plugin "foo" será `foo_article`.
* A chave primária da tabela deve ser `id`.
* Deve-se utilizar o mecanismo InnoDB de forma consistente.
* O conjunto de caracteres deve ser utf8mb4_general_ci.
* ORM do banco de dados pode ser feito com Laravel ou Think-ORM.
* Recomenda-se o uso do campo de tempo DateTime.

## Convenções de código

#### Conformidade com a especificação PSR
O código deve seguir a especificação de carregamento PSR4.

#### Nomes das classes devem começar com maiúsculas em estilo camel case
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Nomes de propriedades e métodos da classe devem começar com minúsculas em estilo camel case
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Métodos que não precisam de autenticação
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
As propriedades e métodos da classe devem incluir comentários, incluindo uma descrição geral, parâmetros e tipos de retorno.

#### Indentação
O código deve ser indentado com 4 espaços em branco, e não utilizando tabulações.

#### Controle de fluxo
Após palavras-chave de controle de fluxo (if, for, while, foreach, etc.), deve-se incluir um espaço em branco, e as chaves de abertura dos blocos de controle de fluxo devem estar na mesma linha que a declaração do controle de fluxo.
```php
foreach ($users as $uid => $user) {

}
```

#### Nomes de variáveis temporárias
É recomendável que sigam o estilo camel case com letras minúsculas (não é obrigatório)
```php
$articleCount = 100;
```
