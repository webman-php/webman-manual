# Notas de programação

## Sistema operativo
O webman suporta simultaneamente a execução em sistemas Linux e Windows. No entanto, devido ao fato de que o Workerman não suporta a configuração de vários processos e processos de daemon no Windows, é apenas recomendado que o Windows seja usado para desenvolvimento e depuração em ambientes de desenvolvimento. Para ambientes de produção, é recomendado o uso de sistemas Linux.

## Modo de inicialização
No **sistema Linux**, utilize o comando `php start.php start` (modo de depuração) ou `php start.php start -d` (modo de daemon) para iniciar o serviço.  
No **sistema Windows**, execute o `windows.bat` ou utilize o comando `php windows.php` para iniciar o serviço e pressione Ctrl+C para interromper. O sistema Windows não suporta comandos como stop, reload, status, connections, entre outros.

## Residência em memória
O webman é um framework de residência em memória, o que significa que, em geral, os arquivos PHP carregados na memória serão reutilizados e não serão lidos novamente do disco (com exceção dos arquivos de modelo). Portanto, após alterações no código de negócios ou nas configurações em um ambiente de produção, é necessário executar `php start.php reload` para que as alterações tenham efeito. Se houver alterações na configuração do processo ou se novos pacotes do Composer forem instalados, é necessário reiniciar o serviço com `php start.php restart`.

> Para facilitar o desenvolvimento, o webman inclui um monitor de processo personalizado para monitorar as atualizações nos arquivos de negócios, o qual executa automaticamente o reload quando há alterações nos arquivos. Esta funcionalidade é ativada apenas quando o Workerman é executado no modo de depuração (sem usar `-d` na inicialização). Os usuários do Windows precisam executar `windows.bat` ou `php windows.php` para ativá-lo.

## Sobre instruções de saída
Em projetos tradicionais de PHP-FPM, o uso de funções como `echo` e `var_dump` para produzir saída de dados resulta na exibição direta na página. No entanto, no webman, essas saídas geralmente aparecem no terminal e não na página (com exceção das saídas nos arquivos de modelo).

## Evite executar instruções `exit` ou `die`
A execução de `die` ou `exit` faz com que o processo seja encerrado e reiniciado, resultando na resposta incorreta à solicitação atual.

## Evite o uso da função `pcntl_fork`
O uso da função `pcntl_fork` para criar um processo não é permitido no webman.
