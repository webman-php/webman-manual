# O que é webman

Webman é um framework de serviço HTTP de alto desempenho baseado no [workerman](https://www.workerman.net). Ele é usado para substituir a arquitetura tradicional php-fpm, oferecendo serviços HTTP altamente escaláveis e de alto desempenho. Com o webman, você pode desenvolver sites, APIs ou microserviços.

Além disso, o webman suporta processos personalizados, podendo lidar com qualquer coisa que o workerman possa fazer, como serviços de websocket, IoT, jogos, serviços TCP, serviços UDP, serviços de soquete unix, entre outros.

# Filosofia do webman
**Oferecer máxima extensibilidade e desempenho com um núcleo mínimo.**

O webman fornece apenas as funcionalidades essenciais (roteamento, middleware, sessão, interface de processo personalizado). Todas as outras funcionalidades são reutilizadas do ecossistema do composer, o que significa que você pode usar os componentes de funcionalidades mais familiares no webman, como por exemplo, no desenvolvimento de banco de dados, você pode optar pelo `illuminate/database` do Laravel, pelo `ThinkORM` do ThinkPHP, ou por outros componentes como `Medoo`. Integrá-los no webman é muito fácil.

# Características do webman

1. Alta estabilidade. O webman é baseado no workerman, que sempre foi um framework de socket com pouquíssimos bugs e grande estabilidade na indústria.
2. Desempenho ultraalto. O desempenho do webman é de 10 a 100 vezes maior do que o framework tradicional php-fpm, e cerca de duas vezes maior do que frameworks como gin e echo em Go.
3. Alta reutilização. Não há necessidade de modificar, a maioria dos componentes e bibliotecas do composer podem ser reutilizados.
4. Alta extensibilidade. Suporta processos personalizados, podendo lidar com qualquer coisa que o workerman possa fazer.
5. Super fácil de usar, com baixo custo de aprendizado e sem diferença significativa no código em comparação com os frameworks tradicionais.
6. Usa uma das licenças de código aberto mais amigáveis e flexíveis, MIT.

# Endereço do projeto
GitHub: https://github.com/walkor/webman **Não hesite em dar sua estrela**

Gitee: https://gitee.com/walkor/webman **Não hesite em dar sua estrela**

# Dados de teste de terceiros

![](../assets/img/benchmark1.png)

Com operações de consulta ao banco de dados, o throughput único do webman atinge 390.000 QPS, cerca de 80 vezes maior que a estrutura tradicional php-fpm do framework Laravel.

![](../assets/img/benchmarks-go.png)

Com operações de consulta ao banco de dados, o webman tem um desempenho cerca de duas vezes maior que o framework web em Go. 

Estes dados são provenientes do [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
