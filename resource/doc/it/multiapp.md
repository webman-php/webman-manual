# Applicazioni multiple
A volte un progetto può essere suddiviso in diversi sotto-progetti, ad esempio un negozio online potrebbe essere composto da tre sotto-progetti: il negozio principale, l'interfaccia API del negozio e il pannello di controllo amministrativo del negozio, ognuno di essi condividendo la stessa configurazione del database.

Con webman è possibile organizzare la directory delle app in questo modo:
```plaintext
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Quando si accede all'indirizzo `http://127.0.0.1:8787/shop/{controller}/{metodo}`, si accede al controller e al metodo nella directory `app/shop/controller`.

Quando si accede all'indirizzo `http://127.0.0.1:8787/api/{controller}/{metodo}`, si accede al controller e al metodo nella directory `app/api/controller`.

Quando si accede all'indirizzo `http://127.0.0.1:8787/admin/{controller}/{metodo}`, si accede al controller e al metodo nella directory `app/admin/controller`.

In webman, è persino possibile organizzare la directory delle app in questo modo:
```plaintext
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
In questo modo, quando si accede all'indirizzo `http://127.0.0.1:8787/{controller}/{metodo}`, si accede al controller e al metodo nella directory `app/controller`. Quando il percorso inizia con "api" o "admin", si accede al controller e al metodo nella directory corrispondente.

Quando si utilizzano più app, lo spazio dei nomi delle classi deve essere conforme a `psr4`. Ad esempio, per il file `app/api/controller/FooController.php`, la classe sarebbe simile a questa:
```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Configurazione dei middleware per app multiple
A volte si desidera configurare middleware diversi per app diverse, ad esempio l'app "api" potrebbe richiedere un middleware per la gestione della cross-origin resource sharing (CORS), mentre l'app "admin" richiede un middleware per controllare il login degli amministratori. In tal caso, la configurazione del file `config/midlleware.php` potrebbe essere simile a questa:
```php
return [
    // Middleware globale
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware dell'app "api"
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware dell'app "admin"
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> I middleware sopra potrebbero non esistere, sono solo un esempio di come configurare i middleware per app diverse.

L'ordine di esecuzione dei middleware è: `middleware globale` -> `middleware dell'app`.

Per lo sviluppo dei middleware fare riferimento al [capitolo sui middleware](middleware.md).

## Configurazione della gestione delle eccezioni per app multiple
Allo stesso modo, si può desiderare configurare classi di gestione delle eccezioni diverse per app diverse, ad esempio in caso di eccezioni nell'app "shop" si potrebbe voler fornire una pagina di errore amichevole, mentre in "api" si potrebbe voler restituire una stringa JSON anziché una pagina. La configurazione del file `config/exception.php` per la gestione delle eccezioni per app diverse potrebbe essere simile a questa:
```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> A differenza dei middleware, ogni app può essere configurata solo con una classe di gestione delle eccezioni.

> Le classi di gestione delle eccezioni sopra potrebbero non esistere, sono solo un esempio di come configurare la gestione delle eccezioni per app diverse.

Per lo sviluppo della gestione delle eccezioni fare riferimento al [capitolo sulla gestione delle eccezioni](exception.md).
