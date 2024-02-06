# Applicazioni multiple
A volte un progetto può essere suddiviso in più sotto-progetti, ad esempio un negozio online potrebbe essere diviso in un progetto principale del negozio, un'API del negozio e un'area di amministrazione del negozio, tutti utilizzano la stessa configurazione del database.

Webman ti consente di pianificare la directory dell'applicazione in questo modo:
```
app
├── shop
│   ├── controller
│   ├── model
│   └── view
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
Quando si accede all'indirizzo `http://127.0.0.1:8787/shop/{controller}/{metodo}`, si accede al controller e al metodo sotto `app/shop/controller`.

Quando si accede all'indirizzo `http://127.0.0.1:8787/api/{controller}/{metodo}`, si accede al controller e al metodo sotto `app/api/controller`.

Quando si accede all'indirizzo `http://127.0.0.1:8787/admin/{controller}/{metodo}`, si accede al controller e al metodo sotto `app/admin/controller`.

In webman, è persino possibile pianificare la directory dell'applicazione in questo modo:
```
app
├── controller
├── model
├── view
│
├── api
│   ├── controller
│   └── model
└── admin
    ├── controller
    ├── model
    └── view
```
In questo modo, quando si accede all'indirizzo `http://127.0.0.1:8787/{controller}/{metodo}`, si accede al controller e al metodo sotto `app/controller`. Quando il percorso inizia con api o admin, si accede ai corrispondenti controller e metodi nella directory relativa.

Per le applicazioni multiple, lo spazio dei nomi delle classi deve essere conforme a `psr4`. Ad esempio, il file `app/api/controller/FooController.php` sarebbe simile al seguente:

```php
<?php
namespace app\api\controller;

use support\Request;

class FooController
{
    
}

```

## Configurazione del middleware per applicazioni multiple
A volte si desidera configurare middleware diversi per diverse applicazioni. Ad esempio, l'applicazione `api` potrebbe richiedere un middleware di controllo degli accessi mentre `admin` potrebbe richiedere un middleware di controllo dell'accesso degli amministratori. La configurazione del file `config/midlleware.php` potrebbe essere simile a quanto segue:

```php
return [
    // Middleware globali
    '' => [
        support\middleware\AuthCheck::class,
    ],
    // Middleware dell'applicazione api
    'api' => [
         support\middleware\AccessControl::class,
     ],
    // Middleware dell'applicazione admin
    'admin' => [
         support\middleware\AdminAuthCheck::class,
         support\middleware\SomeOtherClass::class,
    ],
];
```
> I middleware sopra menzionati potrebbero non esistere, qui vengono forniti solo come esempio su come configurare i middleware per le diverse applicazioni

L'ordine di esecuzione del middleware è `middleware globale` -> `middleware dell'applicazione`.

Per lo sviluppo del middleware fare riferimento al capitolo [Middleware](middleware.md)

## Configurazione della gestione delle eccezioni per applicazioni multiple
Anche in questo caso, si desidera configurare classi di gestione delle eccezioni diverse per diverse applicazioni. Ad esempio, quando si verifica un'eccezione nell'applicazione `shop`, potrebbe essere desiderabile visualizzare una pagina di errore amichevole; mentre quando si verifica un'eccezione nell'applicazione `api`, si potrebbe voler restituire una stringa JSON anziché una pagina. La configurazione del file `config/exception.php` per le diverse applicazioni potrebbe essere simile a quanto segue:

```php
return [
    'shop' => support\exception\Handler::class,
    'api' => support\exception\ApiHandler::class,
];
```
> A differenza del middleware, ogni applicazione può configurare solo una classe di gestione delle eccezioni.

> Le classi di gestione delle eccezioni sopra menzionate potrebbero non esistere, qui vengono fornite solo come esempio su come configurare la gestione delle eccezioni per le diverse applicazioni

Per lo sviluppo della gestione delle eccezioni fare riferimento al capitolo [Gestione delle eccezioni](exception.md)
