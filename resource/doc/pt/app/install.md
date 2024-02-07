# Instalação

Existem duas maneiras de instalar plugins de aplicativos:

## Instalação no Mercado de Plugins
Acesse a página de plugins de aplicativos do [painel de controle oficial webman-admin](https://www.workerman.net/plugin/82) e clique no botão de instalação para instalar o plugin de aplicativo correspondente.

## Instalação a partir do Pacote de Código-fonte
Baixe o pacote de código-fonte do mercado de aplicativos, descompacte e faça o upload do diretório descompactado para o diretório `{main_project}/plugin/` (crie manualmente o diretório plugin se ele não existir) e execute `php webman app-plugin:install nome_do_plugin` para concluir a instalação.

Por exemplo, se o nome do pacote descompactado for ai.zip, descompacte-o para `{main_project}/plugin/ai` e execute `php webman app-plugin:install ai` para concluir a instalação.


# Desinstalação

Da mesma forma, existem duas maneiras de desinstalar plugins de aplicativos:

## Desinstalação no Mercado de Plugins
Acesse a página de plugins de aplicativos do [painel de controle oficial webman-admin](https://www.workerman.net/plugin/82) e clique no botão de desinstalação para desinstalar o plugin de aplicativo correspondente.

## Desinstalação a partir do Pacote de Código-fonte
Execute `php webman app-plugin:uninstall nome_do_plugin` para desinstalar e, após a execução, exclua manualmente o diretório do plugin correspondente em `{main_project}/plugin/`.
