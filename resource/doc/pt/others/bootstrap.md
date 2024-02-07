# Inicialização do Negócio

Às vezes, precisamos fazer algumas inicializações de negócios após o início do processo. Essa inicialização é executada apenas uma vez durante o ciclo de vida do processo, como configurar um temporizador após o início do processo ou inicializar a conexão com o banco de dados. Abaixo, vamos explicar isso.

## Princípio
De acordo com a explicação no **[fluxo de execução](process.md)**, o webman carrega as classes definidas em `config/bootstrap.php` (incluindo `config/plugin/*/*/bootstrap.php`) após o início do processo e executa o método start da classe. Nós podemos adicionar código de negócios no método start para concluir a operação de inicialização do negócio após o início do processo.

## Fluxo
Suponha que queremos criar um temporizador para relatar regularmente a utilização de memória do processo atual, e nomeamos essa classe como `MemReport`.

#### Executar Comando
Execute o comando `php webman make:bootstrap MemReport` para gerar o arquivo de inicialização `app/bootstrap/MemReport.php`

> **Dica**
> Se o seu webman não tiver o `webman/console` instalado, execute o comando `composer require webman/console` para instalar.

#### Edite o Arquivo de Inicialização
Edite `app/bootstrap/MemReport.php` com o seguinte conteúdo:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Está no ambiente de linha de comando?
        $is_console = !$worker;
        if ($is_console) {
            // Se você não deseja que essa inicialização seja executada no ambiente de linha de comando, retorne aqui diretamente.
            return;
        }
        
        // Executar a cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Para facilitar a demonstração, usamos a saída no lugar do relatório real
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Dica**
> Ao usar a linha de comando, o framework também executará o método start configurado em `config/bootstrap.php`. Podemos decidir se executar o código de inicialização de negócios com base em se `$worker` é nulo.

#### Configure para Iniciar com o Processo
Abra `config/bootstrap.php` e adicione a classe `MemReport` aos itens de início.
```php
return [
    // ... outros itens de configuração omitidos ...
    
    app\bootstrap\MemReport::class,
];
```

Dessa forma, concluímos o fluxo de inicialização de negócios.

## Observações Adicionais
Após a inicialização, também será executado o método start configurado em `config/bootstrap.php` para [processos personalizados](../process.md). Podemos usar `$worker->name` para determinar qual é o processo atual e, em seguida, decidir se o seu código de inicialização de negócios deve ser executado nesse processo. Por exemplo, se não precisarmos monitorar o processo monitor, o conteúdo de `MemReport.php` será semelhante ao seguinte:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Está no ambiente de linha de comando?
        $is_console = !$worker;
        if ($is_console) {
            // Se você não deseja que essa inicialização seja executada no ambiente de linha de comando, retorne aqui diretamente.
            return;
        }
        
        // Não execute o temporizador para o processo monitor
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Executar a cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Para facilitar a demonstração, usamos a saída no lugar do relatório real
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
