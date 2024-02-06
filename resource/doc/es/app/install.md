# Instalación

La instalación de complementos de la aplicación se puede realizar de dos maneras:

## Instalación desde el mercado de complementos
Ingresa a la página de complementos de la aplicación en el [panel de administración oficial webman-admin](https://www.workerman.net/plugin/82), haz clic en el botón de instalación para instalar el complemento de la aplicación correspondiente.

## Instalación desde el paquete fuente
Descarga el paquete comprimido del complemento de la aplicación desde el mercado de aplicaciones, descomprímelo y carga el directorio descomprimido en el directorio `{proyecto principal}/plugin/` (si el directorio del complemento no existe, se debe crear manualmente), luego ejecuta `php webman app-plugin:install nombre_del_complemento` para completar la instalación.

Por ejemplo, si el nombre del paquete comprimido descargado es "ai.zip", descomprímelo en `{proyecto principal}/plugin/ai` y ejecuta `php webman app-plugin:install ai` para finalizar la instalación.

# Desinstalación

Del mismo modo, la desinstalación de complementos de la aplicación también se puede realizar de dos maneras:

## Desinstalación desde el mercado de complementos
Ingresa a la página de complementos de la aplicación en el [panel de administración oficial webman-admin](https://www.workerman.net/plugin/82), haz clic en el botón de desinstalación para desinstalar el complemento de la aplicación correspondiente.

## Desinstalación desde el paquete fuente
Ejecuta `php webman app-plugin:uninstall nombre_del_complemento` para desinstalarlo, luego manualmente elimina el directorio del complemento correspondiente en el directorio`{proyecto principal}/plugin/`.
