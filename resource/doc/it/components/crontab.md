# Componente di programmazione delle attività cron

## workerman/crontab

### Descrizione

`workerman/crontab` è simile a crontab di Linux, con la differenza che `workerman/crontab` supporta la pianificazione fino al secondo.

Spiegazione del tempo:

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ giorno della settimana (0 - 6) (Domenica=0)
|   |   |   |   +------ mese (1 - 12)
|   |   |   +-------- giorno del mese (1 - 31)
|   |   +---------- ora (0 - 23)
|   +------------ minuto (0 - 59)
+-------------- secondo (0-59)[Opzionale, se manca 0, il minimo granularità di tempo è il minuto]
```

### Indirizzo del progetto

https://github.com/walkor/crontab

### Installazione

```php
composer require workerman/crontab
```

### Utilizzo

**Passaggio 1: Creare il file del processo `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Eseguire ogni secondo
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Eseguire ogni 5 secondi
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Eseguire ogni minuto
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Eseguire ogni 5 minuti
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Eseguire al primo secondo di ogni minuto
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Eseguire alle 7:50 di ogni giorno, nota che qui l'indicatore dei secondi è omesso
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Passaggio 2: Configurare il file del processo per avviarsi con webman**

Apri il file di configurazione `config/process.php` e aggiungi la seguente configurazione

```php
return [
    ....altre configurazioni, omesse per brevità....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Passaggio 3: Riavviare webman**

> Nota: le attività pianificate non verranno eseguite immediatamente, inizieranno a essere eseguite solo nel minuto successivo.

### Nota
crontab non è asincrono, ad esempio se un processo di attività imposta due timer A e B, entrambi eseguono un'attività ogni secondo, ma se l'attività A richiede 10 secondi, allora B dovrà aspettare che A finisca prima di poter essere eseguita, causando un ritardo nell'esecuzione di B.
Se il business è sensibile agli intervalli di tempo, è necessario eseguire le attività pianificate sensibili al tempo in un processo separato per evitare il loro impatto su altre attività pianificate. Ad esempio, nel file `config/process.php` effettuare la seguente configurazione

```php
return [
    ....altre configurazioni, omesse per brevità....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Mettere le attività pianificate sensibili al tempo in `process/Task1.php` e le altre attività pianificate in `process/Task2.php`

### Altro
Per ulteriori istruzioni sulla configurazione di `config/process.php`, fare riferimento a [Processi personalizzati](../process.md)
