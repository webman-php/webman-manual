# Segurança

## Usuário de Execução
É recomendado configurar o usuário de execução como um usuário com permissões mais baixas, por exemplo, o mesmo usuário que executa o nginx. O usuário de execução é configurado nos campos `user` e `group` no arquivo `config/server.php`. Da mesma forma, o usuário e o grupo para processos personalizados são especificados no arquivo `config/process.php`. É importante observar que o processo de monitoramento não deve ter um usuário de execução definido, pois ele precisa de permissões elevadas para funcionar corretamente.

## Padrão de Controlador
Apenas arquivos de controladores devem ser colocados no diretório `controller` ou em seus subdiretórios. É proibido colocar outros tipos de arquivos nesse diretório, caso contrário, se o [sufixo do controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) não estiver ativado, arquivos de classes podem ser acessados de forma inadequada por URLs, resultando em consequências imprevisíveis. Por exemplo, `app/controller/model/User.php` pode ser um arquivo de classe Model, mas se for erroneamente colocado no diretório `controller` e o [sufixo do controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) não estiver ativado, os usuários podem acessar qualquer método em `User.php` através de URLs como `/model/user/xxx`. Para evitar completamente essa situação, é altamente recomendável usar o [sufixo do controlador](https://www.workerman.net/doc/webman/controller.html#%E6%8E%A7%E5%88%B6%E5%99%A8%E5%90%8E%E7%BC%80) para identificar claramente quais arquivos são de controladores.

## Filtro XSS
Por uma questão de generalidade, o webman não realiza a filtragem de XSS nas requisições. O webman recomenda fortemente a realização da filtragem de XSS durante a renderização, em vez de antes de inserir no banco de dados. Além disso, os modelos como twig, blade, think-template etc., executam automaticamente a filtragem de XSS, portanto, não é necessário realizar manualmente. 

> **Dica**
> Realizar a filtragem de XSS antes de inserir no banco de dados pode causar problemas de incompatibilidade com alguns plugins do aplicativo.

## Prevenção de Injeção de SQL
Para prevenir a injeção de SQL, é altamente recomendado utilizar um ORM, como [illuminate/database](https://www.workerman.net/doc/webman/db/tutorial.html) ou [think-orm](https://www.workerman.net/doc/webman/db/thinkorm.html), em vez de montar SQL manualmente.

## Proxy Nginx
Quando o seu aplicativo precisa ser exposto a usuários externos, é altamente recomendado adicionar um proxy Nginx antes do webman. Isso pode filtrar algumas requisições HTTP ilegais e aumentar a segurança. Para mais informações, consulte [proxy Nginx](nginx-proxy.md).
