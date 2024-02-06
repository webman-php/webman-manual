# Creare un plugin dell'applicazione

## Identificativo unico

Ogni plugin ha un identificativo unico dell'applicazione. Prima di sviluppare un plugin, il programmatore deve pensare a un'identificazione e verificare che non sia già in uso. Verifica l'identificativo all'indirizzo [Verifica dell'identificativo dell'applicazione](https://www.workerman.net/app/check)

## Creazione

Esegui `composer require webman/console` per installare la riga di comando di webman.

Usa il comando `php webman app-plugin:create {identificativo del plugin}` per creare un plugin dell'applicazione in locale.

Ad esempio: `php webman app-plugin:create foo`

Riavvia webman.

Visita `http://127.0.0.1:8787/app/foo`. Se viene restituito un contenuto, significa che la creazione è avvenuta con successo.
