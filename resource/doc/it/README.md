# Che cos'è webman

webman è un framework ad alte prestazioni per servizi HTTP, basato su [workerman](https://www.workerman.net). Viene utilizzato al posto dell'architettura tradizionale php-fpm, offrendo servizi HTTP altamente performanti e scalabili. Con webman è possibile sviluppare siti web, API HTTP e microservizi.

Inoltre, webman supporta processi personalizzati e può svolgere qualsiasi compito che workerman è in grado di gestire, come ad esempio servizi websocket, Internet of Things, giochi, servizi TCP, servizi UDP, servizi di socket Unix, e così via.

# Filosofia di webman
**Fornire massima estensibilità e prestazioni ottimali con un kernel minimo.**

webman fornisce solo le funzionalità essenziali (routing, middleware, session, interfaccia dei processi personalizzati). Tutte le altre funzionalità possono essere integrate nell'ecosistema di composer, il che significa che è possibile utilizzare i componenti più familiari in webman, ad esempio, per lo sviluppo del database è possibile scegliere tra `illuminate/database` di Laravel, `ThinkORM` di ThinkPHP, o altri componenti come `Medoo`.

# Caratteristiche di webman

1. Elevata stabilità: webman è basato su workerman, un framework socket noto per la sua estrema stabilità.
2. Prestazioni elevate: le prestazioni di webman superano di 10-100 volte i tradizionali framework php-fpm e sono circa il doppio di framework come gin e echo di go.
3. Massima riutilizzabilità: è possibile riutilizzare la maggior parte dei componenti e librerie di composer senza alcuna modifica.
4. Estensibilità elevata: supporta i processi personalizzati per svolgere qualsiasi compito gestibile da workerman.
5. Estremamente semplice e facile da usare, con un costo di apprendimento molto basso e una sintassi di codice simile ai framework tradizionali.
6. Rilasciato con la licenza MIT, che è estremamente aperta e amichevole.

# Indirizzo del progetto
GitHub: https://github.com/walkor/webman **Non abbiate paura di lasciare la vostra stellina!**

Gitee: https://gitee.com/walkor/webman **Non abbiate paura di lasciare la vostra stellina!**

# Dati di benchmark di terze parti autorevoli

![](../assets/img/benchmark1.png)

Con attività di interrogazione al database, webman raggiunge una throughput di 390.000 QPS su singola macchina, superando di quasi 80 volte il framework laravel dell'architettura tradizionale php-fpm.

![](../assets/img/benchmarks-go.png)

Con attività di interrogazione al database, webman ha prestazioni circa il doppio di un framework web simile in linguaggio go.

I dati sopra provengono da [techempower.com](https://www.techempower.com/benchmarks/#section=data-r20&hw=ph&test=db&l=zik073-sf).
