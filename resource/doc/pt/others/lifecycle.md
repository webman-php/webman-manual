# Ciclo de Vida

## Ciclo de Vida do Processo
- Cada processo tem um ciclo de vida muito longo
- Cada processo é executado independentemente e não interfere nos outros
- Cada processo pode lidar com vários pedidos durante o seu ciclo de vida
- Quando um processo recebe os comandos `stop`, `reload` ou `restart`, ele encerrará e terminará o ciclo de vida atual

> **Dica**
> Cada processo é independente e não interfere uns com os outros, o que significa que cada processo mantém seus próprios recursos, variáveis e instâncias de classe, resultando em cada processo tendo sua própria conexão de banco de dados. Alguns singletons são inicializados em cada processo, o que significa que eles serão inicializados várias vezes quando houver vários processos.

## Ciclo de Vida do Pedido
- Cada pedido gera um objeto `$request`
- O objeto `$request` é liberado após o processamento do pedido

## Ciclo de Vida do Controlador
- Cada controlador é instanciado apenas uma vez por processo, mas é instanciado várias vezes em vários processos (exceto quando o reuso de controlador é desativado, consulte [Ciclo de Vida do Controlador](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- A instância do controlador é compartilhada entre vários pedidos dentro do processo (exceto quando o reuso de controlador é desativado)
- O ciclo de vida do controlador termina quando o processo é encerrado (exceto quando o reuso de controlador é desativado)

## Sobre o Ciclo de Vida das Variáveis
webman é desenvolvido em PHP, portanto, segue completamente o mecanismo de liberação de variáveis do PHP. Variáveis temporárias geradas na lógica de negócios, incluindo instâncias de classes criadas com a palavra-chave `new`, são liberadas automaticamente após o término de uma função ou método, sem a necessidade de liberá-las manualmente com `unset`. Isso significa que o desenvolvimento com webman oferece uma experiência semelhante ao desenvolvimento em estruturas tradicionais. Por exemplo, a instância `$foo` no exemplo a seguir será liberada automaticamente após a execução do método `index`:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Supondo que haja uma classe Foo aqui
        return response($foo->sayHello());
    }
}
```
Se desejar reutilizar uma instância de uma classe, é possível armazenar a classe em uma propriedade estática da classe ou em uma propriedade de um objeto de longa duração, como o controlador. Além disso, pode-se usar o método `get` do Container para inicializar a instância da classe, como mostrado no exemplo a seguir:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Container;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = Container::get(Foo::class);
        return response($foo->sayHello());
    }
}
```
O método `Container::get()` é usado para criar e armazenar a instância da classe, de modo que, quando chamado novamente com os mesmos parâmetros, retornará a instância anterior da classe.

> **Nota**
> `Container::get()` só pode instanciar classes sem parâmetros de construtor. `Container::make()` pode criar instâncias com parâmetros de construtor, mas, ao contrário de `Container::get()`, não reutiliza instâncias; ou seja, mesmo que chamado com os mesmos parâmetros, `Container::make()` sempre retornará uma nova instância.

## Sobre Vazamentos de Memória
Na maioria dos casos, nosso código de negócios não apresenta vazamentos de memória (muito poucos usuários relataram vazamentos de memória). Basta ter um pouco de atenção para evitar que os dados de matrizes de longa duração se expandam indefinidamente. Considere o código a seguir como exemplo:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Propriedade da matriz
    public $data = [];
    
    public function index(Request $request)
    {
        $this->data[] = time();
        return response('hello index');
    }

    public function hello(Request $request)
    {
        return response('hello webman');
    }
}
```
Por padrão, os controladores têm uma longa duração (exceto quando o reuso de controlador é desativado). Da mesma forma, a propriedade da matriz `$data` do controlador possui uma longa duração. Com o pedido `foo/index` sendo continuamente chamado, os elementos do arrays de `$data` aumentarão constantemente, resultando em um vazamento de memória.

Para obter mais informações, consulte [Vazamentos de Memória](./memory-leak.md).
