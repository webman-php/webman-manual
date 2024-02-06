# Estrutura de Diretórios
```
.
├── app                           Diretório de aplicativos
│   ├── controller                Diretório de controladores
│   ├── model                     Diretório de modelos
│   ├── view                      Diretório de visualizações
│   ├── middleware                Diretório de middlewares
│   │   └── StaticFile.php        Middleware de arquivos estáticos incorporado
|   └── functions.php             Funções personalizadas de negócios são escritas neste arquivo
|
├── config                        Diretório de configuração
│   ├── app.php                   Configuração de aplicativo
│   ├── autoload.php              Os arquivos configurados aqui serão carregados automaticamente
│   ├── bootstrap.php             Configuração de retorno de chamada executada em onWorkerStart durante a inicialização do processo
│   ├── container.php             Configuração do container
│   ├── dependence.php            Configuração de dependências do container
│   ├── database.php              Configuração de banco de dados
│   ├── exception.php             Configuração de exceções
│   ├── log.php                   Configuração de log
│   ├── middleware.php            Configuração de middlewares
│   ├── process.php               Configuração de processos personalizados
│   ├── redis.php                 Configuração do Redis
│   ├── route.php                 Configuração de roteamento
│   ├── server.php                Configuração de servidor, portas, número de processos, etc.
│   ├── view.php                  Configuração de visualização
│   ├── static.php                Configuração de alternância de arquivos estáticos e middleware de arquivos estáticos
│   ├── translation.php           Configuração de idiomas múltiplos
│   └── session.php               Configuração de sessão
├── public                        Diretório de recursos estáticos
├── process                       Diretório de processos personalizados
├── runtime                       Diretório de tempo de execução do aplicativo, requer permissão de gravação
├── start.php                     Arquivo de inicialização do serviço
├── vendor                        Diretório de bibliotecas de terceiros instaladas pelo Composer
└── support                       Adaptação de bibliotecas (incluindo bibliotecas de terceiros)
    ├── Request.php               Classe de solicitação
    ├── Response.php              Classe de resposta
    ├── Plugin.php                Script de instalação e desinstalação de plugin
    ├── helpers.php               Funções auxiliares (para funções personalizadas de negócios, escreva-as em app/functions.php)
    └── bootstrap.php             Script de inicialização após a inicialização do processo
```
