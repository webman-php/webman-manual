# Prestazioni di webman

### Flusso di gestione delle richieste dei framework tradizionali

1. Nginx/Apache riceve la richiesta
2. Nginx/Apache inoltra la richiesta a php-fpm
3. php-fpm inizializza l'ambiente, come la creazione di un elenco di variabili
4. php-fpm chiama l'RINIT delle varie estensioni/moduli
5. php-fpm legge il file PHP dal disco (usando opcache può essere evitato)
6. php-fpm analizza lessicalmente, sintatticamente e compila in opcode il file PHP (usando opcache può essere evitato)
7. php-fpm esegue l'opcode, inclusi 8,9,10,11
8. Il framework si inizializza, ad esempio istanziando varie classi come contenitori, controller, routing, middleware, ecc.
9. Il framework si connette al database e verifica i permessi, connessione a Redis
10. Il framework esegue la logica di business
11. Il framework chiude la connessione al database e a Redis
12. php-fpm rilascia le risorse, distrugge tutte le definizioni e le istanze di classe, distrugge le tabelle dei simboli, ecc.
13. php-fpm chiama in sequenza i metodi RSHUTDOWN delle varie estensioni/moduli
14. php-fpm inoltra il risultato a Nginx/Apache
15. Nginx/Apache restituisce il risultato al client

### Flusso di gestione delle richieste di webman

1. Il framework riceve la richiesta
2. Il framework esegue la logica di business
3. Il framework restituisce il risultato al client

Sì, senza un proxy inverso Nginx, il framework ha solo questi 3 passaggi. Si potrebbe dire che questo è il massimo di un framework PHP, rendendo le prestazioni di webman diverse volte superiori, anche decine di volte, rispetto ai framework tradizionali.

Per ulteriori informazioni, consulta [test di stress](benchmarks.md)
