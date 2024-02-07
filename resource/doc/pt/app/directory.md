# Estrutura de diretório

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

Nós vemos uma estrutura de diretório e arquivos de configuração semelhantes ao webman em um plugin. Na verdade, a experiência de desenvolvimento de um aplicativo comum do webman é basicamente a mesma que a de desenvolvimento de um plugin.
O diretório e o nome do plugin seguem a especificação PSR4. Visto que os plugins são todos armazenados no diretório de plugin, os namespaces começam com "plugin\", por exemplo, `plugin\foo\app\controller\UserController`.

## Sobre o diretório api
Cada plugin tem um diretório api. Se o seu aplicativo fornece algumas interfaces internas para serem chamadas por outros aplicativos, é necessário colocar as interfaces no diretório api.
É importante ressaltar que as interfaces mencionadas são interfaces de chamadas de função, não de chamadas de rede.
Por exemplo, o plugin de `email` fornece uma interface `Email::send()` em `plugin/email/api/Email.php` para que outros aplicativos possam enviar e-mails.
Além disso, `plugin/email/api/Install.php` é gerado automaticamente e é usado para permitir que o mercado de plugins do webman-admin execute operações de instalação ou desinstalação.
