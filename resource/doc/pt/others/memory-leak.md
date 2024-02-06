# Sobre Vazamentos de Memória

O webman é um framework de memória persistente, então precisamos prestar um pouco de atenção aos vazamentos de memória. No entanto, os desenvolvedores não precisam se preocupar muito, porque os vazamentos de memória ocorrem em condições extremas e são fáceis de evitar. Desenvolver com o webman é praticamente igual ao desenvolvimento com frameworks tradicionais, sem a necessidade de realizar operações extras para gerenciar a memória.

> **Dica**
> O processo de monitoramento embutido do webman irá monitorar o uso de memória de todos os processos. Se um processo estiver prestes a atingir o valor definido em `memory_limit` no arquivo php.ini, ele será reiniciado automaticamente de forma segura, liberando memória. Isso não afeta a aplicação durante esse período.

## Definição de Vazamento de Memória
Com o aumento contínuo das solicitações, a memória utilizada pelo webman também **aumenta infinitamente** (observe que é um aumento **infinito**), alcançando várias centenas de megabytes ou até mais, isso é considerado um vazamento de memória. Se a memória aumenta e depois para de aumentar, não é considerado um vazamento de memória.

Geralmente, um processo que utiliza algumas dezenas de megabytes de memória é considerado uma situação normal. Quando um processo lida com uma solicitação muito grande ou mantém um grande número de conexões, é comum que o uso de memória de um único processo possa atingir várias centenas de megabytes. Depois de utilizar essa parte da memória, o PHP pode não retorná-la completamente ao sistema operacional, deixando-a reservada para reutilização. Sendo assim, pode acontecer de haver um aumento no uso de memória após processar uma grande solicitação e não liberar a memória, o que é considerado um comportamento normal. (Chamar o método gc_mem_caches() pode liberar parte da memória ociosa)

## Como os Vazamentos de Memória Ocorrem
**Os vazamentos de memória ocorrem apenas quando ambos os seguintes critérios são atendidos:**
1. Existe um array de **longa duração** (observe que é um array de **longa duração**, arrays comuns não são um problema)
2. E esse array de **longa duração** é expandindo indefinidamente (ou seja, o negócio insere dados ilimitadamente e nunca limpa os dados)

Se ambos os critérios 1 e 2 forem **satisfeitos simultaneamente** (observe que é simultaneamente), um vazamento de memória ocorrerá. Caso contrário, não será considerado um vazamento de memória se não atender a ambos os critérios ou atender apenas a um deles.

## Arrays de Longa Duração
Os arrays de longa duração no webman incluem:
1. Arrays com a palavra-chave `static`
2. Propriedades de arrays singleton
3. Arrays com a palavra-chave `global`

> **Observação:**
> O webman permite o uso de dados de longa duração, desde que limite os elementos do array para que não se expandam indefinidamente.

A seguir, serão fornecidos exemplos explicativos.

#### Array estático que se expande indefinidamente
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

O array `$data` definido com a palavra-chave `static` é um array de longa duração. No exemplo, o array `$data` aumenta indefinidamente com as solicitações, resultando em um vazamento de memória.

#### Propriedade de array singleton que se expande indefinidamente
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

Código de chamada:
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

`Cache::instance()` retorna um singleton de Cache, que é uma instância de longa duração. Embora a propriedade `$data` não tenha a palavra-chave `static`, devido à própria classe ser de longa duração, `$data` também se torna um array de longa duração. Com o aumento contínuo de diferentes chaves no array `$data`, a aplicação utiliza cada vez mais memória, resultando em um vazamento de memória.

> **Observação:**
> Se as chaves adicionadas por `Cache::instance()->set(key, value)` forem de quantidade finita, não ocorrerá vazamento de memória, pois o array `$data` não aumentará indefinidamente.

#### Array global que se expande indefinidamente
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
O array definido com a palavra-chave global não é liberado após a execução de uma função ou método, tornando-o um array de longa duração. O código acima resultará em um vazamento de memória conforme as solicitações aumentam. Da mesma forma, um array definido com a palavra-chave static dentro de uma função ou método também é um array de longa duração. Se o array se expandir indefinidamente, ocorrerá um vazamento de memória, por exemplo:
```php
class Index
{
    public function index(Request $request)
    {
        static $data = [];
        $data[] = time();
        return response($foo->sayHello());
    }
}
```

## Sugestões
Recomenda-se que os desenvolvedores não se concentrem muito nos vazamentos de memória, pois eles raramente ocorrem. Se ocorrerem, é possível identificar a origem por meio de testes de carga e corrigi-la. Mesmo que os desenvolvedores não consigam encontrar o ponto de vazamento, o serviço de monitoramento embutido no webman reiniciará o processo com vazamento de memória apropriadamente, liberando a memória.

No entanto, se desejar evitar ao máximo os vazamentos de memória, siga as sugestões a seguir.
1. Evite usar arrays com as palavras-chave `global` e `static`, e se precisar usar, garanta que não expandam indefinidamente.
2. Evite usar singletons para classes não familiares, e caso precise, verifique se elas possuem propriedades de arrays que expandem indefinidamente.


