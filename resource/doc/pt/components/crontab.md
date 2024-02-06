# Componente de Tarefas Agendadas crontab

## workerman/crontab

### Descrição

O `workerman/crontab` é semelhante ao crontab do Linux, mas com suporte a agendamento de tarefas em nível de segundos.

Explicação de tempo:

```
0   1   2   3   4   5
|   |   |   |   |   |
|   |   |   |   |   +------ dia da semana (0 - 6) (domingo=0)
|   |   |   |   +------ mês (1 - 12)
|   |   |   +-------- dia do mês (1 - 31)
|   |   +---------- hora (0 - 23)
|   +------------ minuto (0 - 59)
+-------------- segundo (0-59)[Opcional, se não houver o bit 0, a menor unidade de tempo será minutos]
```

### Endereço do Projeto

https://github.com/walkor/crontab

### Instalação

```php
composer require workerman/crontab
```

### Uso

**Passo 1: Criar o arquivo do processo `process/Task.php`**

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
      
        // Executar às 7:50 todos os dias, observe que o bit de segundo foi omitido aqui
        new Crontab('50 7 * * *', function(){
            echo date('Y-m-d H:i:s')."\n";
        });
        
    }
}
```

**Passo 2: Configurar a inicialização do arquivo do processo com o webman**

Abrir o arquivo de configuração `config/process.php` e adicionar a seguinte configuração

```php
return [
    ....outras configurações, omitidas aqui....
  
    'task'  => [
        'handler'  => process\Task::class
    ],
];
```

**Passo 3: Reiniciar o webman**

> Nota: As tarefas agendadas não serão executadas imediatamente; todas as tarefas agendadas serão válidas apenas a partir do próximo minuto.

### Observação

O crontab não é assíncrono, por exemplo, em um processo de tarefa é configurado com duas cronometragens, A e B, ambas para serem executadas a cada segundo, mas se a tarefa A levar 10 segundos para ser executada, a tarefa B precisará aguardar a conclusão da tarefa A, resultando em atraso na execução da tarefa B.
Se o negócio for sensível ao intervalo de tempo, as tarefas agendadas sensíveis ao tempo devem ser colocadas em um processo separado para serem executadas, a fim de evitar serem influenciadas por outras tarefas agendadas. Por exemplo, no arquivo `config/process.php`, fazer a seguinte configuração:

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
Colocar as tarefas agendadas sensíveis ao tempo no arquivo `process/Task1.php` e as demais tarefas agendadas no arquivo `process/Task2.php`.

### Mais

Para mais explicações sobre a configuração do arquivo `config/process.php`, consulte [Processo Personalizado](../process.md).
