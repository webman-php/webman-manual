# Procedura di Aggiornamento

`composer require workerman/webman-framework ^1.4.3 && composer require webman/console ^1.0.27 && php webman install`

> **Nota**
> Poiché il proxy di Composer di Alibaba Cloud ha interrotto la sincronizzazione dei dati dal repository ufficiale di Composer, attualmente non è possibile aggiornare a webman più recente utilizzando il proxy di Alibaba Cloud. Si prega di utilizzare il seguente comando `composer config -g --unset repos.packagist` per ripristinare l'uso del repository dati ufficiale di Composer.
