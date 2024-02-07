# Cos'è webman

webman è un framework per servizi HTTP ad alte prestazioni basato su [workerman](https://www.workerman.net). webman è progettato per sostituire l'architettura tradizionale di php-fpm, fornendo servizi HTTP altamente performanti e scalabili. Con webman è possibile sviluppare siti web, sviluppare interfacce HTTP o servizi micro.

Inoltre, webman supporta anche i processi personalizzati che possono eseguire qualsiasi cosa che workerman può fare, come servizi websocket, Internet of Things, giochi, servizi TCP, servizi UDP, servizi Unix socket e altro ancora.

# Filosofia di webman
**Offrire la massima estensibilità e le migliori performance con il minimo nucleo.**

webman fornisce solo le funzionalità essenziali (routing, middleware, session, interfaccia dei processi personalizzati). Le altre funzionalità sono tutte riutilizzate nell'ecosistema di composer, il che significa che è possibile utilizzare i componenti più familiari in webman, ad esempio per lo sviluppo di database i developer possono scegliere di utilizzare `illuminate/database` di Laravel, oppure `ThinkORM` di ThinkPHP, o altri componenti come `Medoo`. Integrarli in webman è molto semplice.

# Caratteristiche di webman

1. Elevata stabilità. webman è sviluppato sulla base di workerman, che è sempre stato un framework socket estremamente stabile con pochissimi bug nel settore.

2. Prestazioni eccezionali. Le performance di webman sono 10-100 volte superiori rispetto ai framework tradizionali di php-fpm e circa il doppio rispetto a framework come gin di go e echo.

3. Elevato riutilizzo. Senza modifiche, è possibile riutilizzare la maggior parte dei componenti e delle librerie di composer.

4. Estensibilità elevata. Supporta i processi personalizzati che possono eseguire qualsiasi cosa che workerman può fare.

5. Estremamente semplice e intuitivo, con un basso costo di apprendimento, la scrittura del codice è simile a quella nei framework tradizionali.

6. Utilizza la licenza open source MIT, estremamente permissiva e amichevole.

# Indirizzo del progetto
GitHub: https://github.com/walkor/webman **Non essere avaro con le tue stelline**

Gitee: https://gitee.com/walkor/webman **Non essere avaro con le tue stelline**

# Dati di benchmark da fonti autorevoli

![](../assets/img/benchmark1.png)

Con attività di interrogazione del database, webman può gestire un throughput di 390.000 QPS su un singolo server, quasi 80 volte superiore al framework laravel dell'architettura tradizionale di php-fpm.

![](../assets/img/benchmarks-go.png)

Con attività di interrogazione del database, webman supera di circa il doppio le performance di framework di tipo simile in linguaggio go.

I dati provengono da [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf)
