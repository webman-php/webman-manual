# Segurança

## Usuário de Execução
É recomendável definir o usuário de execução como um usuário com permissões mais baixas, como o usuário de execução do nginx. O usuário de execução é configurado nas configurações `user` e `group` em `config/server.php`. Da mesma forma, o usuário personalizado para processos é especificado em `config/process.php` usando `user` e `group`. É importante observar que o processo de monitoramento não deve ter um usuário de execução definido, pois ele requer permissões elevadas para funcionar corretamente.

## Padrão do Controlador
Apenas arquivos de controladores devem ser colocados no diretório `controller` ou em seus subdiretórios, e é proibido colocar outros arquivos de classe. Caso contrário, quando o [sufixo do controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) não estiver ativado, arquivos de classe podem ser acessados de forma não autorizada por meio de URLs, resultando em consequências imprevisíveis. Por exemplo, `app/controller/model/User.php` é na verdade uma classe de modelo, mas foi erroneamente colocada no diretório `controller`. Sem o [sufixo do controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) ativado, isso poderia permitir que os usuários acessem arbitrariamente os métodos de `User.php` através de URLs como `/model/user/xxx`. Para evitar essa situação, é altamente recomendável usar o [sufixo do controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) para identificar claramente quais arquivos são de controladores.

## Filtro XSS
Por questões de generalidade, o webman não realiza a filtragem de XSS nas solicitações. O webman recomenda enfaticamente realizar a filtragem de XSS durante a renderização, em vez de antes de inserir no banco de dados. Além disso, os templates como twig, blade, think-tmplate realizam automaticamente a filtragem de XSS, dispensando assim a necessidade de filtragem manual.

> **Dica**
> Se você realizar a filtragem de XSS antes de inserir no banco de dados, é provável que isso cause incompatibilidades com alguns plugins do aplicativo.

## Prevenção de Injeção de SQL
Para prevenir a injeção de SQL, é altamente recomendável utilizar ORM, como [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) e [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), e evitar ao máximo criar consultas SQL manualmente.

## Proxy do nginx
Quando seu aplicativo precisa ser exposto a usuários da Internet, é altamente recomendável adicionar um proxy do nginx antes do webman para filtrar algumas solicitações HTTP não autorizadas e aumentar a segurança. Para mais informações, consulte [Proxy do nginx](nginx-proxy.md).
