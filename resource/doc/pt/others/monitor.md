# Monitorar Processos
webman possui um processo de monitoramento embutido que oferece suporte a duas funcionalidades:
1. Monitoramento de atualizações de arquivos e carregamento automático de novo código de negócios (geralmente usado durante o desenvolvimento).
2. Monitoramento de todos os processos que ocupam memória; se um processo estiver prestes a exceder o limite de 'memory_limit' definido no 'php.ini', o processo será reiniciado com segurança (sem impacto no negócio).

## Configuração de Monitoramento
No arquivo de configuração `config/process.php`, a configuração de `monitor` é a seguinte:
```php
global $argv;

return [
    // Detecção de atualizações de arquivos e recarregamento automático
    'monitor' => [
        'handler' => process\Monitor::class,
        'reloadable' => false,
        'constructor' => [
            // Monitorar esses diretórios
            'monitorDir' => array_merge([    // Quais diretórios devem ser monitorados
                app_path(),
                config_path(),
                base_path() . '/process',
                base_path() . '/support',
                base_path() . '/resource',
                base_path() . '/.env',
            ], glob(base_path() . '/plugin/*/app'), glob(base_path() . '/plugin/*/config'), glob(base_path() . '/plugin/*/api')),
            // Arquivos com essas extensões serão monitorados
            'monitorExtensions' => [
                'php', 'html', 'htm', 'env'
            ],
            'options' => [
                'enable_file_monitor' => !in_array('-d', $argv) && DIRECTORY_SEPARATOR === '/', // Habilitar monitoramento de arquivos
                'enable_memory_monitor' => DIRECTORY_SEPARATOR === '/',                      // Habilitar monitoramento de memória
            ]
        ]
    ]
];
```
`monitorDir` é usado para configurar quais diretórios monitorar para atualizações (não é aconselhável ter muitos arquivos monitorados em um diretório).
`monitorExtensions` é usado para configurar quais extensões de arquivos no diretório `monitorDir` devem ser monitoradas.
Quando `options.enable_file_monitor` é `true`, o monitoramento de atualizações de arquivo é ativado (por padrão, no sistema Linux, o monitoramento de arquivo está ativado ao executar em modo de depuração).
Quando `options.enable_memory_monitor` é `true`, o monitoramento do uso de memória é ativado (o monitoramento de uso de memória não é suportado no sistema Windows).

> **Dica**
> No sistema Windows, o monitoramento de atualizações de arquivo só é ativado ao executar `windows.bat` ou `php windows.php`.
