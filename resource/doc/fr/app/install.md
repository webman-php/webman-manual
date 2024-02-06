# Installation

Il existe deux façons d'installer des plugins d'application :

## Installation depuis le marché des plugins
Accédez à la page des plugins d'application dans [l'interface d'administration officielle de webman-admin](https://www.workerman.net/plugin/82) et cliquez sur le bouton d'installation pour installer le plugin d'application correspondant.

## Installation à partir du package source
Téléchargez le fichier compressé du plugin d'application depuis le marché des applications, décompressez-le, puis téléversez le répertoire extrait dans le répertoire `{projet principal}/plugin/` (créez-le manuellement s'il n'existe pas), et exécutez `php webman app-plugin:install nom_du_plugin` pour terminer l'installation.

Par exemple, si vous avez téléchargé le fichier compressé nommé ai.zip et que vous l'avez extrait dans `{projet principal}/plugin/ai`, exécutez `php webman app-plugin:install ai` pour finaliser l'installation.

# Désinstallation

De la même manière, il existe deux façons de désinstaller un plugin d'application :

## Désinstallation depuis le marché des plugins
Accédez à la page des plugins d'application dans [l'interface d'administration officielle de webman-admin](https://www.workerman.net/plugin/82) et cliquez sur le bouton de désinstallation pour désinstaller le plugin d'application correspondant.

## Désinstallation à partir du package source
Exécutez `php webman app-plugin:uninstall nom_du_plugin` pour désinstaller, puis supprimez manuellement le répertoire du plugin correspondant dans le répertoire `{projet principal}/plugin/`.
