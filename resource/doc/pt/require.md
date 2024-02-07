# Requisitos de ambiente


## Sistema Linux
O sistema Linux depende das extensões `posix` e `pcntl`, as quais são extensões embutidas no PHP e geralmente podem ser usadas sem a necessidade de instalação.

Caso esteja utilizando o painel de controle Baota, basta desativar ou remover as funções iniciadas com `pnctl_`.

Embora a extensão `event` não seja obrigatória, recomenda-se a sua instalação para obter um desempenho melhor.

## Sistema Windows
O webman pode ser executado no sistema Windows, porém, devido à incapacidade de configurar processos múltiplos, processos daemon, entre outros motivos, sugere-se que o Windows seja utilizado apenas como ambiente de desenvolvimento, sendo o ambiente de produção preferencialmente o sistema Linux.

Observação: O sistema Windows não depende das extensões `posix` e `pcntl`.
