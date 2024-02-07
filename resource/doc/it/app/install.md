# Installazione

Ci sono due modi per installare i plugin dell'applicazione:

## Installazione dal mercato dei plugin
Accedi alla pagina dei plugin dell'applicazione nell '[amministratore ufficiale webman-admin](https://www.workerman.net/plugin/82)' e fai clic sul pulsante di installazione per installare il plugin dell'applicazione corrispondente.

## Installazione da un pacchetto sorgente
Scarica il pacchetto compresso del plugin dall'app store, decomprimilo e carica la directory estratta nella directory `{progetto principale}/plugin/`(se la directory plugin non esiste, creala manualmente), esegui `php webman app-plugin:install nome_plugin` per completare l'installazione.

Ad esempio, se il nome del file compresso scaricato è ai.zip, decomprimilo in `{progetto principale}/plugin/ai`, quindi esegui `php webman app-plugin:install ai` per completare l'installazione.

# Disinstallazione

Anche la disinstallazione dei plugin dell'applicazione ha due modi:

## Disinstallazione dal mercato dei plugin
Accedi alla pagina dei plugin dell'applicazione nell '[amministratore ufficiale webman-admin](https://www.workerman.net/plugin/82)' e fai clic sul pulsante di disinstallazione per disinstallare il plugin dell'applicazione corrispondente.

## Disinstallazione da un pacchetto sorgente
Esegui `php webman app-plugin:uninstall nome_plugin` per disinstallare, quindi manualmente elimina la directory del plugin corrispondente nella directory `{progetto principale}/plugin/`.
