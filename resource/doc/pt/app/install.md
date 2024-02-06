# Instalação

Existem duas maneiras de instalar um plug-in de aplicativo:

## Instalação no mercado de plug-ins
Acesse a página de plug-ins de aplicativos no [painel de administração oficial do webman-admin](https://www.workerman.net/plugin/82) e clique no botão de instalação para instalar o plug-in do aplicativo correspondente.

## Instalação a partir do pacote de origem
Baixe o pacote compactado do plug-in do mercado de aplicativos, descompacte e faça o upload do diretório descompactado para o diretório `{projeto principal}/plugin/` (se o diretório plugin não existir, crie manualmente), execute `php webman app-plugin:install nome_do_plugin` para concluir a instalação.

Por exemplo, se o nome do pacote compactado for ai.zip, descomprima em `{projeto principal}/plugin/ai` e execute `php webman app-plugin:install ai` para concluir a instalação.

# Desinstalação

Da mesma forma, a desinstalação do plug-in do aplicativo também pode ser feita de duas maneiras:

## Desinstalação no mercado de plug-ins
Acesse a página de plug-ins de aplicativos no [painel de administração oficial do webman-admin](https://www.workerman.net/plugin/82) e clique no botão de desinstalação para desinstalar o plug-in do aplicativo correspondente.

## Desinstalação a partir do pacote de origem
Execute `php webman app-plugin:uninstall nome_do_plugin` para desinstalar e, em seguida, exclua manualmente o diretório correspondente ao plug-in em `{projeto principal}/plugin/`.
