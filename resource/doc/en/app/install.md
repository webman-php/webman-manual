# Installation

There are two ways to install application plugins:

## Install from plugin market:
Go to the application plugin page of [official management background of webman-admin](https://www.workerman.net/plugin/82) and click the install button to install the corresponding application plugin.

## Install from source code package:
Download the application plugin compressed package from the application market, unzip it, and upload the unzipped directory to the `{main project}/plugin/` directory (if the plugin directory does not exist, create it manually). Run `php webman app-plugin:install plugin-name` to complete the installation.

For example, if the downloaded compressed package is named "ai.zip", unzip it to `{main project}/plugin/ai`. Run `php webman app-plugin:install ai` to complete the installation.

# Uninstallation

Similarly, there are two ways to uninstall application plugins:

## Uninstall from plugin market:
Go to the application plugin page of [official management background of webman-admin](https://www.workerman.net/plugin/82) and click the uninstall button to uninstall the corresponding application plugin.

## Uninstall from source code package:
Run `php webman app-plugin:uninstall plugin-name` to complete the uninstallation. After that, manually delete the corresponding plugin directory under `{main project}/plugin/`.
