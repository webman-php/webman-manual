# Sobre Vazamento de Memória
webman é um framework de memória residente, por isso precisamos prestar um pouco de atenção aos vazamentos de memória. No entanto, os desenvolvedores não precisam se preocupar muito, pois os vazamentos de memória ocorrem em condições extremas e são facilmente evitáveis. O processo de desenvolvimento com webman é basicamente o mesmo que o desenvolvimento com frameworks tradicionais, não sendo necessário realizar operações extras de gerenciamento de memória.

> **Dica**
> O processo monitor embutido do webman irá monitorar o uso de memória de todos os processos. Se um processo estiver prestes a atingir o valor definido em `memory_limit` no arquivo php.ini, ele será reiniciado com segurança para liberar memória. Isso não afetará os negócios durante o processo.

## Definição de Vazamento de Memória
À medida que o número de solicitações aumenta, a quantidade de memória usada pelo webman também aumenta indefinidamente (observe que isso significa **aumento indefinido**), atingindo centenas de MB ou até mais, caracterizando um vazamento de memória. Se a memória aumentar e parar de aumentar depois disso, isso não é considerado um vazamento de memória.

É bastante normal para um processo usar algumas dezenas de MB de memória. No entanto, ao lidar com solicitações muito grandes ou manter grandes quantidades de conexões, é comum que o uso de memória por processo atinja centenas de MB. Após o uso desse tipo de memória, o PHP pode não devolvê-la completamente ao sistema operacional. Em vez disso, ele a mantém para reutilização, o que pode resultar em um aumento do uso de memória após o processamento de uma solicitação grande e não a liberação da memória. Isso é considerado um comportamento normal. (Chamar o método gc_mem_caches() pode liberar parte da memória ociosa)


## Como ocorre o Vazamento de Memória
**Um vazamento de memória ocorre quando as seguintes duas condições são atendidas:**
1. Existe um array de **longa vida útil** (observe que é um array de **longa vida útil**, e não um array comum)
2. E esse array de **longa vida útil** se expande indefinidamente (o negócio continua inserindo dados nele sem limpá-los)

Se 1 e 2 forem **atendidos simultaneamente** (observe que é simultaneamente), ocorrerá um vazamento de memória. Caso contrário, se uma das condições não for atendida ou se apenas uma for atendida, não será considerado um vazamento de memória.


## Arrays de Longa Vida Útil
Os arrays de longa vida útil no webman incluem:
1. Arrays com a palavra-chave static
2. Propriedades de arrays singleton
3. Arrays com a palavra-chave global

> **Observação**
> O webman permite o uso de dados de longa vida útil, mas é necessário garantir que os dados dentro deles sejam finitos e que o número de elementos não se expanda indefinidamente.


A seguir estão exemplos ilustrativos

#### Array static expandindo indefinidamente
```php
class Foo
{
    public static $data = [];
    public function index(Request $request)
    {
        self::$data[] = time();
        return response('hello');
    }
}
```

O array `$data` definido com a palavra-chave `static` é um array de longa vida útil. No exemplo acima, o array `$data` se expande indefinidamente à medida que as solicitações continuam, levando a um vazamento de memória.

#### Propriedade de Array Singleton expandindo indefinidamente
```php
class Cache
{
    protected static $instance;
    public $data = [];
    
    public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }
    
    public function set($key, $value)
    {
        $this->data[$key] = $value;
    }
}
```

Código de chamada
```php
class Foo
{
    public function index(Request $request)
    {
        Cache::instance()->set(time(), time());
        return response('hello');
    }
}
```

`Cache::instance()` retorna um singleton Cache, que é uma instância de longa vida útil. Embora sua propriedade `$data` não tenha a palavra-chave `static`, ela é considerada de longa vida útil devido à própria classe ser de longa vida útil. À medida que dados com chaves diferentes são continuamente adicionados ao `$data`, a memória usada pelo programa aumenta, resultando em um vazamento de memória.

> **Observação**
> Se as chaves adicionadas através de Cache::instance()->set(chave, valor) forem de um número finito, então não ocorrerá um vazamento de memória, pois o array `$data` não se expandirá indefinidamente.


#### Array global expandindo indefinidamente
```php
class Index
{
    public function index(Request $request)
    {
        global $data;
        $data[] = time();
        return response($foo->sayHello());
    }
}
```
O array definido com a palavra-chave global não será liberado após a conclusão da função ou do método da classe, tornando-o um array de longa vida útil. O código acima, à medida que as solicitações continuamente aumentam, resultará em um vazamento de memória. Da mesma forma, um array definido com a palavra-chave static dentro de uma função ou método também é um array de longa vida útil e, se esse array se expandir indefinidamente, também resultará em um vazamento de memória, como por exemplo:
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
```

## Recomendações
Recomenda-se que os desenvolvedores não se preocupem excessivamente com vazamentos de memória, pois eles raramente acontecem. Caso ocorram, podemos encontrar o trecho de código que está causando o vazamento por meio de testes de estresse, a fim de localizar o problema. Mesmo que os desenvolvedores não identifiquem o ponto de vazamento, o serviço de monitoramento embutido no webman fará com que os processos com vazamento de memória sejam reiniciados de forma segura e oportuna para liberar memória.

Se você desejar evitar vazamentos de memória o máximo possível, pode seguir as seguintes recomendações:
1. Evite usar arrays com as palavras-chave `global` e `static`, mas se usar, certifique-se de que eles não se expandirão indefinidamente.
2. Evite utilizar singletons em classes que não sejam familiares, e opte por inicializá-las com a palavra-chave `new`. Se um singleton for necessário, verifique se ele possui propriedades de array que não aumentarão indefinidamente.
