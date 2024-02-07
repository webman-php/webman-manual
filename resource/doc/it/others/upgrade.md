# Procedura di aggiornamento

```
composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install
```

> **Nota**
> Poiché il proxy di composer di Alibaba Cloud ha smesso di sincronizzare i dati dal repository ufficiale di composer, attualmente non è possibile utilizzare il proxy di Alibaba Cloud per aggiornare a webman più recente. Si prega di utilizzare il comando seguente `composer config -g --unset repos.packagist` per ripristinare l'uso del repository dati ufficiale di composer.
