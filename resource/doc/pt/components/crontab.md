# Componente de tarefas agendadas crontab

## workerman/crontab

### Descrição

`workerman/crontab` é semelhante ao crontab do Linux, a diferença é que o `workerman/crontab` suporta tarefas agendadas com precisão de segundos.

Explicação do tempo:

``` 
0    1    2    3    4    5
|    |    |    |    |    |
|    |    |    |    |    +------ dia da semana (0 - 6) (Domingo=0)
|    |    |    |    +------ mês (1 - 12)
|    |    |    +-------- dia do mês (1 - 31)
|    |    +---------- hora (0 - 23)
|    +------------ minuto (0 - 59)
+-------------- segundo (0-59) [pode ser omitido, se omitido, a menor unidade de tempo é o minuto]
```

### Endereço do projeto

https://github.com/walkor/crontab

### Instalação

```php
composer require workerman/crontab
```

### Uso

**Passo 1: Criar o arquivo de processo `process/Task.php`**

```php
<?php
namespace process;

use Workerman\Crontab\Crontab;

class Task
{
    public function onWorkerStart()
    {
    
        // Executar a cada segundo
        new Crontab('*/1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Executar a cada 5 segundos
        new Crontab('*/5 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Executar a cada minuto
        new Crontab('0 */1 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Executar a cada 5 minutos
        new Crontab('0 */5 * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
        // Executar no primeiro segundo de cada minuto
        new Crontab('1 * * * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
      
        // Executar às 7:50 diariamente, observação: o segundo foi omitido aqui
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Passo 2: Configurar o arquivo de processo para iniciar com o webman**

Abra o arquivo de configuração `config/process.php` e adicione a seguinte configuração

```php
return [
    ....outras configurações, omitidas aqui....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Passo 3: Reiniciar o webman**

> Observação: As tarefas agendadas não serão executadas imediatamente. Todas as tarefas agendadas serão iniciadas a cada minuto.

### Explicação

O crontab não é assíncrono, por exemplo, em um processo de tarefa são configurados dois cronômetros, A e B, que executam uma tarefa a cada segundo. No entanto, se a tarefa A demorar 10 segundos para ser concluída, a tarefa B precisará esperar a conclusão de A antes de ser executada, causando um atraso na execução da tarefa B.
Se o negócio for sensível ao intervalo de tempo, as tarefas agendadas sensíveis ao tempo devem ser executadas em um processo separado para evitar a interferência de outras tarefas agendadas. Por exemplo, configure o arquivo `config/process.php` da seguinte maneira.

```php
return [
    ....outras configurações, omitidas aqui....
  
    'task1'  => [
        'handler'  => process\Task1::class
    ],
    'task2'  => [
        'handler'  => process\Task2::class
    ],
];
```
Coloque as tarefas agendadas sensíveis ao tempo em `process/Task1.php` e as outras tarefas agendadas em `process/Task2.php`.

### Mais

Para mais explicações sobre configuração em `config/process.php`, consulte [Processo Personalizado](../process.md).
