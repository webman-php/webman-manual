# Installation

Il existe deux façons d'installer des plugins d'application :

## Installation depuis le marché des plugins
Accédez à la page des plugins d'application du [panneau d'administration officiel webman-admin](https://www.workerman.net/plugin/82) et cliquez sur le bouton d'installation pour installer le plugin d'application correspondant.

## Installation à partir du package source
Téléchargez le package compressé du plugin d'application depuis le marché des applications, décompressez-le et téléversez le répertoire extrait dans le répertoire `{projet_principal}/plugin/` (créez manuellement le répertoire plugin s'il n'existe pas), puis exécutez `php webman app-plugin:install nom_du_plugin` pour terminer l'installation.

Par exemple, si le nom du package compressé est ai.zip, décompressez-le dans `{projet_principal}/plugin/ai`, puis exécutez `php webman app-plugin:install ai` pour terminer l'installation.

# Désinstallation

De même, il existe deux façons de désinstaller un plugin d'application :

## Désinstallation depuis le marché des plugins
Accédez à la page des plugins d'application du [panneau d'administration officiel webman-admin](https://www.workerman.net/plugin/82) et cliquez sur le bouton de désinstallation pour désinstaller le plugin d'application correspondant.

## Désinstallation à partir du package source
Exécutez `php webman app-plugin:uninstall nom_du_plugin` pour désinstaller, puis supprimez manuellement le répertoire du plugin correspondant dans le répertoire `{projet_principal}/plugin/` après l'exécution.
