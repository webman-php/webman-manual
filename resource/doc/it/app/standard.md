# Norme per lo sviluppo di plugin dell'applicazione

## Requisiti dei plugin dell'applicazione
* Il plugin non deve contenere codice, icone, immagini o altro materiale che violi i diritti d'autore
* Il codice sorgente del plugin deve essere completo e non può essere crittografato
* Il plugin deve offrire funzionalità complete e non può essere una semplice funzione
* Deve essere fornita una descrizione completa delle funzionalità e della documentazione
* Il plugin non può includere sottomercati
* All'interno del plugin non possono essere presenti testi o collegamenti promozionali

## Identificazione del plugin dell'applicazione
Ogni plugin dell'applicazione ha un'identificazione univoca, composta da lettere. Questa identificazione influisce sul nome della directory in cui si trova il codice sorgente del plugin, sul namespace della classe e sul prefisso della tabella del database del plugin.

Ad esempio, se lo sviluppatore utilizza "foo" come identificazione del plugin, allora il codice sorgente del plugin si troverà in `{directory principale del progetto}/plugin/foo`, il namespace del plugin corrispondente sarà `plugin\foo`, e il prefisso della tabella nel database del plugin sarà `foo_`.

Poiché l'identificazione è univoca in tutto il web, gli sviluppatori devono verificare preventivamente la disponibilità dell'identificazione prima di iniziare lo sviluppo. Può essere effettuata una verifica all'indirizzo [Verifica Identificazione dell'Applicazione](https://www.workerman.net/app/check).

## Database
* I nomi delle tabelle devono essere composti da lettere minuscole `a-z` e underscore `_`
* Le tabelle dei dati del plugin dovrebbero avere come prefisso l'identificazione del plugin. Ad esempio, la tabella "article" del plugin "foo" sarà `foo_article`
* La chiave primaria della tabella dovrebbe essere denominata "id"
* Si consiglia di utilizzare il motore di archiviazione InnoDB in modo uniforme
* Si consiglia di utilizzare il set di caratteri utf8mb4_general_ci in modo uniforme
* È possibile utilizzare l'ORM del database Laravel o Think-ORM
* Si consiglia di utilizzare il campo di tipo DateTime per i campi temporali

## Norme di codifica

#### Norme PSR
Il codice deve essere conforme alle norme di caricamento PSR4

#### Nomenclatura delle classi in stile CamelCase con lettera maiuscola iniziale
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    
}
```

#### Le proprietà e i metodi delle classi iniziano con una lettera minuscola in stile CamelCase
```php
<?php

namespace plugin\foo\app\controller;

class ArticleController
{
    /**
     * Metodi che non richiedono autenticazione
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
Le proprietà e i metodi della classe devono includere commenti, compresi una descrizione generale, i parametri e il tipo di ritorno

#### Indentazione
Il codice dovrebbe utilizzare 4 spazi per l'indentazione anziché il tabulatore

#### Controllo del flusso
Dopo le parole chiave di controllo del flusso (if for while foreach, ecc.), deve essere presente uno spazio, e le parentesi graffe dovrebbero iniziare sulla stessa riga della parola chiave di controllo.
```php
foreach ($users as $uid => $user) {

}
```

#### Nomi delle variabili temporanee
Si consiglia di utilizzare la nomenclatura in stile CamelCase con lettera minuscola iniziale (non obbligatorio)
```php
$articleCount = 100;
```
