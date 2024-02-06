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

Você também pode alterar as regras de roteamento através da configuração das rotas, veja [Roteamento](route.md).

> **Dica**
> Se ocorrer um erro 404, abra o arquivo `config/app.php` e defina `controller_suffix` como `Controller`, depois reinicie o servidor.

## Sufixo do Controlador
A partir da versão 1.3, o webman suporta a definição de um sufixo para os controladores em `config/app.php`. Se `controller_suffix` estiver definido como uma string vazia `''`, os controladores terão a seguinte semântica

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

É altamente recomendável definir o sufixo do controlador como `Controller`, isso evitará conflitos de nome entre controladores e modelos, além de aumentar a segurança.

## Observações
 - O framework automaticamente passará o objeto `support\Request` para o controlador, através dele é possível acessar os dados de entrada dos usuários (dados GET, POST, cabeçalhos, cookies, etc), consulte [Requisição](request.md)
 - O controlador pode retornar números, strings ou objetos `support\Response`, mas não pode retornar outros tipos de dados.
 - O objeto `support\Response` pode ser criado através das funções auxiliares `response()`, `json()`, `xml()`, `jsonp()`, `redirect()`, entre outras.

## Ciclo de vida do Controlador

Quando `controller_reuse` em `config/app.php` está definido como `false`, uma nova instância do controlador correspondente será inicializada a cada requisição, e após o término da requisição, a instância do controlador será destruída, seguindo o mecanismo tradicional de frameworks.

Quando `controller_reuse` em `config/app.php` está definido como `true`, todas as requisições reaproveitarão a mesma instância do controlador, ou seja, a instância do controlador será criada uma vez e permanecerá na memória, sendo reaproveitada por todas as requisições.

> **Nota**
> Desativar o reuso do controlador requer webman>=1.4.0. Isso significa que, antes da versão 1.4.0, todos os controladores eram reutilizados em todas as requisições e não podiam ser alterados.

> **Nota**
> Ao ativar o reuso do controlador, as requisições não devem alterar quaisquer propriedades do controlador, pois essas alterações afetarão requisições subsequentes, por exemplo

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
        // Este método irá reter o modelo após a primeira requisição update?id=1
        // Se a requisição delete?id=2 for efetuada em seguida, os dados do ID 1 serão excluídos
        if (!$this->model) {
            $this->model = Model::find($id);
        }
        return $this->model;
    }
}
```

> **Dica**
> Retornar dados em um construtor `__construct()` do controlador não terá efeito algum, por exemplo

```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    public function __construct()
    {
        // Retornar dados do construtor não terá efeito, o navegador não receberá esta resposta
        return response('hello'); 
    }
}
```

## Diferenças entre o reuso e não reuso do controlador
As diferenças são as seguintes

#### Não reuso do controlador
Cada requisição cria uma nova instância do controlador, que é liberada e alocada novamente após o término da requisição. O não reuso do controlador é semelhante aos frameworks tradicionais e está de acordo com a maioria das práticas de desenvolvimento. Como os controladores são criados e destruídos repetidamente, o desempenho será ligeiramente inferior em comparação com o reuso do controlador (o desempenho do teste de "helloworld" é cerca de 10% mais baixo, mas será praticamente insignificante com aplicações reais).

#### Reuso do controlador
Quando reutilizado, o controlador é instanciado apenas uma vez por processo, e não é liberado após o término da requisição, sendo reutilizado pelas requisições subsequentes no mesmo processo. O reuso do controlador tem melhor desempenho, mas não está de acordo com a maioria das práticas de desenvolvimento.

#### Casos em que não se deve usar o reuso do controlador

Quando a requisição altera as propriedades do controlador, o reuso do controlador não deve ser ativado, pois essas alterações nas propriedades afetarão requisições futuras.

Alguns desenvolvedores gostam de realizar algumas inicializações para cada requisição dentro do construtor `__construct()` do controlador, neste caso, o reuso do controlador não é ideal, pois o construtor do processo será chamado apenas uma vez e não a cada requisição.
