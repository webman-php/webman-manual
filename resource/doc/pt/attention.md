# Nota de Programação

## Sistema Operacional
O webman suporta tanto o sistema Linux quanto o Windows. No entanto, devido à incapacidade do Workerman de suportar configurações de múltiplos processos e processos de demonização no Windows, é apenas recomendado para uso de desenvolvimento e depuração em ambientes de desenvolvimento, e para uso em ambientes de produção, é recomendado o uso do sistema Linux.

## Método de Inicialização
No sistema **Linux**, inicie com o comando `php start.php start` (modo de depuração) ou `php start.php start -d` (modo de processo de demonização).
No sistema **Windows**, execute `windows.bat` ou use o comando `php windows.php` para iniciar e use Ctrl + C para parar. O sistema Windows não suporta comandos como stop, reload, status, reload connections.

## Memória Residencial
O webman é um framework de memória residencial. Em geral, uma vez que o arquivo PHP é carregado na memória, será reutilizado e não será lido do disco novamente (exceto para arquivos de modelo). Portanto, após alterações no código de negócio ou configurações no ambiente de produção, é necessário executar `php start.php reload` para que as alterações tenham efeito. Se houver alterações nas configurações relacionadas ao processo ou se um novo pacote do Composer for instalado, é necessário reiniciar com `php start.php restart`.

> Para facilitar o desenvolvimento, o webman possui um monitor de processo personalizado para monitorar as atualizações de arquivos de negócio, e quando um arquivo de negócio é atualizado, o reload é executado automaticamente. Esta funcionalidade apenas é habilitada quando o Workerman está em execução no modo de depuração (não incluindo `-d` durante a inicialização). Os usuários do Windows precisam executar `windows.bat` ou `php windows.php` para habilitar essa funcionalidade.

## Sobre Declarações de Saída
Nos projetos tradicionais do PHP-FPM, o uso das funções `echo` e `var_dump` para imprimir dados é exibido diretamente na página. No entanto, no webman, essas saídas geralmente são exibidas no terminal e não na página (exceto para as saídas nos arquivos de modelo).

## Não Utilize as Declarações `exit` e `die`
A execução de `die` ou `exit` faz com que o processo seja encerrado e reiniciado, resultando na incapacidade de responder corretamente à solicitação atual.

## Não Utilize a Função `pcntl_fork`
O uso da função `pcntl_fork` para criar um processo não é permitido no webman.
