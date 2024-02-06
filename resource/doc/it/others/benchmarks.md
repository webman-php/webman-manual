# Test di stress

### Quali sono i fattori che influenzano i risultati del test di stress?
* Ritardo di rete dal server alla macchina di stress (si consiglia test in rete locale o sulla stessa macchina)
* Larghezza di banda dalla macchina di stress al server (si consiglia test in rete locale o sulla stessa macchina)
* Attivazione della funzione HTTP keep-alive (si consiglia di attivarla)
* Numero di connessioni simultanee sufficienti (per test in rete esterna, si consiglia di aumentare il numero di connessioni)
* Numero di processi sul server appropriato (si suggerisce un numero di processi pari al numero di CPU per la gestione di base, ma per applicazioni con base di dati si consiglia di aumentare il numero di processi a almeno quattro volte il numero di CPU)
* Prestazione dell'applicazione stessa (ad esempio se si utilizza un database esterno)

### Che cos'è l'HTTP keep-alive?
Il meccanismo di Keep-Alive HTTP è una tecnologia utilizzata per inviare più richieste e risposte HTTP su una singola connessione TCP. Ha un impatto significativo sui risultati del test di prestazioni, poiché la disattivazione del keep-alive può comportare una significativa diminuzione delle richieste al secondo (QPS).
Attualmente, i browser di default hanno il keep-alive attivato, il che significa che quando un browser accede a un indirizzo HTTP, mantiene la connessione aperta temporaneamente e la riutilizza per la successiva richiesta, per aumentare le prestazioni.
Si consiglia di attivare il keep-alive durante i test di stress.

### Come attivare l'HTTP keep-alive durante il test di stress?
Se si utilizza il programma ab per il test di stress, è necessario aggiungere il parametro -k, ad esempio `ab -n100000 -c200 -k http://127.0.0.1:8787/`.
Per apipost, è necessario restituire l'intestazione gzip per abilitare il keep-alive (errore di apipost, vedere sotto).
Di solito, la maggior parte degli altri programmi di test di stress abilita il keep-alive per impostazione predefinita.

### Perché il QPS è così basso durante il test di stress in rete esterna?
Il ritardo di rete esterno causa un ridotto QPS, cosa normale. Ad esempio, il QPS per il test di stress di una pagina baidu potrebbe essere solo qualche decina.
Si consiglia di eseguire il test di stress in rete locale o sulla stessa macchina per escludere l'effetto del ritardo di rete.
Se è necessario eseguire il test di stress in rete esterna, è possibile aumentare il numero di connessioni simultanee per aumentare il throughput (assicurarsi di avere sufficiente larghezza di banda).

### Perché le prestazioni diminuiscono dopo essere passate attraverso un proxy di Nginx?
L'esecuzione di Nginx richiede risorse di sistema. Inoltre, la comunicazione tra Nginx e webman richiede anch'essa risorse.
Tuttavia, le risorse del sistema sono limitate e webman non può accedere a tutte le risorse di sistema, quindi è normale che le prestazioni dell'intero sistema possano diminuire.
Per ridurre al minimo l'impatto delle prestazioni causato dal proxy di Nginx, si può considerare di disattivare i registri di Nginx (`access_log off;`), abilitare il keep-alive tra Nginx e webman, fare riferimento a [proxy di Nginx](nginx-proxy.md).

Inoltre, l'HTTPS consuma più risorse rispetto all'HTTP, poiché richiede un handshake SSL/TLS, crittografia e decrittografia dei dati, aumentando le dimensioni dei pacchetti e utilizzando più larghezza di banda, il che può causare una diminuzione delle prestazioni.
Se si esegue il test di stress con connessioni brevi (senza attivare l'HTTP keep-alive), ogni richiesta richiederà un ulteriore scambio di handshake SSL/TLS, riducendo significativamente le prestazioni. Si consiglia di attivare l'HTTP keep-alive durante il test di stress di HTTPS.

### Come capire se il sistema ha raggiunto il limite di prestazioni?
In generale, quando la CPU raggiunge il 100% significa che il sistema ha raggiunto il suo limite di prestazioni. Se la CPU ha ancora risorse disponibili, significa che il limite non è stato raggiunto e si può aumentare il numero di connessioni simultanee per aumentare il QPS.
Se l'aumento delle connessioni simultanee non migliora il QPS, potrebbe essere che il numero di processi di webman non è sufficiente, quindi è necessario aumentare il numero di processi di webman. Se non si riesce ancora ad aumentare il QPS, si consiglia di verificare se la larghezza di banda è sufficiente.

### Perché i risultati del test di stress mostrano che le prestazioni di webman sono inferiori rispetto al framework gin di go?
Il test di stress di [techempower](https://www.techempower.com/benchmarks/#section=data-r21&hw=ph&test=db&l=zijnjz-6bj&a=2&f=1ekg-cbcw-2t4w-27wr68-pc0-iv9slc-0-1ekgw-39g-kxs00-o0zk-5jsetl-2x8doc-2) dimostra che webman ha valori di prestazioni significativamente superiori rispetto a gin, quasi il doppio, sia per il test su testo puro, che per le query al database e gli aggiornamenti del database.
Se i tuoi risultati sono diversi, potrebbe essere a causa dell'uso di ORM in webman, che comporta una significativa perdita di prestazioni. Si consiglia di provare webman con PDO nativo e confrontarlo con gin con SQL nativo.

### Quanto possono diminuire le prestazioni utilizzando un ORM in webman?
Ecco un set di dati di test di stress

**Ambiente**
Server Alibaba Cloud 4 core 4GB, restituzione di un record JSON casuale da 100.000 record.

**Utilizzando PDO nativo**
QPS di webman: 17.800

**Utilizzando il Db::table() di Laravel**
QPS di webman: 9.400

**Utilizzando il Model di Laravel**
QPS di webman: 7.200

I risultati sono simili per thinkORM, con poco margine di differenza.

> **Nota**
> Anche se l'uso di un ORM può comportare una diminuzione delle prestazioni, per la maggior parte delle applicazioni è ancora più che sufficiente. Dovremmo trovare un equilibrio tra l'efficienza dello sviluppo, la manutenibilità e le prestazioni, anziché cercare solo prestazioni.

### Perché il QPS è così basso durante il test di stress con apipost?
Il modulo di test di stress di apipost ha un bug, se il server non restituisce l'intestazione gzip, non riesce a mantenere il keep-alive, con conseguente notevole calo delle prestazioni.
Per risolvere questo problema, nella risposta, comprimere i dati e aggiungere l'intestazione gzip, ad esempio
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
Inoltre, in alcuni casi, apipost potrebbe non essere in grado di esercitare una pressione soddisfacente, mostrando un QPS inferiore del 50% rispetto a ab per lo stesso numero di connessioni. Si consiglia di eseguire il test di stress con ab, wrk o altri software di test di stress professionali, anziché apipost.

### Impostazione del numero di processi appropriato
webman di default avvia un numero di processi pari a cpu * 4. In realtà, per i test semplici senza I/O di rete, il numero di processi ottimale è uguale al numero di core della CPU, poiché può ridurre al minimo il costo di commutazione dei processi.
Se si tratta di applicazioni con I/O bloccante come database o Redis, il numero di processi può essere impostato tra 3 e 8 volte il numero di core della CPU, poiché sono necessari più processi per aumentare la capacità di connessione e il costo di commutazione dei processi è trascurabile rispetto all'I/O bloccante.

### Alcuni valori di riferimento per il test di stress

**Server cloud 4 core 4GB, 16 processi, test in locale/rete locale**

| - | Keep-alive attivo | Keep-alive non attivo |
|--|-----|-----|
| hello world | 80.000-160.000 QPS | 10.000-30.000 QPS |
| Query singola al database | 10.000 - 20.000 QPS | 10.000 QPS |

[Dati di test di terze parti di techempower](https://www.techempower.com/benchmarks/#section=data-r21&l=zik073-6bj&test=db)


### Esempi di comandi per il test di stress

**ab**
```
# 100.000 richieste, 200 connessioni simultanee, attivazione del keep-alive
ab -n100000 -c200 -k http://127.0.0.1:8787/

# 100.000 richieste, 200 connessioni simultanee, keep-alive non attivo
ab -n100000 -c200 http://127.0.0.1:8787/
```

**wrk**
```
# Test di stress con 200 connessioni simultanee per 10 secondi, keep-alive attivo (predefinito)
wrk -c 200 -d 10s http://example.com
```
