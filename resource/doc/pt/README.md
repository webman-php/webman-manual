# O que é o webman

O webman é um framework de serviço HTTP de alta performance baseado em [workerman](https://www.workerman.net). Ele é usado para substituir a arquitetura tradicional php-fpm e fornece um serviço HTTP altamente escalável e de alto desempenho. Com o webman, é possível desenvolver sites, APIs HTTP e microserviços.

Além disso, o webman também suporta processos personalizados, que podem fazer qualquer coisa que o workerman possa fazer, como serviços de websocket, Internet das Coisas, jogos, serviços TCP, serviços UDP, serviços de soquete unix, entre outros.

# Filosofia do webman
**Oferecer a maior extensibilidade e o melhor desempenho com o menor núcleo.**

O webman fornece apenas as funcionalidades essenciais (roteamento, middleware, sessão, interface de processo personalizado). As demais funcionalidades são todas reutilizadas a partir do ecossistema do composer, o que significa que é possível usar os componentes mais familiares no webman, como o `illuminate/database` do Laravel para desenvolvimento de banco de dados, o `ThinkORM` do ThinkPHP, ou outros componentes como o `Medoo`. Integrá-los ao webman é muito simples.

# Características do webman

1. Alta estabilidade. O webman é desenvolvido com base no workerman, que sempre foi um framework de soquetes com pouquíssimos bugs na indústria.

2. Desempenho ultrarrápido. O desempenho do webman é de 10 a 100 vezes superior aos frameworks tradicionais php-fpm e cerca de duas vezes mais alto do que o go com o gin e echo.

3. Alta reutilização. A maior parte dos componentes e bibliotecas do composer pode ser reutilizada sem a necessidade de modificação.

4. Alta extensibilidade. Suporta processos personalizados que podem fazer qualquer coisa que o workerman seja capaz de fazer.

5. Extremamente fácil de usar, com um custo de aprendizado muito baixo e uma escrita de código semelhante aos frameworks tradicionais.

6. Usa a licença de código aberto MIT, que é extremamente permissiva e amigável.

# Endereço do Projeto
GitHub: https://github.com/walkor/webman **Não se esqueça de dar sua estrelinha**

Gitee: https://gitee.com/walkor/webman **Não se esqueça de dar sua estrelinha**

# Dados de Testes de Terceiros

![](../assets/img/benchmark1.png)

Com consultas a banco de dados, o webman atingiu uma taxa de transferência de 390.000 QPS em um único servidor, o que é cerca de 80 vezes maior do que o framework Laravel com a arquitetura tradicional php-fpm.

![](../assets/img/benchmarks-go.png)

Com consultas a banco de dados, o webman tem um desempenho cerca de duas vezes superior a um framework web semelhante feito em Go.

Estes dados são provenientes de [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
