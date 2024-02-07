# Controlador

Crie um novo arquivo de controlador `app/controller/FooController.php`.

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

Quando acessar `http://127.0.0.1:8787/foo`, a página retornará `hello index`.

Quando acessar `http://127.0.0.1:8787/foo/hello`, a página retornará `hello webman`.

Obviamente, você pode alterar as regras de roteamento através da configuração de rota, consulte [Rotas](route.md).

> **Dica**
> Se ocorrer um erro 404 ao acessar, abra `config/app.php` e defina `controller_suffix` como `Controller` e reinicie.

## Sufixo do Controlador

A partir da versão 1.3 do webman, é possível definir um sufixo para os controladores em `config/app.php`. Se `controller_suffix` no arquivo `config/app.php` estiver definido como vazio `''`, então o controlador se parece com o seguinte:

`app\controller\Foo.php`.

```php
<?php
namespace app\controller;

use support\Request;

class Foo
{
    public function index(Request $request)
    {
        return response('hello index');
    }
    
    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```

É altamente recomendável definir o sufixo do controlador como `Controller`, dessa forma é possível evitar conflitos de nomes entre controladores e modelos, ao mesmo tempo que aumenta a segurança.

## Observações
 - O framework automaticamente passará um objeto `support\Request` para o controlador, através do qual é possível obter dados de entrada do usuário (como dados de GET, POST, cabeçalho, cookie, etc), consulte [Requisição](request.md)
 - Os controladores podem retornar números, strings ou objetos `support\Response`, mas não podem retornar outros tipos de dados.
 - O objeto `support\Response` pode ser criado através das funções auxiliares `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, entre outras.

## Ciclo de Vida do Controlador

Quando `config/app.php` tem `controller_reuse` definido como `false`, uma instância do controlador correspondente é inicializada para cada solicitação, e após o término da solicitação a instância do controlador é destruída, seguindo o mecanismo de operação dos frameworks tradicionais.

Quando `config/app.php` tem `controller_reuse` definido como `true`, todas as solicitações terão a reutilização da instância do controlador. Ou seja, uma vez que a instância do controlador é criada, ela permanece na memória e é reutilizada por todas as solicitações.

> **Observação**
> Desabilitar a reutilização do controlador requer webman>=1.4.0, ou seja, antes da versão 1.4.0, o controlador é reutilizado para todas as solicitações e não pode ser alterado.

> **Observação**
> Ao ativar a reutilização do controlador, as solicitações não devem alterar quaisquer propriedades do controlador, pois essas alterações afetarão as solicitações subsequentes, por exemplo

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    protected $model;
    
    public function update(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->update();
        return response('ok');
    }
    
    public function delete(Request $request, $id)
    {
        $model = $this->getModel($id);
        $model->delete();
        return response('ok');
    }
    
    protected function getModel($id)
    {
        // Esta função manterá o modelo após a primeira solicitação update?id=1
        // Caso uma nova solicitação delete?id=2 seja feita, o dado de id=1 será excluído
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Dica**
> Retornar dados em um construtor `__construct()` do controlador não terá efeito, por exemplo

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Retornar dados no construtor não terá efeito, o navegador não receberá essa resposta
        return response('hello'); 
    }
}
```

## Diferença entre Reutilização e Não Reutilização do Controlador
As diferenças são as seguintes

#### Não Reutilizar o Controlador
Uma nova instância do controlador é criada para cada solicitação, e após o término da solicitação, essa instância é liberada e a memória é recuperada. Não reutilizar o controlador é semelhante à operação dos frameworks tradicionais e está de acordo com a maioria dos hábitos de desenvolvedores. Como o controlador é criado e destruído repetidamente, o desempenho é ligeiramente inferior ao da reutilização do controlador (o desempenho de teste de carga simples helloworld é cerca de 10% inferior, mas pode ser ignorado em cenários com carga de trabalho real).

#### Reutilizar o Controlador
Se a reutilização estiver ativada, uma instância do controlador é criada uma vez por processo, e após o término da solicitação, essa instância do controlador não será liberada e será reutilizada pelas solicitações subsequentes desse processo. A reutilização do controlador tem um desempenho melhor, mas não está de acordo com a maioria dos hábitos de desenvolvedores.

#### Situações em que a reutilização do controlador não pode ser utilizada

Quando a solicitação altera as propriedades do controlador, a reutilização do controlador não pode ser utilizada, pois as alterações dessas propriedades afetarão as solicitações subsequentes.

Alguns desenvolvedores gostam de fazer inicializações específicas para cada solicitação dentro do construtor `__construct()` do controlador, nesses casos, a reutilização do controlador não pode ser utilizada, já que o construtor do processo atual só será chamado uma vez e não em cada solicitação.
