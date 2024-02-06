# Prestazioni di webman

### Flusso di gestione delle richieste dei framework tradizionali

1. Nginx/Apache riceve la richiesta
2. Nginx/Apache inoltra la richiesta a php-fpm
3. php-fpm inizializza l'ambiente, come la creazione di un elenco di variabili
4. php-fpm richiama RINIT delle varie estensioni/moduli
5. php-fpm legge il file PHP dal disco (utilizzando l'opcache per evitarlo)
6. php-fpm analizza lessicograficamente, sintatticamente e compila in opcode (utilizzando l'opcache per evitarlo)
7. php-fpm esegue l'opcode compreso 8.9.10.11
8. Il framework si inizializza, come istanziazione di varie classi, tra cui contenitori, controller, routing, middleware, ecc.
9. Il framework si connette al database e verifica l'autorizzazione, si connette a redis
10. Il framework esegue la logica di business
11. Il framework chiude la connessione al database e redis
12. php-fpm rilascia risorse, distrugge tutte le definizioni di classe, istanze, distrugge le tabelle dei simboli, ecc.
13. php-fpm chiama in sequenza il metodo RSHUTDOWN delle varie estensioni/moduli
14. php-fpm inoltra il risultato a Nginx/Apache
15. Nginx/Apache restituisce il risultato al client

### Flusso di gestione delle richieste di webman
1. Il framework riceve la richiesta
2. Il framework esegue la logica di business
3. Il framework restituisce il risultato al client

Esattamente, senza un proxy inverso Nginx, il framework ha solo questi 3 passaggi. Si potrebbe dire che questo è l'apice dei framework PHP, che rende le prestazioni di webman diverse volte superiori, anche decine di volte, rispetto ai framework tradizionali.

Per ulteriori informazioni, consulta [test di stress](benchmarks.md)
