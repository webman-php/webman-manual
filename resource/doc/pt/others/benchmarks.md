# Teste de estresse

## Quais são os fatores que afetam os resultados do teste de estresse?
* A latência de rede do servidor para a máquina de teste (é recomendado realizar o teste na rede local ou na própria máquina)
* A largura de banda da máquina de teste para o servidor (é recomendado realizar o teste na rede local ou na própria máquina)
* Se a opção HTTP keep-alive está habilitada (recomenda-se habilitar)
* Se o número de conexões simultâneas é suficiente (para testes externos, recomenda-se aumentar o número de conexões simultâneas o máximo possível)
* Se o número de processos no servidor é adequado (para o serviço 'helloworld', recomenda-se que o número de processos seja igual ao número de CPUs; para serviços de banco de dados, recomenda-se que o número de processos seja pelo menos quatro vezes o número de CPUs)
* O desempenho do serviço em si (por exemplo, se está usando um banco de dados externo)

## O que é o HTTP keep-alive?
O mecanismo de HTTP Keep-Alive é uma técnica usada para enviar várias solicitações e respostas HTTP em uma única conexão TCP, o que tem um grande impacto nos resultados dos testes de desempenho. Desativar o keep-alive pode diminuir o QPS consideravelmente.
Atualmente, os navegadores têm o keep-alive ativado por padrão, o que significa que, após acessar um endereço http, o navegador mantém a conexão aberta temporariamente e a reutiliza na próxima solicitação, a fim de melhorar o desempenho.
É recomendado manter o keep-alive ativado durante os testes de estresse.

## Como habilitar o HTTP keep-alive durante o teste de estresse?
Se estiver utilizando o programa 'ab' para o teste de estresse, você precisa adicionar o parâmetro -k, por exemplo, `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Para o 'apipost', é necessário retornar o cabeçalho gzip para habilitar o keep-alive (devido a um bug do 'apipost'). 
Outros programas de teste de estresse geralmente têm o keep-alive habilitado por padrão.

## Por que o QPS é baixo ao realizar testes de estresse pela internet?
A alta latência da rede externa resulta em um baixo QPS, o que é um comportamento normal. Por exemplo, ao testar a página do Baidu, o QPS pode ser apenas algumas dezenas.
Recomenda-se realizar o teste na rede local ou na própria máquina para eliminar o impacto da latência da rede. Se for necessário realizar o teste pela internet, é possível aumentar o número de conexões simultâneas para aumentar o throughput (desde que haja largura de banda suficiente).

## Por que o desempenho diminui após a passagem pelo proxy do nginx?
A execução do nginx consome recursos do sistema. Além disso, a comunicação entre o nginx e o webman também consome recursos. 
No entanto, os recursos do sistema são limitados, e o webman não pode acessar todos os recursos do sistema, portanto, é normal que o desempenho do sistema como um todo possa diminuir.
Para minimizar o impacto no desempenho causado pelo proxy do nginx, pode-se considerar desativar o log do nginx (`access_log off;`), e habilitar o keep-alive entre o nginx e o webman, conforme descrito em [Nginx Proxy](nginx-proxy.md).
Além disso, o HTTPS consome mais recursos em comparação com o HTTP, porque o HTTPS requer um aperto de mão SSL/TLS, criptografia e descriptografia de dados, o tamanho dos pacotes aumenta ocupando mais largura de banda, o que pode resultar em uma redução de desempenho. Se estiver realizando testes de estresse com conexões de curta duração (sem a opção HTTP keep-alive), cada solicitação exigirá um aperto de mão SSL/TLS adicional, o que reduzirá consideravelmente o desempenho. Recomenda-se habilitar o HTTP keep-alive ao testar o HTTPS.

## Como saber se o sistema atingiu seu limite de desempenho?
Geralmente, quando a CPU atinge 100%, significa que o sistema atingiu seu limite de desempenho. Se a CPU ainda estiver ociosa, significa que o limite ainda não foi atingido. Nesse caso, aumentar o número de conexões simultâneas pode aumentar o QPS. Se aumentar as conexões simultâneas não melhorar o QPS, pode ser que o número de processos do webman não seja suficiente; nesse caso, é possível aumentar o número de processos do webman. Se ainda assim o desempenho não melhorar, verifique se a largura de banda é suficiente.

## Por que os resultados do teste de estresse mostram que o desempenho do webman é inferior ao do framework gin em Go?
Os testes de desempenho do [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) mostram que o webman supera o gin cerca de duas vezes em todos os indicadores, como texto puro, consulta a banco de dados, atualização de banco de dados, etc. Se os seus resultados forem diferentes, pode ser devido ao uso do ORM no webman, o que resulta em uma perda significativa de desempenho. Recomenda-se tentar comparar o webman com o SQL puro usando o PDO nativo e o gin com SQL puro.

## Quanto o uso do ORM afeta o desempenho no webman?
Aqui estão os dados de teste:

**Ambiente**
Servidor na nuvem com 4 núcleos e 4 GB de RAM, consultando um registro aleatório de 100.000 e retornando os dados em formato JSON.

**Quando usado o PDO nativo**
O QPS do webman é de 17.800.

**Quando usado o Db::table() do Laravel**
O QPS do webman cai para 9.400.

**Quando usado o Model do Laravel**
O QPS do webman cai para 7.200.

O resultado é semelhante para o thinkORM. 

> **Observação**
> Embora o uso do ORM possa causar uma queda no desempenho, para a maioria dos negócios, ele é suficiente. Devemos encontrar um equilíbrio entre eficiência no desenvolvimento, manutenção e desempenho, em vez de buscar apenas o desempenho.

## Por que o QPS é baixo ao realizar testes de estresse com o apipost?
O módulo de teste de estresse do apipost tem um bug, se o servidor não retornar o cabeçalho gzip, o keep-alive não será ativado, o que resultará em uma grande queda de desempenho. A solução é comprimir os dados ao retorná-los e adicionar o cabeçalho gzip, por exemplo:
```php
<?php
namespace app\controller;
class IndexController
{
    public function index()
    {
        return response(gzencode('hello webman'))->withHeader('Content-Encoding', 'gzip');
    }
}
```  
Além disso, em algumas situações, o apipost não consegue atingir uma pressão satisfatória, o que resulta em aproximadamente 50% a menos de QPS em comparação com o 'ab'.  É recomendado realizar testes de estresse com o 'ab', 'wrk' ou outros softwares de teste especializados em vez do apipost.

## Configurando o número adequado de processos
O webman abre por padrão um número de processos igual a quatro vezes o número de CPUs. Na verdade, para um serviço 'helloworld' sem IO de rede, o número ideal de processos para o teste de estresse é igual ao número de núcleos da CPU, a fim de reduzir a sobrecarga de troca de contexto.
Para serviços com bloqueio de IO, como acesso a banco de dados, o número de processos pode ser definido entre 3 e 8 vezes o número de CPUs, uma vez que mais processos são necessários para aumentar a concorrência, e a sobrecarga de troca de contexto em relação ao IO bloqueante pode ser ignorada.

## Algumas faixas de referência para testes de estresse

**Nuvem com servidor de 4 núcleos 4G, 16 processos, teste na máquina local ou na rede local**

| - | Com keep-alive ativado | Sem keep-alive ativado |
|--|-----|-----|
| hello world | 80.000-160.000 QPS | 10.000-30.000 QPS |
| Consulta única a banco de dados | 10.000-20.000 QPS | 10.000 QPS |

[**Dados de teste de estresse de terceiros da techempower**](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

## Exemplos de comandos de teste de estresse

**ab**
```plaintext
# 100.000 solicitações, 200 conexões simultâneas, com keep-alive habilitado
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100.000 solicitações, 200 conexões simultâneas, sem keep-alive habilitado
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```plaintext
# Teste de 10 segundos com 200 conexões simultâneas, com keep-alive habilitado (padrão)
wrk -c 200 -d 10s http://example.com
```
