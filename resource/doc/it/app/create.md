# Creare un'applicazione di plugin

## Identificatore unico

Ogni plugin ha un'identificazione unica dell'applicazione, pertanto gli sviluppatori devono pensare a un'identificazione prima di sviluppare e verificare che l'identificazione non sia già occupata.
Indirizzo di verifica dell'identificazione dell'applicazione [Check dell'identificazione dell'applicazione](https://www.workerman.net/app/check)

## Creazione

Eseguire `composer require webman/console` per installare la console webman

Utilizzare il comando `php webman app-plugin:create {identificatore del plugin}` per creare un'applicazione di plugin locale

Ad esempio `php webman app-plugin:create foo`

Riavviare webman

Visitare `http://127.0.0.1:8787/app/foo` se viene restituito un contenuto, significa che la creazione è riuscita.
