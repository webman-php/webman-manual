# Ciclo de Vida

## Ciclo de Vida do Processo
- Cada processo tem um longo ciclo de vida
- Cada processo é executado independentemente e sem interferência mútua
- Durante o seu ciclo de vida, cada processo pode lidar com múltiplos pedidos
- Quando um processo recebe comandos `stop`, `reload` ou `restart`, ele encerra e termina o ciclo de vida atual

> **Dica**
> Cada processo é independente e sem interferência mútua, o que significa que cada processo mantém seus próprios recursos, variáveis e instâncias de classe, resultando em cada processo tendo sua própria conexão de banco de dados. Além disso, certos singletons são inicializados em cada processo, causando múltiplas inicializações em vários processos.

## Ciclo de Vida do Pedido
- Cada pedido gera um objeto `$request`
- O objeto `$request` é liberado após o processamento do pedido

## Ciclo de Vida do Controlador
- Cada controlador é instanciado apenas uma vez por processo, exceto quando o reuso do controlador é desativado (consulte [Ciclo de Vida do Controlador](https://www.workerman.net/doc/webman/controller.html#%E7%94%9F%E5%91%BD%E5%91%A8%E6%9C%9F))
- A instância do controlador é compartilhada entre vários pedidos dentro do mesmo processo, exceto quando o reuso do controlador é desativado
- O ciclo de vida do controlador termina quando o processo é encerrado, exceto quando o reuso do controlador é desativado

## Sobre o Ciclo de Vida da Variável
O webman é baseado em PHP, e segue completamente o mecanismo de liberação de variáveis do PHP. Variáveis temporárias criadas na lógica de negócios, incluindo instâncias de classes criadas com a palavra-chave `new`, são liberadas automaticamente após o término de uma função ou método, sem necessidade de liberar manualmente com `unset`. Em outras palavras, o desenvolvimento com webman é praticamente idêntico ao desenvolvimento com frameworks tradicionais. Por exemplo, no exemplo a seguir, a instância `$foo` será liberada automaticamente após a execução do método `index`:
```php
<?php

namespace app\controller;

use app\service\Foo;
use support\Request;

class IndexController
{
    public function index(Request $request)
    {
        $foo = new Foo(); // Supondo que haja uma classe Foo
        return response($foo->sayHello());
    }
}
```
Se desejar reutilizar a instância de uma classe específica, é possível armazenar a classe em uma propriedade estática da classe ou na propriedade de um objeto de longa duração, como um controlador. Também é possível usar o método `get` do contêiner para inicializar a instância da classe, como mostrado no exemplo a seguir:
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

O método `Container::get()` é usado para criar e armazenar a instância da classe, que será retornada ao chamar novamente com os mesmos parâmetros.

> **Observação**
> `Container::get()` só pode inicializar instâncias sem parâmetros de construtor. `Container::make()` pode criar instâncias com parâmetros de construtor, mas ao contrário de `Container::get()`, `Container::make()` não reutiliza a instância, ou seja, mesmo ao chamar com os mesmos parâmetros, `Container::make()` sempre retorna uma nova instância.

# Sobre Vazamento de Memória
Na grande maioria dos casos, nossos códigos de negócios não sofrem vazamento de memória (muito raramente há relatos de vazamento de memória). Basta ter um pouco de cuidado com os dados de longa duração do array. Veja o exemplo a seguir:
```php
<?php
namespace app\controller;

use support\Request;

class FooController
{
    // Propriedade de array
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
Por padrão, os controladores têm uma longa duração (exceto quando o reuso do controlador é desativado). Da mesma forma, a propriedade de array `$data` do controlador também tem uma longa duração. Com o aumento contínuo das requisições `foo/index`, os elementos do array `$data` aumentam indefinidamente, levando a vazamento de memória.

Para mais informações, consulte [Vazamento de Memória](./memory-leak.md)
