# Manipulação de Exceções

## Configuração
`config/exception.php`
```php
return [
    // Configure aqui a classe de manipulação de exceções
    '' => support\exception\Handler::class,
];
```
No modo de aplicação múltipla, é possível configurar uma classe de manipulação de exceções para cada aplicação individualmente, consulte [Múltiplas Aplicações](multiapp.md) para mais detalhes.


## Classe Padrão de Manipulação de Exceções
No webman, as exceções são normalmente tratadas pela classe `support\exception\Handler`. Você pode modificar a classe de tratamento de exceções padrão alterando o arquivo de configuração `config/exception.php`. A classe de tratamento de exceções deve implementar a interface `Webman\Exception\ExceptionHandlerInterface`.
```php
interface ExceptionHandlerInterface
{
    /**
     * Registra o log
     * @param Throwable $e
     * @return mixed
     */
    public function report(Throwable $e);

    /**
     * Renderiza a resposta
     * @param Request $request
     * @param Throwable $e
     * @return Response
     */
    public function render(Request $request, Throwable $e): Response;
}
```

## Renderização da Resposta
O método `render` na classe de tratamento de exceções é utilizado para renderizar a resposta.

Se o valor de `debug` no arquivo de configuração `config/app.php` for `true` (abreviado como `app.debug=true` a seguir), serão retornadas informações detalhadas sobre a exceção. Caso contrário, serão retornadas informações resumidas.

Se a requisição esperar uma resposta em formato JSON, as informações da exceção serão retornadas em formato JSON, como no exemplo a seguir:
```json
{
    "code": "500",
    "msg": "Informações da Exceção"
}
```
Se `app.debug=true`, os dados JSON incluirão adicionalmente um campo `trace`, fornecendo detalhes completos da pilha de chamadas.

Você pode escrever sua própria classe de tratamento de exceções para alterar a lógica padrão de tratamento de exceções.

# Exceção de Negócios BusinessException
Às vezes, é desejável interromper uma solicitação em uma função aninhada e retornar uma mensagem de erro para o cliente. Para isso, pode-se lançar uma `BusinessException`. Por exemplo:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->checkInput($request->post());
        return response('Olá, índice');
    }
    
    protected function checkInput($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Erro de parâmetro', 3000);
        }
    }
}
```

O exemplo acima retornará:
```json
{"code": 3000, "msg": "Erro de parâmetro"}
```

> **Observação**
> A exceção de negócios BusinessException não precisa ser capturada pelo bloco try-catch, o framework captura e retorna a saída apropriada automaticamente de acordo com o tipo de requisição.

## Exceção de Negócios Personalizada

Se a resposta mencionada acima não atender aos requisitos, como a necessidade de alterar `msg` para `message`, é possível criar uma exceção personalizada `MyBusinessException`.

Crie o arquivo `app/exception/MyBusinessException.php` com o seguinte conteúdo:
```php
<?php

namespace app\exception;

use support\exception\BusinessException;
use Webman\Http\Request;
use Webman\Http\Response;

class MyBusinessException extends BusinessException
{
    public function render(Request $request): ?Response
    {
        // Retorna dados JSON para solicitações em JSON
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Retorna uma página para solicitações não em JSON
        return new Response(200, [], $this->getMessage());
    }
}
```

Dessa forma, ao chamar:
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Erro de parâmetro', 3000);
```
Uma solicitação JSON receberá uma resposta semelhante à seguinte:
```json
{"code": 3000, "message": "Erro de parâmetro"}
```

> **Dica**
> Como a exceção BusinessException é uma exceção de negócios (por exemplo, erro nos parâmetros fornecidos pelo usuário), é previsível, logo o framework não a considera como um erro fatal e não a registra no log.

## Conclusão
Quando houver a necessidade de interromper a solicitação atual e retornar informações para o cliente, considere o uso da exceção `BusinessException`.
