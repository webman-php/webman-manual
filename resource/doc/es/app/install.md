# Instalación

Hay dos formas de instalar los complementos de la aplicación:

## Instalación desde el mercado de complementos
Vaya a la página de complementos de la aplicación en el [panel de administración oficial webman-admin](https://www.workerman.net/plugin/82) y haga clic en el botón de instalación para instalar el complemento de la aplicación correspondiente.

## Instalación desde el paquete fuente
Descargue el archivo comprimido del complemento de la aplicación desde el mercado de aplicaciones, descomprímalo y luego suba el directorio descomprimido a la carpeta `{proyecto principal}/plugin/` (si el directorio de complementos no existe, debe crearlo manualmente). Luego, ejecute `php webman app-plugin:install nombre_del_complemento` para completar la instalación.

Por ejemplo, si el nombre del archivo comprimido descargado es ai.zip, descomprímalo en `{proyecto principal}/plugin/ai` y luego ejecute `php webman app-plugin:install ai` para completar la instalación.


# Desinstalación

Del mismo modo, hay dos formas de desinstalar un complemento de la aplicación:

## Desinstalación desde el mercado de complementos
Vaya a la página de complementos de la aplicación en el [panel de administración oficial webman-admin](https://www.workerman.net/plugin/82) y haga clic en el botón de desinstalación para desinstalar el complemento de la aplicación correspondiente.

## Desinstalación desde el paquete fuente
Ejecute `php webman app-plugin:uninstall nombre_del_complemento` para desinstalar el complemento. Una vez completado, elimine manualmente el directorio del complemento correspondiente en la carpeta `{proyecto principal}/plugin/`.
