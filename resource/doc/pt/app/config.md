# Ficheiro de Configuração

A configuração do plugin é semelhante à de um projeto webman comum. No entanto, a configuração do plugin geralmente só afeta o plugin atual e não tem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.app.controller_suffix` afeta apenas o sufixo do controlador do plugin, sem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.app.controller_reuse` afeta apenas se o plugin reutiliza controladores, sem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.middleware` afeta apenas os middlewares do plugin, sem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.view` afeta apenas as visualizações usadas pelo plugin, sem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.container` afeta apenas o contêiner usado pelo plugin, sem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.exception` afeta apenas a classe de tratamento de exceção do plugin, sem impacto no projeto principal.

No entanto, como as rotas são globais, as rotas configuradas pelo plugin também afetam globalmente.

## Obter Configuração
Para obter a configuração de um determinado plugin, o método é `config('plugin.{plugin}.{configuração específica}')`. Por exemplo, para obter todas as configurações de `plugin/foo/config/app.php`, o método seria `config('plugin.foo.app')`.
Da mesma forma, o projeto principal ou outros plugins podem usar `config('plugin.foo.xxx')` para obter a configuração do plugin foo.

## Configurações Não Suportadas
As configurações do `server.php` e `session.php` não são suportadas para aplicativos de plugins, assim como as configurações `app.request_class`, `app.public_path`, e `app.runtime_path`.
