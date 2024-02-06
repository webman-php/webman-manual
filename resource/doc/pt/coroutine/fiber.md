# Coroutines

> **Coroutines Requirements**
> PHP>=8.1workerman>=5.0 webman-framework>=1.5 revolt/event-loop>1.0.0
> Upgrade webman command `composer require workerman/webman-framework ^1.5.0`
> Upgrade workerman command `composer require workerman/workerman ^5.0.0`
> Fiber coroutine requires installation of `composer require revolt/event-loop ^1.0.0`

# Exemplo
### Resposta atrasada

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        // Dorme por 1.5 segundos
        Timer::sleep(1.5);
        return $request->getRemoteIp();
    }
}
```
`Timer::sleep()` é semelhante à função `sleep()` nativa do PHP, a diferença é que `Timer::sleep()` não bloqueará o processo

### Fazendo Requisição HTTP

> **Nota**
> Requer a instalação de `composer require workerman/http-client ^2.0.0`

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Http\Client;

class TestController
{
    public function index(Request $request)
    {
        static $client;
        $client = $client ?: new Client();
        $response = $client->get('http://example.com'); // Solicitação assíncrona utilizando um método síncrono
        return $response->getBody()->getContents();
    }
}
```
Da mesma forma, a solicitação `$client->get('http://example.com')` é não bloqueante, o que pode ser usado para fazer solicitações HTTP não bloqueantes no webman, melhorando o desempenho do aplicativo.

Para mais informações, consulte [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html)

### Adicionando a Classe support\Context

A classe `support\Context` é usada para armazenar os dados do contexto da solicitação. Quando a solicitação é concluída, os dados do contexto correspondentes serão excluídos automaticamente. Ou seja, a vida útil dos dados do contexto segue a vida útil da solicitação. O `support\Context` é compatível com o ambiente de corrotina Fiber, Swoole e Swow.

### Corrotinas Swoole
Após a instalação da extensão Swoole (requer Swoole>=5.0), é possível habilitar as corrotinas Swoole configurando o arquivo config/server.php
```php
'event_loop' => \Workerman\Events\Swoole::class,
```

Para mais informações, consulte [workerman eventos](https://www.workerman.net/doc/workerman/appendices/event.html)

### Poluição de Variáveis Globais

Ambiente de corrotina proíbe o armazenamento de informações de estado **relacionadas à solicitação** em variáveis globais ou estáticas, pois isso pode levar à poluição de variáveis globais, como por exemplo

```php
<?php

namespace app\controller;

use support\Request;
use Workerman\Timer;

class TestController
{
    protected static $name = '';

    public function index(Request $request)
    {
        static::$name = $request->get('name');
        Timer::sleep(5);
        return static::$name;
    }
}
```

Configurando o número de processos como 1, ao enviar duas requisições consecutivas  
http://127.0.0.1:8787/test?name=lilei  
http://127.0.0.1:8787/test?name=hanmeimei  
Esperamos que os resultados retorne `lilei` e `hanmeimei` respectivamente, mas na verdade ambos retornam `hanmeimei`.
Isso ocorre porque a segunda requisição sobrescreve a variável estática `$name`, e quando a primeira requisição termina o sleep e retorna, a variável estática `$name` já se tornou `hanmeimei`.

**O método correto é usar o contexto para armazenar os dados do estado da solicitação**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        Context::set('name', $request->get('name'));
        Timer::sleep(5);
        return Context::get('name');
    }
}
```

**Variáveis locais não causam poluição de dados**
```php
<?php

namespace app\controller;

use support\Request;
use support\Context;
use Workerman\Timer;

class TestController
{
    public function index(Request $request)
    {
        $name = $request->get('name');
        Timer::sleep(5);
        return $name;
    }
}
```
Porque `$name` é uma variável local, as corrotinas não podem acessar as variáveis locais umas das outras, então as variáveis locais são seguras para corrotinas.

# Sobre Corrotinas
Corrotinas não são uma solução definitiva, sua introdução implica em tomar cuidado com a poluição de variáveis globais/estáticas e a necessidade de definir o contexto. Além disso, depurar bugs em um ambiente de corrotinas é mais complexo do que a programação bloqueante.

A programação bloqueante do webman já é suficientemente rápida. De acordo com os dados de benchmarks de três rodadas nos últimos três anos em [techempower.com](https://www.techempower.com/benchmarks/#section=data-r21&l=zijnjz-6bj&test=db&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-4fu13d-2x8do8-2), a programação bloqueante do webman com operações de banco de dados possui um desempenho quase duas vezes maior que os frameworks web em Go como gin e echo, e supera em quase 40 vezes o desempenho do framework tradicional Laravel.
![](../../assets/img/benchemarks-go-sw.png?)

Quando o banco de dados, o redis e outros estão em uma rede local, a programação bloqueante com vários processos pode muitas vezes apresentar um desempenho melhor que as corrotinas. Isso ocorre porque, quando o banco de dados, o redis e outros são rápidos o suficiente, o custo de criação, programação e eliminação de corrotinas pode ser maior do que o custo de troca de processos, de modo que a introdução de corrotinas pode não melhorar significativamente o desempenho.

# Quando Utilizar Corrotinas
Quando houver acesso lento no negócio, por exemplo, ao acessar uma API de terceiros, é possível usar [workerman/http-client](https://www.workerman.net/doc/workerman/components/workerman-http-client.html) para fazer chamadas HTTP assíncronas através de corrotinas, melhorando a capacidade de concorrência do aplicativo.
