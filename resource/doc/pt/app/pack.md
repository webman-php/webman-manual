# Empacotamento

Por exemplo, para empacotar o aplicativo de plug-in foo:

* Defina o número da versão em `plugin/foo/config/app.php` (**importante**)
* Remova arquivos desnecessários em `plugin/foo`, especialmente os arquivos temporários de upload de teste em `plugin/foo/public`
* Remova as configurações do banco de dados e do Redis. Se o seu projeto tiver configurações de banco de dados e Redis independentes, essas configurações devem ser acionadas pelo programa de instalação na primeira vez que o aplicativo for acessado (precisa ser implementado), para que os administradores possam preenchê-las manualmente e gerá-las.
* Restaure outros arquivos que precisam ser restaurados à sua forma original
* Após concluir as operações acima, vá para o diretório `{main_project}/plugin/` e use o comando `zip -r foo.zip foo` para gerar foo.zip
