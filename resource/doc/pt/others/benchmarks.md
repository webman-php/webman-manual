# Teste de estresse

### Quais fatores afetam os resultados do teste de estresse?
* Latência de rede do cliente para o servidor (recomenda-se teste de estresse na rede local ou na máquina local)
* Largura de banda do cliente para o servidor (recomenda-se teste de estresse na rede local ou na máquina local)
* Se o HTTP keep-alive está ativado (recomenda-se ativá-lo)
* Se o número de conexões simultâneas é suficiente (para teste de estresse na internet, é recomendável aumentar o número de conexões simultâneas)
* Se o número de processos do servidor está adequado (para um serviço simples, o número de processos recomendado é o mesmo que o número de núcleos da CPU. Para um serviço de banco de dados, o número de processos recomendado é quatro vezes a quantidade de núcleos da CPU ou mais)
* Desempenho do próprio serviço (por exemplo, se está utilizando um banco de dados na internet)

### O que é o HTTP keep-alive?
O mecanismo de HTTP Keep-Alive é uma técnica usada para enviar múltiplas solicitações e respostas HTTP em uma única conexão TCP, o que tem um grande impacto nos resultados dos testes de desempenho. A desativação do keep-alive pode resultar em uma queda significativa na QPS.
Atualmente, os navegadores estão configurados para manter o keep-alive ativado por padrão, o que significa que, após acessar um endereço HTTP, o navegador irá manter a conexão aberta e reutilizá-la na próxima solicitação, visando melhorar o desempenho.
Recomenda-se manter o keep-alive ativado durante o teste de estresse.

### Como ativar o HTTP keep-alive durante o teste de estresse?
Se estiver utilizando o programa ab para o teste de estresse, é necessário adicionar o parâmetro -k, por exemplo, `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
O apipost precisa retornar o cabeçalho gzip para ativar o keep-alive (um problema conhecido no apipost, consulte abaixo).
Geralmente, outros programas de teste de estresse já têm o keep-alive ativado por padrão.

### Por que a QPS é tão baixa ao realizar o teste de estresse na internet?
A alta latência da internet resulta em uma baixa QPS, o que é um fenômeno comum. Por exemplo, o teste de estresse na página do Baidu pode resultar em apenas algumas dezenas de QPS.
Recomenda-se realizar o teste de estresse na rede local ou na máquina local, a fim de eliminar a influência da latência de rede.
Se for absolutamente necessário realizar o teste na internet, é possível aumentar o número de conexões simultâneas para aumentar a taxa de transferência (desde que haja largura de banda suficiente).

### Por que o desempenho diminui após passar pelo proxy nginx?
A execução do nginx consome recursos do sistema. Além disso, a comunicação entre o nginx e o webman também consome recursos.
No entanto, os recursos do sistema são limitados e o webman não pode acessar todos eles, portanto, é normal que haja uma queda no desempenho do sistema como um todo.
Para minimizar o impacto do desempenho causado pelo proxy nginx, é possível considerar desativar o log do nginx (`access_log off;`), ativar o keep-alive entre o nginx e o webman, consulte [proxy nginx](nginx-proxy.md).
Além disso, o HTTPS consome mais recursos do que o HTTP, pois requer um handshake SSL/TLS, criptografia e descriptografia de dados, e aumenta o tamanho dos pacotes, consumindo mais largura de banda, o que pode resultar em uma queda no desempenho.
Ao realizar o teste de estresse com HTTPS e manter conexões curtas (sem manter o HTTP keep-alive), cada solicitação exigirá um handshake adicional de SSL/TLS, o que levará a uma significativa queda no desempenho. Recomenda-se manter o HTTP keep-alive ativado ao testar com HTTPS.

### Como saber se o sistema atingiu o limite de desempenho?
Em geral, quando a CPU atinge 100%, significa que o sistema atingiu o limite de desempenho. Se a CPU ainda tiver recursos disponíveis, significa que o limite ainda não foi atingido, e neste caso, aumentar o número de conexões simultâneas pode resultar em um aumento na QPS.
Se aumentar as conexões simultâneas não aumentar a QPS, pode ser devido ao número insuficiente de processos do webman, sendo recomendado aumentar a quantidade de processos do webman. Se ainda assim não aumentar, é necessário verificar se a largura de banda é suficiente.

### Por que os resultados do teste de estresse mostram que o desempenho do webman é inferior ao do framework go/gin?
Os testes de [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) demonstram que o webman supera o gin em quase o dobro em todos os aspectos, incluindo texto simples, consulta e atualização de banco de dados.
Se os resultados obtidos forem diferentes, pode ser devido ao uso de ORM no webman, o que resulta em uma perda significativa de desempenho. Nesse caso, é recomendável comparar o desempenho entre webman + PDO nativo e gin + SQL nativo.

### Quanto o uso de ORM no webman impacta o desempenho?
Aqui estão os resultados de um conjunto de testes de estresse.

**Ambiente**
Servidor na nuvem com 4 núcleos e 4 GB de RAM, retornando um registro aleatório de 10.000 registros em formato JSON.

**Se estiver usando PDO nativo**
QPS do webman é de 17.800.

**Se estiver usando Db::table() do Laravel**
O QPS do webman cai para 9.400.

**Se estiver usando Model do Laravel**
O QPS do webman cai para 7.200.

O thinkORM apresenta resultados semelhantes, com pouca diferença.

> **Dica**
> Embora o uso de ORM possa resultar em uma pequena redução de desempenho, para a maioria dos casos de uso, já é um desempenho suficiente. Devemos buscar um equilíbrio entre eficiência no desenvolvimento, manutenibilidade e desempenho, em vez de apenas buscar o desempenho.

### Por que o QPS é baixo ao utilizar o apipost para o teste de estresse?
O módulo de teste de estresse do apipost tem um bug. Se o servidor não retornar o cabeçalho gzip, não será possível manter o keep-alive, resultando em uma queda significativa de desempenho.
A solução é comprimir os dados antes de retorná-los e adicionar o cabeçalho gzip, por exemplo:
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
Além disso, em algumas situações, o apipost pode não ser capaz de gerar uma pressão satisfatória, resultando em cerca de 50% menos QPS em comparação ao ab, com a mesma quantidade de conexões simultâneas. Recomenda-se utilizar o ab, wrk ou outras ferramentas de teste de estresse profissionais em vez do apipost.

### Definindo a quantidade de processos adequada
O webman abre por padrão 4 vezes a quantidade de processos equivalente ao número de núcleos CPU. Na prática, para serviços sem IO de rede, o número ideal de processos para o teste de estresse de um serviço simples (como saudação) deve ser igual ao número de núcleos da CPU, pois isso reduzirá os custos de troca de processo.
Para serviços com IO bloqueante como banco de dados e Redis, o número de processos pode ser configurado para 3-8 vezes o número de núcleos da CPU, já que mais processos são necessários para aumentar a concorrência, e os custos de troca de processo em comparação com o IO bloqueante podem ser ignorados praticamente.

### Algumas faixas de referência para o teste de estresse

**Servidor na nuvem, 4 núcleos, 4GB, 16 processos, teste de estresse local/na rede local**

| - | Keep-alive ativado | Keep-alive desativado |
|--|-----|-----|
| Olá, mundo | 80.000-160.000 QPS | 10.000-30.000 QPS |
| Consulta única de banco de dados | 10.000-20.000 QPS | 10.000 QPS |

[Dados de testes de estresse de terceiros techempower](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)

### Exemplos de comandos de testes de estresse

**ab**
```
# 100.000 solicitações, 200 conexões simultâneas, com keep-alive ativado
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100.000 solicitações, 200 conexões simultâneas, com keep-alive desativado
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# Teste de estresse com 200 conexões simultâneas por 10 segundos, mantendo o keep-alive ativado (padrão)
wrk -c 200 -d 10s http://example.com
```
