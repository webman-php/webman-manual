# Estrutura do diretório

```
plugin/
└── foo
    ├── app
    │   ├── controller
    │   │   └── IndexController.php
    │   ├── exception
    │   │   └── Handler.php
    │   ├── functions.php
    │   ├── middleware
    │   ├── model
    │   └── view
    │       └── index
    │           └── index.html
    ├── config
    │   ├── app.php
    │   ├── autoload.php
    │   ├── container.php
    │   ├── database.php
    │   ├── exception.php
    │   ├── log.php
    │   ├── middleware.php
    │   ├── process.php
    │   ├── redis.php
    │   ├── route.php
    │   ├── static.php
    │   ├── thinkorm.php
    │   ├── translation.php
    │   └── view.php
    ├── public
    └── api
```

Podemos ver que um plugin de aplicativo tem a mesma estrutura de diretório e arquivos de configuração que webman, na verdade, a experiência de desenvolvimento de um plugin é basicamente a mesma do desenvolvimento de um aplicativo comum no webman.
Os diretórios e nomes dos plugins seguem a especificação PSR4, e como os plugins são todos colocados no diretório plugin, os namespaces começam com "plugin\", por exemplo `plugin\foo\app\controller\UserController`.

## Sobre o diretório api
Cada plugin possui um diretório api e se o seu aplicativo oferecer algumas interfaces internas para serem chamadas por outros aplicativos, é necessário colocar as interfaces no diretório api.
É importante ressaltar que as interfaces mencionadas são para chamadas de funções, e não para chamadas de rede.
Por exemplo, o `plugin de e-mail` fornece uma interface `Email::send()` em `plugin/email/api/Email.php`, para ser chamada por outros aplicativos para enviar e-mails.
Além disso, `plugin/email/api/Install.php` é gerado automaticamente e é usado para que o mercado de plugins webman-admin execute operações de instalação ou desinstalação.
