# Inicialização de Negócios

Às vezes, precisamos fazer uma inicialização de negócios após o início do processo, essa inicialização é executada apenas uma vez durante o ciclo de vida do processo, como configurar um temporizador após o início do processo ou inicializar a conexão com o banco de dados, etc. Abaixo, explicaremos como fazer isso.

## Princípio
De acordo com a explicação em **[fluxo de execução](process.md)**, após o início do processo, o webman carregará as classes definidas em `config/bootstrap.php` (incluindo `config/plugin/*/*/bootstrap.php`) e executará o método start da classe. Podemos adicionar o código de negócios ao método start para concluir a operação de inicialização de negócios após o início do processo.

## Fluxo
Suponhamos que precisamos implementar um temporizador para relatar o uso atual de memória do processo em intervalos regulares. Vamos chamar essa classe de `MemReport`.

#### Execute o comando

Execute o comando `php webman make:bootstrap MemReport` para gerar o arquivo de inicialização `app/bootstrap/MemReport.php`

> **Dica**
> Se o seu webman não tiver `webman/console` instalado, execute o comando `composer require webman/console` para instalar.

#### Edite o arquivo de inicialização
Edite o arquivo `app/bootstrap/MemReport.php` com um conteúdo semelhante ao seguinte:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Está no ambiente de linha de comando ?
        $is_console = !$worker;
        if ($is_console) {
            // Se você não quiser que esta inicialização seja executada no ambiente de linha de comando, então retorne diretamente aqui
            return;
        }
        
        // Executa a cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Por uma questão de demonstração, aqui usamos a saída em vez do processo de relatório
            echo memory_get_usage() . "\n";
        });
        
    }

}
```

> **Dica**
> Ao usar a linha de comando, o framework também executará o método de start configurado em `config/bootstrap.php`. Podemos determinar se estamos no ambiente de linha de comando com base em `$worker` sendo `null` e, assim, decidir se executamos o código de inicialização de negócios.

#### Configure para iniciar com o processo
Abra `config/bootstrap.php` e adicione a classe `MemReport` aos itens de inicialização.
```php
return [
    // ... outros valores foram omitidos aqui ...

    app\bootstrap\MemReport::class,
];
```

Desta forma, completamos o processo de inicialização de negócios.

## Observação Adicional
Os métodos de start configurados em `config/bootstrap.php` também serão executados depois que os [processos personalizados](../process.md) forem iniciados. Podemos usar `$worker->name` para determinar qual processo está sendo executado e, assim, decidir se devemos executar o código de inicialização de negócios nesse processo. Por exemplo, se não precisarmos monitorar o processo "monitor", o conteúdo de `MemReport.php` será semelhante ao seguinte:
```php
<?php

namespace app\bootstrap;

use Webman\Bootstrap;

class MemReport implements Bootstrap
{
    public static function start($worker)
    {
        // Está no ambiente de linha de comando ?
        $is_console = !$worker;
        if ($is_console) {
            // Se você não quiser que esta inicialização seja executada no ambiente de linha de comando, então retorne diretamente aqui
            return;
        }
        
        // O temporizador não será executado para o processo monitor
        if ($worker->name == 'monitor') {
            return;
        }
        
        // Executa a cada 10 segundos
        \Workerman\Timer::add(10, function () {
            // Por uma questão de demonstração, aqui usamos a saída em vez do processo de relatório
            echo memory_get_usage() . "\n";
        });
        
    }

}
```
