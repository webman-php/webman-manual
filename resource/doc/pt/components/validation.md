# Outros Validadores

Existem muitos validadores disponíveis no composer que podem ser usados diretamente, como:

#### <a href="#webman-validation"> webman/validation (Recomendado)</a>
#### <a href="#think-validate"> top-think/think-validate</a>
#### <a href="#respect-validation"> respect/validation</a>

<a name="webman-validation"></a>
# Validador webman/validation

Baseado em `illuminate/validation`, oferece validação manual, validação por anotação, validação em nível de parâmetro e conjuntos de regras reutilizáveis.

## Instalação

```bash
composer require webman/validation
```

## Conceitos Básicos

- **Reutilização de Conjunto de Regras**: Defina `rules`, `messages`, `attributes` e `scenes` reutilizáveis estendendo `support\validation\Validator`, que podem ser reutilizados tanto na validação manual quanto na validação por anotação.
- **Validação por Anotação (Atributo) em Nível de Método**: Use o atributo PHP 8 `#[Validate]` para vincular a validação aos métodos do controlador.
- **Validação por Anotação (Atributo) em Nível de Parâmetro**: Use o atributo PHP 8 `#[Param]` para vincular a validação aos parâmetros dos métodos do controlador.
- **Tratamento de Exceções**: Lança `support\validation\ValidationException` em caso de falha na validação; a classe de exceção é configurável.
- **Validação de Banco de Dados**: Se a validação envolver banco de dados, é necessário instalar `composer require webman/database`.

## Validação Manual

### Uso Básico

```php
use support\validation\Validator;

$data = ['email' => 'user@example.com'];

Validator::make($data, [
    'email' => 'required|email',
])->validate();
```

> **Nota**
> `validate()` lança `support\validation\ValidationException` quando a validação falha. Se preferir não lançar exceções, use a abordagem `fails()` abaixo para obter as mensagens de erro.

### Mensagens e atributos personalizados

```php
use support\validation\Validator;

$data = ['contact' => 'user@example.com'];

Validator::make(
    $data,
    ['contact' => 'required|email'],
    ['contact.email' => 'Invalid email format'],
    ['contact' => 'Email']
)->validate();
```

### Validar Sem Exceção (Obter Mensagens de Erro)

Se preferir não lançar exceções, use `fails()` para verificar e obter as mensagens de erro via `errors()` (retorna `MessageBag`):

```php
use support\validation\Validator;

$data = ['email' => 'bad-email'];

$validator = Validator::make($data, [
    'email' => 'required|email',
]);

if ($validator->fails()) {
    $firstError = $validator->errors()->first();      // string
    $allErrors = $validator->errors()->all();         // array
    $errorsByField = $validator->errors()->toArray(); // array
    // handle errors...
}
```

## Reutilização de Conjunto de Regras (Validador Personalizado)

```php
namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:1',
        'name' => 'required|string|min:2|max:20',
        'email' => 'required|email',
    ];

    protected array $messages = [
        'name.required' => 'Name is required',
        'email.required' => 'Email is required',
        'email.email' => 'Invalid email format',
    ];

    protected array $attributes = [
        'name' => 'Name',
        'email' => 'Email',
    ];
}
```

### Reutilização na Validação Manual

```php
use app\validation\UserValidator;

UserValidator::make($data)->validate();
```

### Uso de Cenas (Opcional)

`scenes` é um recurso opcional; valida apenas um subconjunto de campos quando você chama `withScene(...)`.

```php
namespace app\validation;

use support\validation\Validator;

class UserValidator extends Validator
{
    protected array $rules = [
        'id' => 'required|integer|min:1',
        'name' => 'required|string|min:2|max:20',
        'email' => 'required|email',
    ];

    protected array $scenes = [
        'create' => ['name', 'email'],
        'update' => ['id', 'name', 'email'],
    ];
}
```

```php
use app\validation\UserValidator;

// Nenhuma cena especificada -> valida todas as regras
UserValidator::make($data)->validate();

// Cena especificada -> valida apenas os campos dessa cena
UserValidator::make($data)->withScene('create')->validate();
```

## Validação por Anotação (Nível de Método)

### Regras Diretas

```php
use support\Request;
use support\validation\annotation\Validate;

class AuthController
{
    #[Validate(
        rules: [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ],
        messages: [
            'email.required' => 'Email is required',
            'password.required' => 'Password is required',
        ],
        attributes: [
            'email' => 'Email',
            'password' => 'Password',
        ]
    )]
    public function login(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Reutilização de Conjuntos de Regras

```php
use app\validation\UserValidator;
use support\Request;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(validator: UserValidator::class, scene: 'create')]
    public function create(Request $request)
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Múltiplas Sobreposições de Validação

```php
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(rules: ['email' => 'required|email'])]
    #[Validate(rules: ['token' => 'required|string'])]
    public function send()
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Fonte de Dados da Validação

```php
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(
        rules: ['email' => 'required|email'],
        in: ['query', 'body', 'path']
    )]
    public function send()
    {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

Use o parâmetro `in` para especificar a fonte de dados:

* **query** parâmetros de consulta da requisição HTTP, de `$request->get()`
* **body** corpo da requisição HTTP, de `$request->post()`
* **path** parâmetros de caminho da requisição HTTP, de `$request->route->param()`

`in` pode ser uma string ou array; quando é um array, os valores são mesclados em ordem, com valores posteriores sobrescrevendo os anteriores. Quando `in` não é passado, o padrão é `['query', 'body', 'path']`.


## Validação em Nível de Parâmetro (Param)

### Uso Básico

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: 'required|email')] string $from,
        #[Param(rules: 'required|email')] string $to,
        #[Param(rules: 'required|string|min:1|max:500')] string $content
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Fonte de Dados da Validação

Da mesma forma, a validação em nível de parâmetro também suporta o parâmetro `in` para especificar a fonte:

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: 'required|email', in: ['body'])] string $from
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```


### rules suporta string ou array

```php
use support\validation\annotation\Param;

class MailController
{
    public function send(
        #[Param(rules: ['required', 'email'])] string $from
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Mensagens / atributo personalizados

```php
use support\validation\annotation\Param;

class UserController
{
    public function updateEmail(
        #[Param(
            rules: 'required|email',
            messages: ['email.email' => 'Invalid email format'],
            attribute: 'Email'
        )]
        string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

### Reutilização de Constante de Regra

```php
final class ParamRules
{
    public const EMAIL = ['required', 'email'];
}

class UserController
{
    public function send(
        #[Param(rules: ParamRules::EMAIL)] string $email
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

## Combinação de Nível de Método + Nível de Parâmetro

```php
use support\Request;
use support\validation\annotation\Param;
use support\validation\annotation\Validate;

class UserController
{
    #[Validate(rules: ['token' => 'required|string'])]
    public function send(
        Request $request,
        #[Param(rules: 'required|email')] string $from,
        #[Param(rules: 'required|integer')] int $id
    ) {
        return json(['code' => 0, 'msg' => 'ok']);
    }
}
```

## Inferência Automática de Regras (Baseada na Assinatura do Parâmetro)

Quando `#[Validate]` é usado em um método, ou qualquer parâmetro desse método usa `#[Param]`, este componente **infere e completa automaticamente regras básicas de validação a partir da assinatura do parâmetro do método**, depois mescla-as com as regras existentes antes da validação.

### Exemplo: expansão equivalente de `#[Validate]`

1) Apenas habilitar `#[Validate]` sem escrever regras manualmente:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content, int $uid)
    {
    }
}
```

Equivalente a:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'required|string',
        'uid' => 'required|integer',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

2) Apenas regras parciais escritas, o restante completado pela assinatura do parâmetro:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'min:2',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

Equivalente a:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'required|string|min:2',
        'uid' => 'required|integer',
    ])]
    public function create(string $content, int $uid)
    {
    }
}
```

3) Valor padrão / tipo nullable:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate]
    public function create(string $content = 'default', ?int $uid = null)
    {
    }
}
```

Equivalente a:

```php
use support\validation\annotation\Validate;

class DemoController
{
    #[Validate(rules: [
        'content' => 'string',
        'uid' => 'integer|nullable',
    ])]
    public function create(string $content = 'default', ?int $uid = null)
    {
    }
}
```

## Tratamento de Exceções

### Exceção Padrão

A falha na validação lança `support\validation\ValidationException` por padrão, que estende `Webman\Exception\BusinessException` e não registra erros.

O comportamento padrão de resposta é tratado por `BusinessException::render()`:

- Requisições normais: retorna mensagem em string, ex: `token is required.`
- Requisições JSON: retorna resposta JSON, ex: `{"code": 422, "msg": "token is required.", "data":....}`

### Personalizar tratamento via exceção customizada

- Configuração global: `exception` em `config/plugin/webman/validation/app.php`

## Suporte Multilíngue

O componente inclui pacotes de idioma chinês e inglês integrados e suporta sobrescritas do projeto. Ordem de carregamento:

1. Pacote de idioma do projeto `resource/translations/{locale}/validation.php`
2. Integrado ao componente `vendor/webman/validation/resources/lang/{locale}/validation.php`
3. Inglês integrado do Illuminate (fallback)

> **Nota**
> O idioma padrão do Webman é configurado em `config/translation.php`, ou pode ser alterado via `locale('en');`.

### Exemplo de Sobrescrita Local

`resource/translations/zh_CN/validation.php`

```php
return [
    'email' => ':attribute is not a valid email format.',
];
```

## Carregamento Automático de Middleware

Após a instalação, o componente carrega automaticamente o middleware de validação via `config/plugin/webman/validation/middleware.php`; não é necessário registro manual.

## Geração pela Linha de Comando

Use o comando `make:validator` para gerar classes de validador (saída padrão para o diretório `app/validation`).

> **Nota**
> Requer `composer require webman/console`

### Uso Básico

- **Gerar template vazio**

```bash
php webman make:validator UserValidator
```

- **Sobrescrever arquivo existente**

```bash
php webman make:validator UserValidator --force
php webman make:validator UserValidator -f
```

### Gerar regras a partir da estrutura da tabela

- **Especificar nome da tabela para gerar regras base** (infere `$rules` a partir do tipo de campo/nullable/comprimento etc.; exclui campos relacionados a ORM por padrão: laravel usa `created_at/updated_at/deleted_at`, thinkorm usa `create_time/update_time/delete_time`)

```bash
php webman make:validator UserValidator --table=wa_users
php webman make:validator UserValidator -t wa_users
```

- **Especificar conexão do banco de dados** (cenários de múltiplas conexões)

```bash
php webman make:validator UserValidator --table=wa_users --database=mysql
php webman make:validator UserValidator -t wa_users -d mysql
```

### Cenas

- **Gerar cenas CRUD**: `create/update/delete/detail`

```bash
php webman make:validator UserValidator --table=wa_users --scenes=crud
php webman make:validator UserValidator -t wa_users -s crud
```

> A cena `update` inclui o campo de chave primária (para localizar registros) mais os demais campos; `delete/detail` incluem apenas a chave primária por padrão.

### Seleção de ORM (laravel (illuminate/database) vs think-orm)

- **Seleção automática (padrão)**: Usa o que estiver instalado/configurado; quando ambos existem, usa illuminate por padrão
- **Forçar especificação**

```bash
php webman make:validator UserValidator --table=wa_users --orm=laravel
php webman make:validator UserValidator --table=wa_users --orm=thinkorm
php webman make:validator UserValidator -t wa_users -o thinkorm
```

### Exemplo completo

```bash
php webman make:validator UserValidator -t wa_users -d mysql -s crud -o laravel -f
```

## Testes Unitários

No diretório raiz do `webman/validation`, execute:

```bash
composer install
vendor\bin\phpunit -c phpunit.xml
```

## Referência de Regras de Validação

<a name="available-validation-rules"></a>
## Regras de Validação Disponíveis

> [!IMPORTANT]
> - O Webman Validation é baseado em `illuminate/validation`; os nomes das regras correspondem ao Laravel e não há regras específicas do Webman.
> - O middleware valida dados de `$request->all()` (GET+POST) mesclados com parâmetros de rota por padrão, excluindo arquivos enviados; para regras de arquivo, mescle `$request->file()` nos dados você mesmo, ou chame `Validator::make` manualmente.
> - `current_password` depende do guard de autenticação; `exists`/`unique` dependem da conexão do banco de dados e do query builder; essas regras não estão disponíveis quando os componentes correspondentes não estão integrados.

A seguir, todas as regras de validação disponíveis e seus propósitos:

<style>
    .collection-method-list > p {
        columns: 10.8em 3; -moz-columns: 10.8em 3; -webkit-columns: 10.8em 3;
    }

    .collection-method-list a {
        display: block;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>

#### Boolean

<div class="collection-method-list" markdown="1">

[Accepted](#rule-accepted)
[Accepted If](#rule-accepted-if)
[Boolean](#rule-boolean)
[Declined](#rule-declined)
[Declined If](#rule-declined-if)

</div>

#### String

<div class="collection-method-list" markdown="1">

[Active URL](#rule-active-url)
[Alpha](#rule-alpha)
[Alpha Dash](#rule-alpha-dash)
[Alpha Numeric](#rule-alpha-num)
[Ascii](#rule-ascii)
[Confirmed](#rule-confirmed)
[Current Password](#rule-current-password)
[Different](#rule-different)
[Doesnt Start With](#rule-doesnt-start-with)
[Doesnt End With](#rule-doesnt-end-with)
[Email](#rule-email)
[Ends With](#rule-ends-with)
[Enum](#rule-enum)
[Hex Color](#rule-hex-color)
[In](#rule-in)
[IP Address](#rule-ip)
[IPv4](#rule-ipv4)
[IPv6](#rule-ipv6)
[JSON](#rule-json)
[Lowercase](#rule-lowercase)
[MAC Address](#rule-mac)
[Max](#rule-max)
[Min](#rule-min)
[Not In](#rule-not-in)
[Regular Expression](#rule-regex)
[Not Regular Expression](#rule-not-regex)
[Same](#rule-same)
[Size](#rule-size)
[Starts With](#rule-starts-with)
[String](#rule-string)
[Uppercase](#rule-uppercase)
[URL](#rule-url)
[ULID](#rule-ulid)
[UUID](#rule-uuid)

</div>

#### Numeric

<div class="collection-method-list" markdown="1">

[Between](#rule-between)
[Decimal](#rule-decimal)
[Different](#rule-different)
[Digits](#rule-digits)
[Digits Between](#rule-digits-between)
[Greater Than](#rule-gt)
[Greater Than Or Equal](#rule-gte)
[Integer](#rule-integer)
[Less Than](#rule-lt)
[Less Than Or Equal](#rule-lte)
[Max](#rule-max)
[Max Digits](#rule-max-digits)
[Min](#rule-min)
[Min Digits](#rule-min-digits)
[Multiple Of](#rule-multiple-of)
[Numeric](#rule-numeric)
[Same](#rule-same)
[Size](#rule-size)

</div>

#### Array

<div class="collection-method-list" markdown="1">

[Array](#rule-array)
[Between](#rule-between)
[Contains](#rule-contains)
[Doesnt Contain](#rule-doesnt-contain)
[Distinct](#rule-distinct)
[In Array](#rule-in-array)
[In Array Keys](#rule-in-array-keys)
[List](#rule-list)
[Max](#rule-max)
[Min](#rule-min)
[Size](#rule-size)

</div>

#### Date

<div class="collection-method-list" markdown="1">

[After](#rule-after)
[After Or Equal](#rule-after-or-equal)
[Before](#rule-before)
[Before Or Equal](#rule-before-or-equal)
[Date](#rule-date)
[Date Equals](#rule-date-equals)
[Date Format](#rule-date-format)
[Different](#rule-different)
[Timezone](#rule-timezone)

</div>

#### File

<div class="collection-method-list" markdown="1">

[Between](#rule-between)
[Dimensions](#rule-dimensions)
[Encoding](#rule-encoding)
[Extensions](#rule-extensions)
[File](#rule-file)
[Image](#rule-image)
[Max](#rule-max)
[MIME Types](#rule-mimetypes)
[MIME Type By File Extension](#rule-mimes)
[Size](#rule-size)

</div>

#### Database

<div class="collection-method-list" markdown="1">

[Exists](#rule-exists)
[Unique](#rule-unique)

</div>

#### Utility

<div class="collection-method-list" markdown="1">

[Any Of](#rule-anyof)
[Bail](#rule-bail)
[Exclude](#rule-exclude)
[Exclude If](#rule-exclude-if)
[Exclude Unless](#rule-exclude-unless)
[Exclude With](#rule-exclude-with)
[Exclude Without](#rule-exclude-without)
[Filled](#rule-filled)
[Missing](#rule-missing)
[Missing If](#rule-missing-if)
[Missing Unless](#rule-missing-unless)
[Missing With](#rule-missing-with)
[Missing With All](#rule-missing-with-all)
[Nullable](#rule-nullable)
[Present](#rule-present)
[Present If](#rule-present-if)
[Present Unless](#rule-present-unless)
[Present With](#rule-present-with)
[Present With All](#rule-present-with-all)
[Prohibited](#rule-prohibited)
[Prohibited If](#rule-prohibited-if)
[Prohibited If Accepted](#rule-prohibited-if-accepted)
[Prohibited If Declined](#rule-prohibited-if-declined)
[Prohibited Unless](#rule-prohibited-unless)
[Prohibits](#rule-prohibits)
[Required](#rule-required)
[Required If](#rule-required-if)
[Required If Accepted](#rule-required-if-accepted)
[Required If Declined](#rule-required-if-declined)
[Required Unless](#rule-required-unless)
[Required With](#rule-required-with)
[Required With All](#rule-required-with-all)
[Required Without](#rule-required-without)
[Required Without All](#rule-required-without-all)
[Required Array Keys](#rule-required-array-keys)
[Sometimes](#validating-when-present)

</div>

<a name="rule-accepted"></a>
#### accepted

O campo deve ser `"yes"`, `"on"`, `1`, `"1"`, `true` ou `"true"`. Comumente usado para cenários como verificar concordância do usuário com os termos de serviço.

<a name="rule-accepted-if"></a>
#### accepted_if:anotherfield,value,...

Quando outro campo for igual ao valor especificado, o campo deve ser `"yes"`, `"on"`, `1`, `"1"`, `true` ou `"true"`. Comumente usado para cenários de concordância condicional.

<a name="rule-active-url"></a>
#### active_url

O campo deve ter um registro A ou AAAA válido. Esta regra primeiro usa `parse_url` para extrair o hostname da URL, depois valida com `dns_get_record`.

<a name="rule-after"></a>
#### after:_date_

O campo deve ser um valor após a data fornecida. A data é passada para `strtotime` para converter em um `DateTime` válido:

```php
use support\validation\Validator;

Validator::make($data, [
    'start_date' => 'required|date|after:tomorrow',
])->validate();
```

Você também pode passar outro nome de campo para comparação:

```php
Validator::make($data, [
    'finish_date' => 'required|date|after:start_date',
])->validate();
```

Você pode usar o construtor fluente da regra `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->after(\Carbon\Carbon::today()->addDays(7)),
    ],
])->validate();
```

`afterToday` e `todayOrAfter` expressam convenientemente "deve ser após hoje" ou "deve ser hoje ou posterior":

```php
Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->afterToday(),
    ],
])->validate();
```

<a name="rule-after-or-equal"></a>
#### after_or_equal:_date_

O campo deve ser na ou após a data fornecida. Consulte [after](#rule-after) para mais detalhes.

Você pode usar o construtor fluente da regra `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->afterOrEqual(\Carbon\Carbon::today()->addDays(7)),
    ],
])->validate();
```

<a name="rule-anyof"></a>
#### anyOf

`Rule::anyOf` permite especificar "satisfazer qualquer um dos conjuntos de regras". Por exemplo, a seguinte regra significa que `username` deve ser um endereço de e-mail ou uma string alfanumérica/underscore/hífen de pelo menos 6 caracteres:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'username' => [
        'required',
        Rule::anyOf([
            ['string', 'email'],
            ['string', 'alpha_dash', 'min:6'],
        ]),
    ],
])->validate();
```

<a name="rule-alpha"></a>
#### alpha

O campo deve ser letras Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=) e [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=)).

Para permitir apenas ASCII (`a-z`, `A-Z`), adicione a opção `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha:ascii',
])->validate();
```

<a name="rule-alpha-dash"></a>
#### alpha_dash

O campo pode conter apenas letras e números Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)), mais hífen ASCII (`-`) e underscore (`_`).

Para permitir apenas ASCII (`a-z`, `A-Z`, `0-9`), adicione a opção `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_dash:ascii',
])->validate();
```

<a name="rule-alpha-num"></a>
#### alpha_num

O campo pode conter apenas letras e números Unicode ([\p{L}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AL%3A%5D&g=&i=), [\p{M}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AM%3A%5D&g=&i=), [\p{N}](https://util.unicode.org/UnicodeJsps/list-unicodeset.jsp?a=%5B%3AN%3A%5D&g=&i=)).

Para permitir apenas ASCII (`a-z`, `A-Z`, `0-9`), adicione a opção `ascii`:

```php
Validator::make($data, [
    'username' => 'alpha_num:ascii',
])->validate();
```

<a name="rule-array"></a>
#### array

O campo deve ser um `array` PHP.

Quando a regra `array` tem parâmetros extras, as chaves do array de entrada devem estar na lista de parâmetros. No exemplo, a chave `admin` não está na lista permitida, portanto é inválida:

```php
use support\validation\Validator;

$input = [
    'user' => [
        'name' => 'Taylor Otwell',
        'username' => 'taylorotwell',
        'admin' => true,
    ],
];

Validator::make($input, [
    'user' => 'array:name,username',
])->validate();
```

Recomenda-se definir explicitamente as chaves de array permitidas em projetos reais.

<a name="rule-ascii"></a>
#### ascii

O campo pode conter apenas caracteres ASCII de 7 bits.

<a name="rule-bail"></a>
#### bail

Interrompe a validação de regras subsequentes para o campo quando a primeira regra falha.

Esta regra afeta apenas o campo atual. Para "parar na primeira falha globalmente", use o validador do Illuminate diretamente e chame `stopOnFirstFailure()`.

<a name="rule-before"></a>
#### before:_date_

O campo deve ser anterior à data fornecida. A data é passada para `strtotime` para converter em um `DateTime` válido. Como [after](#rule-after), você pode passar outro nome de campo para comparação.

Você pode usar o construtor fluente da regra `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->before(\Carbon\Carbon::today()->subDays(7)),
    ],
])->validate();
```

`beforeToday` e `todayOrBefore` expressam convenientemente "deve ser antes de hoje" ou "deve ser hoje ou anterior":

```php
Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->beforeToday(),
    ],
])->validate();
```

<a name="rule-before-or-equal"></a>
#### before_or_equal:_date_

O campo deve ser na ou antes da data fornecida. A data é passada para `strtotime` para converter em um `DateTime` válido. Como [after](#rule-after), você pode passar outro nome de campo para comparação.

Você pode usar o construtor fluente da regra `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->beforeOrEqual(\Carbon\Carbon::today()->subDays(7)),
    ],
])->validate();
```

<a name="rule-between"></a>
#### between:_min_,_max_

O tamanho do campo deve estar entre _min_ e _max_ (inclusive). A avaliação para strings, números, arrays e arquivos é a mesma que [size](#rule-size).

<a name="rule-boolean"></a>
#### boolean

O campo deve ser convertível para boolean. Entradas aceitáveis incluem `true`, `false`, `1`, `0`, `"1"`, `"0"`.

Use o parâmetro `strict` para permitir apenas `true` ou `false`:

```php
Validator::make($data, [
    'foo' => 'boolean:strict',
])->validate();
```

<a name="rule-confirmed"></a>
#### confirmed

O campo deve ter um campo correspondente `{field}_confirmation`. Por exemplo, quando o campo é `password`, `password_confirmation` é obrigatório.

Você também pode especificar um nome de campo de confirmação personalizado, ex: `confirmed:repeat_username` exige que `repeat_username` corresponda ao campo atual.

<a name="rule-contains"></a>
#### contains:_foo_,_bar_,...

O campo deve ser um array e deve conter todos os valores de parâmetro fornecidos. Esta regra é comumente usada para validação de array; você pode usar `Rule::contains` para construí-la:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::contains(['admin', 'editor']),
    ],
])->validate();
```

<a name="rule-doesnt-contain"></a>
#### doesnt_contain:_foo_,_bar_,...

O campo deve ser um array e não deve conter nenhum dos valores de parâmetro fornecidos. Você pode usar `Rule::doesntContain` para construí-la:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'roles' => [
        'required',
        'array',
        Rule::doesntContain(['admin', 'editor']),
    ],
])->validate();
```

<a name="rule-current-password"></a>
#### current_password

O campo deve corresponder à senha do usuário autenticado atual. Você pode especificar o guard de autenticação como primeiro parâmetro:

```php
Validator::make($data, [
    'password' => 'current_password:api',
])->validate();
```

> [!WARNING]
> Esta regra depende do componente de autenticação e da configuração do guard; não use quando a autenticação não estiver integrada.

<a name="rule-date"></a>
#### date

O campo deve ser uma data válida (não relativa) reconhecível por `strtotime`.

<a name="rule-date-equals"></a>
#### date_equals:_date_

O campo deve ser igual à data fornecida. A data é passada para `strtotime` para converter em um `DateTime` válido.

<a name="rule-date-format"></a>
#### date_format:_format_,...

O campo deve corresponder a um dos formatos fornecidos. Use `date` ou `date_format`. Esta regra suporta todos os formatos [DateTime](https://www.php.net/manual/en/class.datetime.php) do PHP.

Você pode usar o construtor fluente da regra `date`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'start_date' => [
        'required',
        Rule::date()->format('Y-m-d'),
    ],
])->validate();
```

<a name="rule-decimal"></a>
#### decimal:_min_,_max_

O campo deve ser numérico com as casas decimais necessárias:

```php
Validator::make($data, [
    'price' => 'decimal:2',
])->validate();

Validator::make($data, [
    'price' => 'decimal:2,4',
])->validate();
```

<a name="rule-declined"></a>
#### declined

O campo deve ser `"no"`, `"off"`, `0`, `"0"`, `false` ou `"false"`.

<a name="rule-declined-if"></a>
#### declined_if:anotherfield,value,...

Quando outro campo for igual ao valor especificado, o campo deve ser `"no"`, `"off"`, `0`, `"0"`, `false` ou `"false"`.

<a name="rule-different"></a>
#### different:_field_

O campo deve ser diferente de _field_.

<a name="rule-digits"></a>
#### digits:_value_

O campo deve ser um inteiro com comprimento _value_.

<a name="rule-digits-between"></a>
#### digits_between:_min_,_max_

O campo deve ser um inteiro com comprimento entre _min_ e _max_.

<a name="rule-dimensions"></a>
#### dimensions

O campo deve ser uma imagem e satisfazer as restrições de dimensão:

```php
Validator::make($data, [
    'avatar' => 'dimensions:min_width=100,min_height=200',
])->validate();
```

Restrições disponíveis: _min\_width_, _max\_width_, _min\_height_, _max\_height_, _width_, _height_, _ratio_.

_ratio_ é a proporção de aspecto; pode ser expressa como fração ou float:

```php
Validator::make($data, [
    'avatar' => 'dimensions:ratio=3/2',
])->validate();
```

Esta regra tem muitos parâmetros; recomenda-se usar `Rule::dimensions` para construí-la:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'avatar' => [
        'required',
        Rule::dimensions()
            ->maxWidth(1000)
            ->maxHeight(500)
            ->ratio(3 / 2),
    ],
])->validate();
```

<a name="rule-distinct"></a>
#### distinct

Ao validar arrays, os valores do campo não devem ser duplicados:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct',
])->validate();
```

Usa comparação frouxa por padrão. Adicione `strict` para comparação estrita:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:strict',
])->validate();
```

Adicione `ignore_case` para ignorar diferenças de maiúsculas/minúsculas:

```php
Validator::make($data, [
    'foo.*.id' => 'distinct:ignore_case',
])->validate();
```

<a name="rule-doesnt-start-with"></a>
#### doesnt_start_with:_foo_,_bar_,...

O campo não deve começar com nenhum dos valores especificados.

<a name="rule-doesnt-end-with"></a>
#### doesnt_end_with:_foo_,_bar_,...

O campo não deve terminar com nenhum dos valores especificados.

<a name="rule-email"></a>
#### email

O campo deve ser um endereço de e-mail válido. Esta regra depende de [egulias/email-validator](https://github.com/egulias/EmailValidator), usa `RFCValidation` por padrão e pode usar outros métodos de validação:

```php
Validator::make($data, [
    'email' => 'email:rfc,dns',
])->validate();
```

Métodos de validação disponíveis:

<div class="content-list" markdown="1">

- `rfc`: `RFCValidation` - Valida e-mail conforme especificação RFC ([RFCs suportados](https://github.com/egulias/EmailValidator?tab=readme-ov-file#supported-rfcs)).
- `strict`: `NoRFCWarningsValidation` - Falha em avisos RFC (ex: ponto final ou pontos consecutivos).
- `dns`: `DNSCheckValidation` - Verifica se o domínio tem registros MX válidos.
- `spoof`: `SpoofCheckValidation` - Previne caracteres Unicode homógrafos ou de spoofing.
- `filter`: `FilterEmailValidation` - Valida usando PHP `filter_var`.
- `filter_unicode`: `FilterEmailValidation::unicode()` - Validação `filter_var` permitindo Unicode.

</div>

Você pode usar o construtor fluente de regras:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::email()
            ->rfcCompliant(strict: false)
            ->validateMxRecord()
            ->preventSpoofing(),
    ],
])->validate();
```

> [!WARNING]
> `dns` e `spoof` requerem a extensão PHP `intl`.

<a name="rule-encoding"></a>
#### encoding:*encoding_type*

O campo deve corresponder à codificação de caracteres especificada. Esta regra usa `mb_check_encoding` para detectar codificação de arquivo ou string. Pode ser usada com o construtor de regras de arquivo:

```php
use Illuminate\Validation\Rules\File;
use support\validation\Validator;

Validator::make($data, [
    'attachment' => [
        'required',
        File::types(['csv'])->encoding('utf-8'),
    ],
])->validate();
```

<a name="rule-ends-with"></a>
#### ends_with:_foo_,_bar_,...

O campo deve terminar com um dos valores especificados.

<a name="rule-enum"></a>
#### enum

`Enum` é uma regra baseada em classe para validar que o valor do campo é um valor enum válido. Passe o nome da classe enum ao construir. Para valores primitivos, use Backed Enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [Rule::enum(ServerStatus::class)],
])->validate();
```

Use `only`/`except` para restringir valores enum:

```php
use app\enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)
            ->only([ServerStatus::Pending, ServerStatus::Active]),
    ],
])->validate();

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)
            ->except([ServerStatus::Pending, ServerStatus::Active]),
    ],
])->validate();
```

Use `when` para restrições condicionais:

```php
use app\Enums\ServerStatus;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'status' => [
        Rule::enum(ServerStatus::class)->when(
            $isAdmin,
            fn ($rule) => $rule->only(ServerStatus::Active),
            fn ($rule) => $rule->only(ServerStatus::Pending),
        ),
    ],
])->validate();
```

<a name="rule-exclude"></a>
#### exclude

O campo será excluído dos dados retornados por `validate`/`validated`.

<a name="rule-exclude-if"></a>
#### exclude_if:_anotherfield_,_value_

Quando _anotherfield_ for igual a _value_, o campo será excluído dos dados retornados por `validate`/`validated`.

Para condições complexas, use `Rule::excludeIf`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::excludeIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::excludeIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-exclude-unless"></a>
#### exclude_unless:_anotherfield_,_value_

A menos que _anotherfield_ seja igual a _value_, o campo será excluído dos dados retornados por `validate`/`validated`. Se _value_ for `null` (ex: `exclude_unless:name,null`), o campo só é mantido quando o campo de comparação for `null` ou ausente.

<a name="rule-exclude-with"></a>
#### exclude_with:_anotherfield_

Quando _anotherfield_ existir, o campo será excluído dos dados retornados por `validate`/`validated`.

<a name="rule-exclude-without"></a>
#### exclude_without:_anotherfield_

Quando _anotherfield_ não existir, o campo será excluído dos dados retornados por `validate`/`validated`.

<a name="rule-exists"></a>
#### exists:_table_,_column_

O campo deve existir na tabela do banco de dados especificada.

<a name="basic-usage-of-exists-rule"></a>
#### Uso básico da regra Exists

```php
Validator::make($data, [
    'state' => 'exists:states',
])->validate();
```

Quando `column` não é especificado, o nome do campo é usado por padrão. Então este exemplo valida se a coluna `state` existe na tabela `states`.

<a name="specifying-a-custom-column-name"></a>
#### Especificando um nome de coluna personalizado

Acrescente o nome da coluna após o nome da tabela:

```php
Validator::make($data, [
    'state' => 'exists:states,abbreviation',
])->validate();
```

Para especificar uma conexão de banco de dados, prefixe o nome da tabela com o nome da conexão:

```php
Validator::make($data, [
    'email' => 'exists:connection.staff,email',
])->validate();
```

Você também pode passar um nome de classe de modelo; o framework resolverá o nome da tabela:

```php
Validator::make($data, [
    'user_id' => 'exists:app\model\User,id',
])->validate();
```

Para condições de consulta personalizadas, use o construtor `Rule`:

```php
use Illuminate\Database\Query\Builder;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::exists('staff')->where(function (Builder $query) {
            $query->where('account_id', 1);
        }),
    ],
])->validate();
```

Você também pode especificar o nome da coluna diretamente em `Rule::exists`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'state' => [Rule::exists('states', 'abbreviation')],
])->validate();
```

Para validar que um conjunto de valores existe, combine com a regra `array`:

```php
Validator::make($data, [
    'states' => ['array', Rule::exists('states', 'abbreviation')],
])->validate();
```

Quando tanto `array` quanto `exists` estão presentes, uma única consulta valida todos os valores.

<a name="rule-extensions"></a>
#### extensions:_foo_,_bar_,...

Valida que a extensão do arquivo enviado está na lista permitida:

```php
Validator::make($data, [
    'photo' => ['required', 'extensions:jpg,png'],
])->validate();
```

> [!WARNING]
> Não confie apenas na extensão para validação de tipo de arquivo; use junto com [mimes](#rule-mimes) ou [mimetypes](#rule-mimetypes).

<a name="rule-file"></a>
#### file

O campo deve ser um arquivo enviado com sucesso.

<a name="rule-filled"></a>
#### filled

Quando o campo existir, seu valor não deve estar vazio.

<a name="rule-gt"></a>
#### gt:_field_

O campo deve ser maior que o _field_ ou _value_ fornecido. Ambos os campos devem ter o mesmo tipo. A avaliação para strings, números, arrays e arquivos é a mesma que [size](#rule-size).

<a name="rule-gte"></a>
#### gte:_field_

O campo deve ser maior ou igual ao _field_ ou _value_ fornecido. Ambos os campos devem ter o mesmo tipo. A avaliação para strings, números, arrays e arquivos é a mesma que [size](#rule-size).

<a name="rule-hex-color"></a>
#### hex_color

O campo deve ser um [valor de cor hexadecimal](https://developer.mozilla.org/en-US/docs/Web/CSS/hex-color) válido.

<a name="rule-image"></a>
#### image

O campo deve ser uma imagem (jpg, jpeg, png, bmp, gif ou webp).

> [!WARNING]
> SVG não é permitido por padrão devido ao risco de XSS. Para permitir, adicione `allow_svg`: `image:allow_svg`.

<a name="rule-in"></a>
#### in:_foo_,_bar_,...

O campo deve estar na lista de valores fornecida. Você pode usar `Rule::in` para construir:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'zones' => [
        'required',
        Rule::in(['first-zone', 'second-zone']),
    ],
])->validate();
```

Quando combinado com a regra `array`, cada valor no array de entrada deve estar na lista `in`:

```php
use support\validation\Rule;
use support\validation\Validator;

$input = [
    'airports' => ['NYC', 'LAS'],
];

Validator::make($input, [
    'airports' => [
        'required',
        'array',
    ],
    'airports.*' => Rule::in(['NYC', 'LIT']),
])->validate();
```

<a name="rule-in-array"></a>
#### in_array:_anotherfield_.*

O campo deve existir na lista de valores de _anotherfield_.

<a name="rule-in-array-keys"></a>
#### in_array_keys:_value_.*

O campo deve ser um array e deve conter pelo menos um dos valores fornecidos como chave:

```php
Validator::make($data, [
    'config' => 'array|in_array_keys:timezone',
])->validate();
```

<a name="rule-integer"></a>
#### integer

O campo deve ser um inteiro.

Use o parâmetro `strict` para exigir que o tipo do campo seja inteiro; inteiros em string serão inválidos:

```php
Validator::make($data, [
    'age' => 'integer:strict',
])->validate();
```

> [!WARNING]
> Esta regra apenas valida se passa no `FILTER_VALIDATE_INT` do PHP; para tipos numéricos estritos, use junto com [numeric](#rule-numeric).

<a name="rule-ip"></a>
#### ip

O campo deve ser um endereço IP válido.

<a name="rule-ipv4"></a>
#### ipv4

O campo deve ser um endereço IPv4 válido.

<a name="rule-ipv6"></a>
#### ipv6

O campo deve ser um endereço IPv6 válido.

<a name="rule-json"></a>
#### json

O campo deve ser uma string JSON válida.

<a name="rule-lt"></a>
#### lt:_field_

O campo deve ser menor que o _field_ fornecido. Ambos os campos devem ter o mesmo tipo. A avaliação para strings, números, arrays e arquivos é a mesma que [size](#rule-size).

<a name="rule-lte"></a>
#### lte:_field_

O campo deve ser menor ou igual ao _field_ fornecido. Ambos os campos devem ter o mesmo tipo. A avaliação para strings, números, arrays e arquivos é a mesma que [size](#rule-size).

<a name="rule-lowercase"></a>
#### lowercase

O campo deve estar em minúsculas.

<a name="rule-list"></a>
#### list

O campo deve ser um array lista. As chaves do array lista devem ser números consecutivos de 0 a `count($array) - 1`.

<a name="rule-mac"></a>
#### mac_address

O campo deve ser um endereço MAC válido.

<a name="rule-max"></a>
#### max:_value_

O campo deve ser menor ou igual a _value_. A avaliação para strings, números, arrays e arquivos é a mesma que [size](#rule-size).

<a name="rule-max-digits"></a>
#### max_digits:_value_

O campo deve ser um inteiro com comprimento não excedendo _value_.

<a name="rule-mimetypes"></a>
#### mimetypes:_text/plain_,...

Valida que o tipo MIME do arquivo está na lista:

```php
Validator::make($data, [
    'video' => 'mimetypes:video/avi,video/mpeg,video/quicktime',
])->validate();
```

O tipo MIME é inferido pela leitura do conteúdo do arquivo e pode diferir do MIME fornecido pelo cliente.

<a name="rule-mimes"></a>
#### mimes:_foo_,_bar_,...

Valida que o tipo MIME do arquivo corresponde à extensão fornecida:

```php
Validator::make($data, [
    'photo' => 'mimes:jpg,bmp,png',
])->validate();
```

Embora os parâmetros sejam extensões, esta regra lê o conteúdo do arquivo para determinar o MIME. Mapeamento extensão-MIME:

[https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types](https://svn.apache.org/repos/asf/httpd/httpd/trunk/docs/conf/mime.types)

<a name="mime-types-and-extensions"></a>
#### Tipos MIME e extensões

Esta regra não valida que a "extensão do arquivo" corresponda ao "MIME real". Por exemplo, `mimes:png` trata `photo.txt` com conteúdo PNG como válido. Para validar extensão, use [extensions](#rule-extensions).

<a name="rule-min"></a>
#### min:_value_

O campo deve ser maior ou igual a _value_. A avaliação para strings, números, arrays e arquivos é a mesma que [size](#rule-size).

<a name="rule-min-digits"></a>
#### min_digits:_value_

O campo deve ser um inteiro com comprimento não inferior a _value_.

<a name="rule-multiple-of"></a>
#### multiple_of:_value_

O campo deve ser um múltiplo de _value_.

<a name="rule-missing"></a>
#### missing

O campo não deve existir nos dados de entrada.

<a name="rule-missing-if"></a>
#### missing_if:_anotherfield_,_value_,...

Quando _anotherfield_ for igual a qualquer _value_, o campo não deve existir.

<a name="rule-missing-unless"></a>
#### missing_unless:_anotherfield_,_value_

A menos que _anotherfield_ seja igual a qualquer _value_, o campo não deve existir.

<a name="rule-missing-with"></a>
#### missing_with:_foo_,_bar_,...

Quando qualquer campo especificado existir, o campo não deve existir.

<a name="rule-missing-with-all"></a>
#### missing_with_all:_foo_,_bar_,...

Quando todos os campos especificados existirem, o campo não deve existir.

<a name="rule-not-in"></a>
#### not_in:_foo_,_bar_,...

O campo não deve estar na lista de valores fornecida. Você pode usar `Rule::notIn` para construir:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'toppings' => [
        'required',
        Rule::notIn(['sprinkles', 'cherries']),
    ],
])->validate();
```

<a name="rule-not-regex"></a>
#### not_regex:_pattern_

O campo não deve corresponder à expressão regular fornecida.

Esta regra usa `preg_match` do PHP. O regex deve ter delimitadores, ex: `'email' => 'not_regex:/^.+$/i'`.

> [!WARNING]
> Ao usar `regex`/`not_regex`, se o regex contiver `|`, use a forma array para evitar conflito com o separador `|`.

<a name="rule-nullable"></a>
#### nullable

O campo pode ser `null`.

<a name="rule-numeric"></a>
#### numeric

O campo deve ser [numérico](https://www.php.net/manual/en/function.is-numeric.php).

Use o parâmetro `strict` para permitir apenas tipos inteiro ou float; strings numéricas serão inválidas:

```php
Validator::make($data, [
    'amount' => 'numeric:strict',
])->validate();
```

<a name="rule-present"></a>
#### present

O campo deve existir nos dados de entrada.

<a name="rule-present-if"></a>
#### present_if:_anotherfield_,_value_,...

Quando _anotherfield_ for igual a qualquer _value_, o campo deve existir.

<a name="rule-present-unless"></a>
#### present_unless:_anotherfield_,_value_

A menos que _anotherfield_ seja igual a qualquer _value_, o campo deve existir.

<a name="rule-present-with"></a>
#### present_with:_foo_,_bar_,...

Quando qualquer campo especificado existir, o campo deve existir.

<a name="rule-present-with-all"></a>
#### present_with_all:_foo_,_bar_,...

Quando todos os campos especificados existirem, o campo deve existir.

<a name="rule-prohibited"></a>
#### prohibited

O campo deve estar ausente ou vazio. "Vazio" significa:

<div class="content-list" markdown="1">

- Valor é `null`.
- Valor é string vazia.
- Valor é array vazio ou objeto `Countable` vazio.
- Arquivo enviado com caminho vazio.

</div>

<a name="rule-prohibited-if"></a>
#### prohibited_if:_anotherfield_,_value_,...

Quando _anotherfield_ for igual a qualquer _value_, o campo deve estar ausente ou vazio. "Vazio" significa:

<div class="content-list" markdown="1">

- Valor é `null`.
- Valor é string vazia.
- Valor é array vazio ou objeto `Countable` vazio.
- Arquivo enviado com caminho vazio.

</div>

Para condições complexas, use `Rule::prohibitedIf`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::prohibitedIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::prohibitedIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-prohibited-if-accepted"></a>
#### prohibited_if_accepted:_anotherfield_,...

Quando _anotherfield_ for `"yes"`, `"on"`, `1`, `"1"`, `true` ou `"true"`, o campo deve estar ausente ou vazio.

<a name="rule-prohibited-if-declined"></a>
#### prohibited_if_declined:_anotherfield_,...

Quando _anotherfield_ for `"no"`, `"off"`, `0`, `"0"`, `false` ou `"false"`, o campo deve estar ausente ou vazio.

<a name="rule-prohibited-unless"></a>
#### prohibited_unless:_anotherfield_,_value_,...

A menos que _anotherfield_ seja igual a qualquer _value_, o campo deve estar ausente ou vazio. "Vazio" significa:

<div class="content-list" markdown="1">

- Valor é `null`.
- Valor é string vazia.
- Valor é array vazio ou objeto `Countable` vazio.
- Arquivo enviado com caminho vazio.

</div>

<a name="rule-prohibits"></a>
#### prohibits:_anotherfield_,...

Quando o campo existir e não estiver vazio, todos os campos em _anotherfield_ devem estar ausentes ou vazios. "Vazio" significa:

<div class="content-list" markdown="1">

- Valor é `null`.
- Valor é string vazia.
- Valor é array vazio ou objeto `Countable` vazio.
- Arquivo enviado com caminho vazio.

</div>

<a name="rule-regex"></a>
#### regex:_pattern_

O campo deve corresponder à expressão regular fornecida.

Esta regra usa `preg_match` do PHP. O regex deve ter delimitadores, ex: `'email' => 'regex:/^.+@.+$/i'`.

> [!WARNING]
> Ao usar `regex`/`not_regex`, se o regex contiver `|`, use a forma array para evitar conflito com o separador `|`.

<a name="rule-required"></a>
#### required

O campo deve existir e não estar vazio. "Vazio" significa:

<div class="content-list" markdown="1">

- Valor é `null`.
- Valor é string vazia.
- Valor é array vazio ou objeto `Countable` vazio.
- Arquivo enviado com caminho vazio.

</div>

<a name="rule-required-if"></a>
#### required_if:_anotherfield_,_value_,...

Quando _anotherfield_ for igual a qualquer _value_, o campo deve existir e não estar vazio.

Para condições complexas, use `Rule::requiredIf`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'role_id' => Rule::requiredIf($isAdmin),
])->validate();

Validator::make($data, [
    'role_id' => Rule::requiredIf(fn () => $isAdmin),
])->validate();
```

<a name="rule-required-if-accepted"></a>
#### required_if_accepted:_anotherfield_,...

Quando _anotherfield_ for `"yes"`, `"on"`, `1`, `"1"`, `true` ou `"true"`, o campo deve existir e não estar vazio.

<a name="rule-required-if-declined"></a>
#### required_if_declined:_anotherfield_,...

Quando _anotherfield_ for `"no"`, `"off"`, `0`, `"0"`, `false` ou `"false"`, o campo deve existir e não estar vazio.

<a name="rule-required-unless"></a>
#### required_unless:_anotherfield_,_value_,...

A menos que _anotherfield_ seja igual a qualquer _value_, o campo deve existir e não estar vazio. Se _value_ for `null` (ex: `required_unless:name,null`), o campo pode estar vazio apenas quando o campo de comparação for `null` ou ausente.

<a name="rule-required-with"></a>
#### required_with:_foo_,_bar_,...

Quando qualquer campo especificado existir e não estiver vazio, o campo deve existir e não estar vazio.

<a name="rule-required-with-all"></a>
#### required_with_all:_foo_,_bar_,...

Quando todos os campos especificados existirem e não estiverem vazios, o campo deve existir e não estar vazio.

<a name="rule-required-without"></a>
#### required_without:_foo_,_bar_,...

Quando qualquer campo especificado estiver vazio ou ausente, o campo deve existir e não estar vazio.

<a name="rule-required-without-all"></a>
#### required_without_all:_foo_,_bar_,...

Quando todos os campos especificados estiverem vazios ou ausentes, o campo deve existir e não estar vazio.

<a name="rule-required-array-keys"></a>
#### required_array_keys:_foo_,_bar_,...

O campo deve ser um array e deve conter pelo menos as chaves especificadas.

<a name="validating-when-present"></a>
#### sometimes

Aplica regras de validação subsequentes apenas quando o campo existir. Comumente usado para campos "opcionais mas devem ser válidos quando presentes":

```php
Validator::make($data, [
    'nickname' => 'sometimes|string|max:20',
])->validate();
```

<a name="rule-same"></a>
#### same:_field_

O campo deve ser o mesmo que _field_.

<a name="rule-size"></a>
#### size:_value_

O tamanho do campo deve ser igual ao _value_ fornecido. Para strings: contagem de caracteres; para números: inteiro especificado (use com `numeric` ou `integer`); para arrays: contagem de elementos; para arquivos: tamanho em KB. Exemplo:

```php
Validator::make($data, [
    'title' => 'size:12',
    'seats' => 'integer|size:10',
    'tags' => 'array|size:5',
    'image' => 'file|size:512',
])->validate();
```

<a name="rule-starts-with"></a>
#### starts_with:_foo_,_bar_,...

O campo deve começar com um dos valores especificados.

<a name="rule-string"></a>
#### string

O campo deve ser uma string. Para permitir `null`, use junto com `nullable`.

<a name="rule-timezone"></a>
#### timezone

O campo deve ser um identificador de fuso horário válido (de `DateTimeZone::listIdentifiers`). Você pode passar parâmetros suportados por esse método:

```php
Validator::make($data, [
    'timezone' => 'required|timezone:all',
])->validate();

Validator::make($data, [
    'timezone' => 'required|timezone:Africa',
])->validate();

Validator::make($data, [
    'timezone' => 'required|timezone:per_country,US',
])->validate();
```

<a name="rule-unique"></a>
#### unique:_table_,_column_

O campo deve ser único na tabela especificada.

**Especificar nome de tabela/coluna personalizado:**

Você pode especificar o nome da classe do modelo diretamente:

```php
Validator::make($data, [
    'email' => 'unique:app\model\User,email_address',
])->validate();
```

Você pode especificar o nome da coluna (usa o nome do campo por padrão quando não especificado):

```php
Validator::make($data, [
    'email' => 'unique:users,email_address',
])->validate();
```

**Especificar conexão do banco de dados:**

```php
Validator::make($data, [
    'email' => 'unique:connection.users,email_address',
])->validate();
```

**Ignorar ID especificado:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        'required',
        Rule::unique('users')->ignore($user->id),
    ],
])->validate();
```

> [!WARNING]
> `ignore` não deve receber entrada do usuário; use apenas IDs únicos gerados pelo sistema (ID auto-incremento ou UUID do modelo), caso contrário pode haver risco de injeção SQL.

Você também pode passar uma instância do modelo:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user),
    ],
])->validate();
```

Se a chave primária não for `id`, especifique o nome da chave primária:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->ignore($user->id, 'user_id'),
    ],
])->validate();
```

Por padrão usa o nome do campo como coluna única; você também pode especificar o nome da coluna:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users', 'email_address')->ignore($user->id),
    ],
])->validate();
```

**Adicionar condições extras:**

```php
use Illuminate\Database\Query\Builder;
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [
        Rule::unique('users')->where(
            fn (Builder $query) => $query->where('account_id', 1)
        ),
    ],
])->validate();
```

**Ignorar registros com exclusão lógica:**

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed()],
])->validate();
```

Se a coluna de exclusão lógica não for `deleted_at`:

```php
use support\validation\Rule;
use support\validation\Validator;

Validator::make($data, [
    'email' => [Rule::unique('users')->withoutTrashed('was_deleted_at')],
])->validate();
```

<a name="rule-uppercase"></a>
#### uppercase

O campo deve estar em maiúsculas.

<a name="rule-url"></a>
#### url

O campo deve ser uma URL válida.

Você pode especificar protocolos permitidos:

```php
Validator::make($data, [
    'url' => 'url:http,https',
    'game' => 'url:minecraft,steam',
])->validate();
```

<a name="rule-ulid"></a>
#### ulid

O campo deve ser um [ULID](https://github.com/ulid/spec) válido.

<a name="rule-uuid"></a>
#### uuid

O campo deve ser um UUID RFC 9562 válido (versão 1, 3, 4, 5, 6, 7 ou 8).

Você pode especificar a versão:

```php
Validator::make($data, [
    'uuid' => 'uuid:4',
])->validate();
```



<a name="think-validate"></a>
# Validador top-think/think-validate

## Descrição

Validador oficial do ThinkPHP

## URL do Projeto

https://github.com/top-think/think-validate

## Instalação

`composer require topthink/think-validate`

## Início Rápido

**Criar `app/index/validate/User.php`**

```php
<?php
namespace app\index\validate;

use think\Validate;

class User extends Validate
{
    protected $rule =   [
        'name'  => 'require|max:25',
        'age'   => 'number|between:1,120',
        'email' => 'email',    
    ];

    protected $message  =   [
        'name.require' => 'Name is required',
        'name.max'     => 'Name cannot exceed 25 characters',
        'age.number'   => 'Age must be a number',
        'age.between'  => 'Age must be between 1 and 120',
        'email'        => 'Invalid email format',    
    ];

}
```
  
**Uso**

```php
$data = [
    'name'  => 'thinkphp',
    'email' => 'thinkphp@qq.com',
];

$validate = new \app\index\validate\User;

if (!$validate->check($data)) {
    var_dump($validate->getError());
}
```

> **Nota**
> O webman não suporta o método `Validate::rule()` do think-validate

<a name="respect-validation"></a>
# Validador workerman/validation

## Descrição

Este projeto é uma versão localizada de https://github.com/Respect/Validation

## URL do Projeto

https://github.com/walkor/validation
  
  
## Instalação
 
```php
composer require workerman/validation
```

## Início Rápido

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use support\Db;

class IndexController
{
    public function index(Request $request)
    {
        $data = v::input($request->post(), [
            'nickname' => v::length(1, 64)->setName('Nickname'),
            'username' => v::alnum()->length(5, 64)->setName('Username'),
            'password' => v::length(5, 64)->setName('Password')
        ]);
        Db::table('user')->insert($data);
        return json(['code' => 0, 'msg' => 'ok']);
    }
}  
```
  
**Acesso via jQuery**
  
  ```js
  $.ajax({
      url : 'http://127.0.0.1:8787',
      type : "post",
      dataType:'json',
      data : {nickname:'Tom', username:'tom cat', password: '123456'}
  });
  ```
  
Resultado:

`{"code":500,"msg":"Username may only contain letters (a-z) and numbers (0-9)"}`

Explicação:

`v::input(array $input, array $rules)` valida e coleta dados. Se a validação falhar, lança `Respect\Validation\Exceptions\ValidationException`; em caso de sucesso retorna os dados validados (array).

Se o código de negócio não capturar a exceção de validação, o framework webman a capturará e retornará JSON (como `{"code":500, "msg":"xxx"}`) ou uma página de exceção normal com base nos cabeçalhos HTTP. Se o formato da resposta não atender às suas necessidades, você pode capturar `ValidationException` e retornar dados personalizados, como no exemplo abaixo:

```php
<?php
namespace app\controller;

use support\Request;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\ValidationException;

class IndexController
{
    public function index(Request $request)
    {
        try {
            $data = v::input($request->post(), [
                'username' => v::alnum()->length(5, 64)->setName('Username'),
                'password' => v::length(5, 64)->setName('Password')
            ]);
        } catch (ValidationException $e) {
            return json(['code' => 500, 'msg' => $e->getMessage()]);
        }
        return json(['code' => 0, 'msg' => 'ok', 'data' => $data]);
    }
}
```

## Guia do Validador

```php
use Respect\Validation\Validator as v;

// Validação de regra única
$number = 123;
v::numericVal()->validate($number); // true

// Validação encadeada
$usernameValidator = v::alnum()->noWhitespace()->length(1, 15);
$usernameValidator->validate('alganet'); // true

// Obter primeiro motivo de falha na validação
try {
    $usernameValidator->setName('Username')->check('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getMessage(); // Username may only contain letters (a-z) and numbers (0-9)
}

// Obter todos os motivos de falha na validação
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    echo $exception->getFullMessage();
    // Será impresso
    // -  Username must satisfy the following rules
    //     - Username may only contain letters (a-z) and numbers (0-9)
    //     - Username must not contain whitespace
  
    var_export($exception->getMessages());
    // Será impresso
    // array (
    //   'alnum' => 'Username may only contain letters (a-z) and numbers (0-9)',
    //   'noWhitespace' => 'Username must not contain whitespace',
    // )
}

// Mensagens de erro personalizadas
try {
    $usernameValidator->setName('Username')->assert('alg  anet');
} catch (ValidationException $exception) {
    var_export($exception->getMessages([
        'alnum' => 'Username may only contain letters and numbers',
        'noWhitespace' => 'Username must not contain spaces',
        'length' => 'length satisfies the rule, so this will not be shown'
    ]));
    // Será impresso 
    // array(
    //    'alnum' => 'Username may only contain letters and numbers',
    //    'noWhitespace' => 'Username must not contain spaces'
    // )
}

// Validar objeto
$user = new stdClass;
$user->name = 'Alexandre';
$user->birthdate = '1987-07-01';
$userValidator = v::attribute('name', v::stringType()->length(1, 32))
                ->attribute('birthdate', v::date()->minAge(18));
$userValidator->validate($user); // true

// Validar array
$data = [
    'parentKey' => [
        'field1' => 'value1',
        'field2' => 'value2'
        'field3' => true,
    ]
];
v::key(
    'parentKey',
    v::key('field1', v::stringType())
        ->key('field2', v::stringType())
        ->key('field3', v::boolType())
    )
    ->assert($data); // Também pode usar check() ou validate()
  
// Validação opcional
v::alpha()->validate(''); // false 
v::alpha()->validate(null); // false 
v::optional(v::alpha())->validate(''); // true
v::optional(v::alpha())->validate(null); // true

// Regra de negação
v::not(v::intVal())->validate(10); // false
```
  
## Diferença entre os métodos do Validador `validate()` `check()` `assert()`

`validate()` retorna boolean, não lança exceção

`check()` lança exceção em falha de validação; obtém o primeiro motivo de falha via `$exception->getMessage()`

`assert()` lança exceção em falha de validação; obtém todos os motivos de falha via `$exception->getFullMessage()`
  
  
## Regras de Validação Comuns

`Alnum()` Apenas letras e números

`Alpha()` Apenas letras

`ArrayType()` Tipo array

`Between(mixed $minimum, mixed $maximum)` Valida que a entrada está entre dois valores.

`BoolType()` Valida tipo boolean

`Contains(mixed $expectedValue)` Valida que a entrada contém certo valor

`ContainsAny(array $needles)` Valida que a entrada contém pelo menos um valor definido

`Digit()` Valida que a entrada contém apenas dígitos

`Domain()` Valida nome de domínio válido

`Email()` Valida endereço de e-mail válido

`Extension(string $extension)` Valida extensão de arquivo

`FloatType()` Valida tipo float

`IntType()` Valida tipo inteiro

`Ip()` Valida endereço IP

`Json()` Valida dados JSON

`Length(int $min, int $max)` Valida que o comprimento está dentro do intervalo

`LessThan(mixed $compareTo)` Valida que o comprimento é menor que o valor fornecido

`Lowercase()` Valida minúsculas

`MacAddress()` Valida endereço MAC

`NotEmpty()` Valida não vazio

`NullType()` Valida null

`Number()` Valida número

`ObjectType()` Valida tipo objeto

`StringType()` Valida tipo string

`Url()` Valida URL
  
Consulte https://respect-validation.readthedocs.io/en/2.0/list-of-rules/ para mais regras de validação.
  
## Mais

Visite https://respect-validation.readthedocs.io/en/2.0/
  
