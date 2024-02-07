# Estrutura de diretórios
```
.
├── app                           Diretório de aplicativos
│   ├── controller                Diretório de controladores
│   ├── model                     Diretório de modelos
│   ├── view                      Diretório de visualizações
│   ├── middleware                Diretório de middleware
│   │   └── StaticFile.php        Middleware de arquivos estáticos incorporado
|   └── functions.php             Funções de negócio personalizadas são escritas neste arquivo
|
├── config                        Diretório de configuração
│   ├── app.php                   Configuração do aplicativo
│   ├── autoload.php              Os arquivos configurados aqui serão carregados automaticamente
│   ├── bootstrap.php             Configuração de retorno de chamada executada em onWorkerStart durante o início do processo
│   ├── container.php             Configuração do contêiner
│   ├── dependence.php            Configuração de dependências do contêiner
│   ├── database.php              Configuração do banco de dados
│   ├── exception.php             Configuração de exceção
│   ├── log.php                   Configuração de log
│   ├── middleware.php            Configuração de middleware
│   ├── process.php               Configuração de processos personalizados
│   ├── redis.php                 Configuração do Redis
│   ├── route.php                 Configuração de rota
│   ├── server.php                Configuração do servidor (porta, número de processos, etc.)
│   ├── view.php                  Configuração de visualização
│   ├── static.php                Interruptor de arquivo estático e configuração de middleware de arquivo estático
│   ├── translation.php           Configuração de idioma
│   └── session.php               Configuração de sessão
├── public                        Diretório de recursos estáticos
├── process                       Diretório de processos personalizados
├── runtime                       Diretório de tempo de execução do aplicativo, requer permissão de escrita
├── start.php                     Arquivo de inicialização do serviço
├── vendor                        Diretório de bibliotecas de terceiros instaladas pelo Composer
└── support                       Adaptação de biblioteca (incluindo bibliotecas de terceiros)
    ├── Request.php               Classe de solicitação
    ├── Response.php              Classe de resposta
    ├── Plugin.php                Script de instalação/desinstalação de plugin
    ├── helpers.php               Funções auxiliares (para funções de negócio personalizadas, por favor, escreva em app/functions.php)
    └── bootstrap.php             Script de inicialização pós-inicialização do processo
```
