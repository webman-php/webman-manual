# Gestão de Exceções

## Configuração
`config/exception.php`
```php
return [
    // Aqui você configura a classe de tratamento de exceções
    '' => support\exception\Handler::class,
];
```
Para o modo de várias aplicações, você pode configurar uma classe de tratamento de exceções para cada aplicação, consulte [Múltiplas Aplicações](multiapp.md).

## Classe de Tratamento de Exceções Padrão
No webman, as exceções são tratadas por padrão pela classe `support\exception\Handler`. Você pode alterar a classe de tratamento de exceções padrão modificando o arquivo de configuração `config/exception.php`. A classe de tratamento de exceções deve implementar a interface `Webman\Exception\ExceptionHandlerInterface`.
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
    public function render(Request $request, Throwable $e) : Response;
}
```


## Renderizar Resposta
O método `render` na classe de tratamento de exceções é usado para renderizar a resposta.

Se o valor `debug` no arquivo de configuração `config/app.php` for `true` (doravante referido como `app.debug=true`), detalhes da exceção serão retornados. Caso contrário, informações resumidas serão retornadas.

Se a solicitação espera uma resposta em formato JSON, as informações da exceção serão retornadas no formato JSON, por exemplo:
```json
{
    "code": "500",
    "msg": "informações da exceção"
}
```
Se `app.debug=true`, dados JSON adicionarão um campo adicional `trace` para fornecer uma pilha de execução detalhada.

Você pode escrever sua própria classe de tratamento de exceções para alterar a lógica padrão de tratamento de exceções.

# Exceção de Negócio BusinessException
Às vezes, queremos interromper uma solicitação dentro de uma função aninhada e retornar uma mensagem de erro para o cliente. Nesses casos, você pode lançar uma `BusinessException`.
Por exemplo:

```php
<?php
namespace app\controller;

use support\Request;
use support\exception\BusinessException;

class FooController
{
    public function index(Request $request)
    {
        $this->chackInpout($request->post());
        return response('hello index');
    }
    
    protected function chackInpout($input)
    {
        if (!isset($input['token'])) {
            throw new BusinessException('Erro nos parâmetros', 3000);
        }
    }
}
```

O exemplo acima retornará:
```json
{"code": 3000, "msg": "Erro nos parâmetros"}
```
> **Observação**
> A exceção de negócio BusinessException não precisa ser capturada pela exceção de negócio try. O framework irá capturá-la automaticamente e retornar a saída apropriada com base no tipo de solicitação.

## Exceção de Negócio Personalizada
Se a resposta acima não atender às suas necessidades, por exemplo, se quiser mudar `msg` para `message`, você pode criar uma exceção personalizada `MyBusinessException`.

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
        // Retorna dados JSON para solicitação JSON
        if ($request->expectsJson()) {
            return json(['code' => $this->getCode() ?: 500, 'message' => $this->getMessage()]);
        }
        // Caso contrário, retorna uma página
        return new Response(200, [], $this->getMessage());
    }
}
```

Dessa forma, ao chamar
```php
use app\exception\MyBusinessException;

throw new MyBusinessException('Erro nos parâmetros', 3000);
```
A solicitação JSON receberá uma resposta semelhante a:
```json
{"code": 3000, "message": "Erro nos parâmetros"}
```

> **Dica**
> Como a exceção BusinessException é uma exceção previsível (como erro nos parâmetros de entrada do usuário), o framework não a considera como um erro fatal e não registra isso no log.

## Conclusão
Sempre que quiser interromper a solicitação atual e retornar informações para o cliente, considere usar a exceção `BusinessException`.
