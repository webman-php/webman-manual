# Norme per lo sviluppo di plugin dell'applicazione

## Requisiti per i plugin dell'applicazione
* I plugin non devono contenere codice, icone, immagini, ecc. che violino i diritti
* Il codice sorgente del plugin deve essere completo e non crittografato
* Il plugin deve offrire funzionalità complete e non essere semplicemente decorativo
* Devono essere fornite informazioni complete sulle funzionalità e documentazione del plugin
* Il plugin non può includere sottomercati
* Il plugin non può contenere testi o collegamenti promozionali

## Identificazione del plugin dell'applicazione
Ogni plugin dell'applicazione ha un'identificazione unica, composta da lettere. Questa identificazione influisce sul nome della directory del codice sorgente del plugin, sul namespace della classe e sul prefisso della tabella del database del plugin.

Supponiamo che lo sviluppatore utilizzi "foo" come identificazione del plugin. Quindi, il codice sorgente del plugin si troverà nella directory `{progetto principale}/plugin/foo`, il namespace corrispondente del plugin sarà `plugin\foo`, e il prefisso della tabella del database del plugin sarà `foo_`.

Poiché l'identificazione è univoca in tutto il web, lo sviluppatore deve verificare la disponibilità dell'identificazione prima di iniziare lo sviluppo, tramite il link di verifica dell'identificazione dell'applicazione [Verifica Identificazione dell'Applicazione](https://www.workerman.net/app/check).

## Database
* I nomi delle tabelle devono essere composti da lettere minuscole da `a` a `z` e da trattini bassi `_`
* Le tabelle dei dati del plugin dovrebbero avere come prefisso l'identificazione del plugin, ad esempio la tabella "article" del plugin "foo" sarà `foo_article`
* La chiave primaria della tabella dovrebbe essere l'indice "id"
* Deve essere utilizzato uniformemente il motore di archiviazione InnoDB
* Deve essere utilizzato uniformemente il set di caratteri utf8mb4_general_ci
* Sia laravel che think-orm possono essere utilizzati per l'ORM del database
* Si consiglia di utilizzare il campo DateTime per i campi temporali

## Norme sul codice

#### Norme PSR
Il codice dovrebbe seguire le norme di caricamento PSR4

#### Nomi delle classi in stile camelCase con lettera maiuscola iniziale
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Attributi e metodi delle classi in stile camelCase con lettera minuscola iniziale
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Metodo non richiede autenticazione
     * @var array
     */
    protected $noNeedAuth = ['getComments'];
    
    /**
     * Ottieni commenti
     * @param Request $request
     * @return Response
     * @throws BusinessException
     */
    public function getComments(Request $request): Response
    {
        
    }
}
```

#### Commenti
Gli attributi e i metodi delle classi devono includere commenti, compresi una descrizione generale, i parametri e il tipo di ritorno

#### Indentazione
Il codice dovrebbe essere indentato con 4 spazi invece che con il tabulatore

#### Controllo del flusso
Dopo le parole chiave di controllo del flusso (if, for, while, foreach, ecc.) deve essere presente uno spazio, e le parentesi graffe del blocco di controllo devono iniziare sulla stessa riga della parola chiave di controllo
```php
foreach ($users as $uid => $user) {

}
```

#### Naming delle variabili temporanee
Si consiglia di utilizzare lo stile camelCase con lettera minuscola iniziale per i nomi delle variabili temporanee (non obbligatorio)
```php
$articleCount = 100;
```
