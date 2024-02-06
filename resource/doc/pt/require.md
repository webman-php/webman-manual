# Requisitos de Sistema

## Sistema Linux
O sistema Linux depende das extensões `posix` e `pcntl`, ambas as quais são extensões incorporadas ao PHP e geralmente não necessitam de instalação adicional.

Se você estiver utilizando o painel de controle Baota, basta desativar ou remover as funções com prefixo `pnctl_` no Baota.

A extensão `event` não é obrigatória, mas para obter melhor desempenho, é recomendável instalá-la.

## Sistema Windows
O webman é compatível com o sistema Windows, no entanto, devido à impossibilidade de configurar processos múltiplos e processos em segundo plano, é recomendável usar o Windows apenas para ambiente de desenvolvimento. Para ambientes de produção, o sistema Linux é recomendado.

Observação: no sistema Windows, não há dependência das extensões `posix` e `pcntl`.
