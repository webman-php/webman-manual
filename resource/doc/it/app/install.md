# Installazione

Ci sono due modi per installare i plugin dell'applicazione:

## Installazione dal Market dei Plugin
Accedi alla pagina dei plugin dell'applicazione nell'[amministratore webman ufficiale](https://www.workerman.net/plugin/82) e fai clic sul pulsante di installazione per installare il plugin dell'applicazione corrispondente.

## Installazione dal pacchetto di origine
Scarica il file compresso del plugin dall'app store, decomprimilo, carica la directory di decompressione in `{main_project}/plugin/` (crea manualmente la directory se non esiste) e esegui `php webman app-plugin:install nome_plugin` per completare l'installazione.

Ad esempio, se il nome del file compresso scaricato è ai.zip, decomprimilo in `{main_project}/plugin/ai` e esegui `php webman app-plugin:install ai` per completare l'installazione.


# Disinstallazione

Anche la disinstallazione dei plugin dell'applicazione ha due modi:

## Disinstallazione dal Market dei Plugin
Accedi alla pagina dei plugin dell'applicazione nell'[amministratore webman ufficiale](https://www.workerman.net/plugin/82) e fai clic sul pulsante di disinstallazione per disinstallare il plugin dell'applicazione corrispondente.

## Disinstallazione dal pacchetto di origine
Esegui `php webman app-plugin:uninstall nome_plugin` per disinstallare e, successivamente, elimina manualmente la directory del plugin corrispondente nella directory `{main_project}/plugin/`.
