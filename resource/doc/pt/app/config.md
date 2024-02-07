# Ficheiro de Configuração

A configuração do plugin é semelhante à de um projeto webman normal, mas geralmente afeta apenas o plugin atual, sem influenciar o projeto principal.
Por exemplo, o valor de `plugin.foo.app.controller_suffix` afeta apenas o sufixo do controlador do plugin e não tem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.app.controller_reuse` afeta apenas se o controlador do plugin será reutilizado e não tem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.middleware` afeta apenas os middlewares do plugin e não tem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.view` afeta apenas as visualizações usadas pelo plugin e não tem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.container` afeta apenas o contêiner usado pelo plugin e não tem impacto no projeto principal.
Por exemplo, o valor de `plugin.foo.exception` afeta apenas a classe de tratamento de exceções do plugin e não tem impacto no projeto principal.

No entanto, como as rotas são globais, as rotas configuradas pelo plugin também afetam globalmente.

## Obter Configuração
Para obter a configuração de um determinado plugin, use `config('plugin.{plugin}.{configuração específica}');`, por exemplo, para obter todas as configurações de `plugin/foo/config/app.php`, use `config('plugin.foo.app')`.
Da mesma forma, o projeto principal ou outros plugins podem usar `config('plugin.foo.xxx')` para obter a configuração do plugin foo.

## Configurações não suportadas
Os plugins de aplicação não suportam as configurações server.php, session.php, não suportam `app.request_class`, `app.public_path`, `app.runtime_path`.
