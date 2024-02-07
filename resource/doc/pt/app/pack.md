# Empacotamento

Por exemplo, empacotando o plugin de aplicativo "foo":

* Defina o número da versão em `plugin/foo/config/app.php` (**importante**).
* Remova os arquivos desnecessários em `plugin/foo`, especialmente os arquivos temporários de teste de upload em `plugin/foo/public`.
* Remova as configurações do banco de dados e do Redis. Se o seu projeto tiver configurações independentes para o banco de dados e o Redis, essas configurações devem ser acionadas pelo assistente de instalação na primeira vez que o aplicativo for acessado (você mesmo deve implementar isso), permitindo que o administrador as preencha manualmente e as gere.
* Restaure outros arquivos que precisam ser restaurados à sua forma original.
* Após concluir as operações acima, vá para o diretório `{diretório principal do projeto}/plugin/`, e use o comando `zip -r foo.zip foo` para gerar o arquivo foo.zip.
